<?php

namespace App\Http\Controllers;

use App\Customer;
use App\CompanyProfile;
use App\ComplimentTx;
use App\DetComplTx;
use App\Goods;
use App\GoodsLog;
use App\PaymentType;
use App\Seller;
use App\TxPaymentLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

use DB;
use Carbon;

class ComplimentTxController extends Controller
{
    public function index()
    {
        $transactions = ComplimentTx::with('customer','employee')->get();
        return view('transaction.compliment.index', compact('transactions'));
    }
    
    public function invoice(ComplimentTx $tx){
        $comp_profile = CompanyProfile::find(1);
        $items = DetComplTx::with('goods','unit')->where('compliment_tx_id', '=', $tx->id)->get();
        $type = "compliment";
        return view('transaction.invoice', compact('tx', 'items', 'comp_profile', 'type'));
    }

    public function create(){
        $goods = Goods::with('unit')->get();
        $customers = Customer::get();
        $sellers = Seller::get();
        $payment_types = PaymentType::get();
        return view('transaction.compliment.create', compact('goods', 'customers', 'sellers', 'payment_types'));
    }

    public function store(Request $request)
    {
        $notif = [];
        $input = $request->all();

        $tx = new ComplimentTx;
        $tx->invoice_id = ComplimentTx::genInvoiceNo();
        $tx->employee_id = Auth::user()->id;
        $tx->customer_id = $input['customer_id'];
        $tx->seller_id = $input['seller_id'];
        $tx->total = $input['total'];
        $tx->tax = $input['tax'];
        $tx->grand_total = $input['grand_total'];
        $tx->status = $input['status'];
        $tx->total_paid = $input['total_paid'];
        $tx->remainder = $input['remainder'];

        $log_payment_proofs = [];
        try{
            DB::beginTransaction();

            $tx->save();
            $payment_type = [];

            $tx_details = [];
            $logs = [];
            foreach($input['goods_id'] as $key => $goods_id){
                $detail = new DetComplTx;
                $detail->compliment_tx_id = $tx->id;
                $detail->goods_id = $goods_id;
                $detail->qty = $input['qty'][$key];
                $detail->unit_id = $input['unit_id'][$key];
                $detail->price = $input['price'][$key];
                $detail->disc = $input['disc'][$key] ?: 0;
                $detail->sub_total = $input['sub_total'][$key];
                $detail->created_at = Carbon::now()->format('Y-m-d H:i:s');
                $detail->updated_at = Carbon::now()->format('Y-m-d H:i:s');
                $tx_details[] = $detail->attributesToArray();
                
                if($tx->status=='paid' || $tx->status=='down payment'){
                    $customer = Customer::find($tx->customer_id);
                    
                    Goods::where("id", $detail->goods_id)->update(["amount" => DB::raw("amount - " . $detail->qty)]);

                    $log_before = GoodsLog::where("goods_id", $detail->goods_id)->orderBy('id', 'DESC')->first();
                    $log = new GoodsLog();
                    $log->goods_id = $detail->goods_id;
                    $log->status = "OUT";
                    $log->date = Carbon::now()->format('Y-m-d H:i:s');
                    $log->qty = $detail->qty;
                    $log->post_amount = ($log_before->post_amount ?? 0) - $detail->qty;
                    $log->price = $detail->sub_total;
                    $log->source = $customer->name;
                    $log->created_at = Carbon::now()->format('Y-m-d H:i:s');
                    $log->updated_at = Carbon::now()->format('Y-m-d H:i:s');
                    $logs[] = $log->attributesToArray();
                }
            }
            DetComplTx::insert($tx_details);
            if($tx->status=='paid' || $tx->status=='down payment'){
                GoodsLog::insert($logs);
            }

            //insert log payment if tx status is not unpaid
            if($tx->status!='unpaid'){
                $log_payments = [];
                foreach($input['payment_id'] as $key => $payment_id){
                    $log_payment = new TxPaymentLog();
                    $log_payment->tx_id = $tx->id;
                    $log_payment->logable_id = $tx->id;
                    $log_payment->logable_type = 'App\ComplimentTx';
                    $log_payment->payment_type_id = $payment_id;
                    $log_payment->paid_amount = $input['paid_amount'][$key];
                    $log_payment->tx_type = 0;
                    $log_payment->pic = Auth::user()->id;
                    $log_payment->payment_proof = "";
                    //upload
                    $file = $request->file('payment_proof');
                    if($file !== null && array_key_exists($key, $file)){
                        $log_payment->payment_proof = $tx->invoice_id . "_proof_". ($key+1) ."." . $file[$key]->getClientOriginalExtension();
                        $log_payment->uploadPhoto($file[$key], $log_payment->payment_proof);
                        $log_payment_proofs[] = $log_payment;
                    }
                    $log_payment->created_at = Carbon::now()->format('Y-m-d H:i:s');
                    $log_payments[] = $log_payment->attributesToArray();
                    $payment_type[] = $input['payment_name'][$key];
                }
                TxPaymentLog::insert($log_payments);
                //update payment date
                if(count($log_payments)){
                    $tx->payment_date =  Carbon::now()->format('Y-m-d H:i:s');
                    $tx->payment_type = join(", ", array_unique($payment_type));
                    $tx->save();
                }
            }
       
            DB::commit();
            $notif = [
                "type" => "success",
                "message" => "Surat Jalan has been created!"
            ];
        }catch(\Exception $e){
            dd($e);
            DB::rollback();
            foreach($log_payment_proofs as $log_payment){
                File::delete($log_payment[$key]->getPaymentProof());
            }
            $notif = [
                "type" => "failed",
                "message" => "Failed to create Surat Jalan transaction!"
            ];
        }

        return redirect()->route('tx.compliment.index')->with($notif['type'], $notif['message']);
    }

    public function draft(ComplimentTx $tx){
        $details = DetComplTx::with('goods','unit')->where('compliment_tx_id', '=', $tx->id)->get();
        $goods = Goods::with('unit')->get();
        $customers = Customer::get();
        $sellers = Seller::get();
        $payment_types = PaymentType::get();
        return view('transaction.compliment.draft', compact('tx', 'details', 'goods', 'customers', 'sellers', 'payment_types'));
    }

    public function update(Request $request, ComplimentTx $tx)
    { 
        $input = $request->all();
        
        $tx->employee_id = Auth::user()->id;
        $tx->customer_id = $input['customer_id'];
        $tx->seller_id = $input['seller_id'];
        $tx->total = $input['total'];
        $tx->tax = $input['tax'];
        $tx->grand_total = $input['grand_total'];
        $previous_status = $tx->status; //get prev tx status before update it
        $tx->status = $input['status'];
        $tx->total_paid = $input['total_paid'];
        $tx->remainder = $input['remainder'];

        $log_payment_proofs = [];
        try{
            DB::beginTransaction();

            $tx->save();
            $payment_type = explode(", ", $tx->payment_type);
            DetComplTx::where('compliment_tx_id', $tx->id)->delete();
            
            $tx_details = [];
            $logs = [];
            foreach($input['goods_id'] as $key => $goods_id){
                $detail = new DetComplTx;
                $detail->compliment_tx_id = $tx->id;
                $detail->goods_id = $goods_id;
                $detail->qty = $input['qty'][$key];
                $detail->unit_id = $input['unit_id'][$key];
                $detail->price = $input['price'][$key];
                $detail->disc = $input['disc'][$key] ?: 0;
                $detail->sub_total = $input['sub_total'][$key];
                $detail->created_at = Carbon::now()->format('Y-m-d H:i:s');
                $detail->updated_at = Carbon::now()->format('Y-m-d H:i:s');
                $tx_details[] = $detail->attributesToArray();
                
                //if previous status is unpaid and the current status != unpaid, update the products stock
                if($previous_status=='unpaid' && ($tx->status=='paid' || $tx->status=='down payment')){
                    $customer = Customer::find($tx->customer_id);
                    
                    Goods::where("id", $detail->goods_id)->update(["amount" => DB::raw("amount - " . $detail->qty)]);

                    $log_before = GoodsLog::where("goods_id", $detail->goods_id)->orderBy('id', 'DESC')->first();
                    $log = new GoodsLog();
                    $log->goods_id = $detail->goods_id;
                    $log->status = "OUT";
                    $log->date = Carbon::now()->format('Y-m-d H:i:s');
                    $log->qty = $detail->qty;
                    $log->post_amount = ($log_before->post_amount ?? 0) - $detail->qty;
                    $log->price = $detail->sub_total;
                    $log->source = $customer->name;
                    $log->created_at = Carbon::now()->format('Y-m-d H:i:s');
                    $log->updated_at = Carbon::now()->format('Y-m-d H:i:s');
                    $logs[] = $log->attributesToArray();
                }
            }
            DetComplTx::insert($tx_details);
            //if previous status is unpaid and the current status != unpaid, create product logs
            if($previous_status=='unpaid' && ($tx->status=='paid' || $tx->status=='down payment')){
                GoodsLog::insert($logs);
            }

            //insert log payment if tx status is not unpaid
            if($tx->status!='unpaid'){
                $log_payments = [];
                foreach($input['payment_id'] as $key => $payment_id){
                    $log_payment = new TxPaymentLog();
                    $log_payment->tx_id = $tx->id;
                    $log_payment->logable_id = $tx->id;
                    $log_payment->logable_type = 'App\ComplimentTx';
                    $log_payment->payment_type_id = $payment_id;
                    $log_payment->paid_amount = $input['paid_amount'][$key];
                    $log_payment->tx_type = 0;
                    $log_payment->pic = Auth::user()->id;
                    $log_payment->payment_proof = "";
                    //upload
                    $file = $request->file('payment_proof');
                    if($file !== null && array_key_exists($key, $file)){
                        $log_payment->payment_proof = $tx->invoice_id . "_proof_". ($key+1) ."." . $file[$key]->getClientOriginalExtension();
                        $log_payment->uploadPhoto($file[$key], $log_payment->payment_proof);
                        $log_payment_proofs[] = $log_payment;
                    }
                    $log_payment->created_at = Carbon::now()->format('Y-m-d H:i:s');
                    $log_payments[] = $log_payment->attributesToArray();
                    $payment_type[] = $input['payment_name'][$key];
                }
                TxPaymentLog::insert($log_payments);
                //update payment date
                if(count($log_payments)){
                    $tx->payment_date =  Carbon::now()->format('Y-m-d H:i:s');
                    $tx->payment_type = join(", ", array_unique($payment_type));
                    $tx->save();
                }
            }

            DB::commit();
            $notif = [
                "type" => "success",
                "message" => "Surat Jalan has been updated!"
            ];
        }catch(\Exception $e){
            DB::rollback();
            foreach($log_payment_proofs as $log_payment){
                File::delete($log_payment[$key]->getPaymentProof());
            }
            $notif = [
                "type" => "failed",
                "message" => "Failed to update Surat Jalan!"
            ];
            dd($e);
        }

        return redirect()->route('tx.compliment.index')->with($notif['type'], $notif['message']);
    }
    
}
