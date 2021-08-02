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

use Auth;
use Carbon;
use DB;
use File;
use View;

class ComplimentTxController extends Controller
{
    public function __construct()
    {
        View::share('sidebar', 'compliment_tx');
    }

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
    
    private function setTx($tx, $input){
        if($tx->invoice_id==""){
            $tx->invoice_id = ComplimentTx::genInvoiceNo();
        }
        $tx->employee_id = Auth::user()->id;
        $tx->customer_id = $input['customer_id'];
        $tx->seller_id = $input['seller_id'];
        $tx->total = $input['total'];
        $tx->tax = $input['tax'];
        $tx->grand_total = $input['grand_total'];
        $tx->status = $input['status'];
        $tx->total_paid = $input['total_paid'];
        $tx->remainder = $input['remainder'];

        return $tx;
    }

    private function setDetailTx($detail, $tx, $goods_id, $input, $key){
        $detail->compliment_tx_id = $tx->id;
        $detail->goods_id = $goods_id;
        $detail->qty = $input['qty'][$key];
        $detail->unit_id = $input['unit_id'][$key];
        $detail->price = $input['price'][$key];
        $detail->disc = $input['disc'][$key] ?: 0;
        $detail->sub_total = $input['sub_total'][$key];
        $detail->created_at = Carbon::now()->format('Y-m-d H:i:s');
        $detail->updated_at = Carbon::now()->format('Y-m-d H:i:s');

        return $detail;
    }

    private function setGoodsLog($detail, $latest_log, $customer, $status="OUT", $note=""){
        $log = new GoodsLog();
        $log->goods_id = $detail->goods_id;
        $log->status = $status;
        $log->date = Carbon::now()->format('Y-m-d H:i:s');
        $log->qty = $detail->qty;
        if ($status=="OUT") {
            $log->post_amount = ($latest_log->post_amount ?? 0) - $detail->qty;
        } else {
            $log->post_amount = ($latest_log->post_amount ?? 0) + $detail->qty;
        }
        $log->price = $detail->sub_total;
        $log->source = $customer->name;
        $log->note = $note;
        $log->logable_id = $customer->id;
        $log->logable_type = 'App\Customer';
        $log->created_at = Carbon::now()->format('Y-m-d H:i:s');
        $log->updated_at = Carbon::now()->format('Y-m-d H:i:s');
        
        return $log;
    }

    private function setPaymentLog($tx, $file, $input, $key){
        $log_payment = new TxPaymentLog();
        $log_payment->tx_id = $tx->id;
        $log_payment->logable_id = $tx->id;
        $log_payment->logable_type = 'App\ComplimentTx';
        $log_payment->payment_type_id = $input['payment_id'][$key];
        $log_payment->paid_amount = $input['paid_amount'][$key];
        $log_payment->tx_type = 0;
        $log_payment->pic = Auth::user()->id;
        $log_payment->payment_proof = "";
        //upload
        if($file !== null && array_key_exists($key, $file)){
            $log_payment->payment_proof = $tx->invoice_id . "_proof_". ($key+1) ."." . $file[$key]->getClientOriginalExtension();
            $log_payment->uploadPhoto($file[$key], $log_payment->payment_proof);
            $log_payment_proofs[] = $log_payment;
        }
        $log_payment->created_at = Carbon::now()->format('Y-m-d H:i:s');

        return $log_payment;
    }

    public function store(Request $request)
    {
        $notif = [];
        $input = $request->all();
        $tx =  $this->setTx(new ComplimentTx, $input);

        $log_payment_proofs = [];
        try{
            DB::beginTransaction();
            $tx->save();

            $payment_type = [];
            $tx_details = [];
            $logs = [];
            foreach($input['goods_id'] as $key => $goods_id){
                $detail = $this->setDetailTx(new DetComplTx, $tx, $goods_id, $input, $key);
                $tx_details[] = $detail->attributesToArray();

                $latest_log = GoodsLog::getLatestLog($detail->goods_id);
                $customer = Customer::find($tx->customer_id);
                $log = $this->setGoodsLog($detail, $latest_log, $customer, "OUT");
                $logs[] = $log->attributesToArray();

                //update stock
                Goods::where("id", $detail->goods_id)->update(["amount" => DB::raw("amount - " . $detail->qty)]);
            }
            DetComplTx::insert($tx_details);
            GoodsLog::insert($logs);

            //insert log payment if tx status is not unpaid
            if($tx->status!='unpaid' && array_key_exists('payment_id', $input)){
                
                $log_payments = [];
                foreach($input['payment_id'] as $key => $payment_id){
                    $log_payment = $this->setPaymentLog($tx, $request->file('payment_proof'), $input, $key);
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
        $tx =  $this->setTx($tx, $input);

        $log_payment_proofs = [];
        try{
            DB::beginTransaction();
            $tx->save();

            $payment_type = array_filter(explode(", ", $tx->payment_type));
            $tx_details = [];
            $logs = [];
            
            //remove deleted item cart
            $tx_detail_ids = array_key_exists('tx_detail_id', $input) ? $input['tx_detail_id'] : [];
            $remove_detail_ids = array_diff( $tx->details()->pluck('id')->toArray(), $tx_detail_ids );
            if (count($remove_detail_ids) > 0) {
                foreach($tx->details as $key => $detail){
                    if(!in_array($detail->id, $remove_detail_ids)) continue; //skip if details not deleted

                    //restore stock
                    Goods::where("id", $detail->goods_id)->update(["amount" => DB::raw("amount + " . $detail->qty)]);
                    
                    //update log (add stock)
                    $latest_log = GoodsLog::getLatestLog($detail->goods_id);
                    $customer = Customer::find($tx->customer_id);
                    $log = $this->setGoodsLog($detail, $latest_log, $customer, "IN", "Update Transaction");
                    $logs[] = $log->attributesToArray();
                }
                //destroy deleted item cart in detail
                DetComplTx::destroy($remove_detail_ids);
                GoodsLog::insert($logs);
            }

            $logs = [];
            $input['goods_id'] = array_filter($input['goods_id']); //get new item cart only
            foreach($input['goods_id'] as $key => $goods_id){
                $detail = $this->setDetailTx(new DetComplTx, $tx, $goods_id, $input, $key);
                $tx_details[] = $detail->attributesToArray();

                $latest_log = GoodsLog::getLatestLog($detail->goods_id);
                $customer = Customer::find($tx->customer_id);
                $log = $this->setGoodsLog($detail, $latest_log, $customer, "OUT");
                $logs[] = $log->attributesToArray();
                
                //update stock
                Goods::where("id", $detail->goods_id)->update(["amount" => DB::raw("amount - " . $detail->qty)]);
            }
            DetComplTx::insert($tx_details);
            GoodsLog::insert($logs);

            //insert log payment if tx status is not unpaid
            if($tx->status!='unpaid' && array_key_exists('payment_id', $input)){
                
                $log_payments = [];
                foreach($input['payment_id'] as $key => $payment_id){
                    $log_payment = $this->setPaymentLog($tx, $request->file('payment_proof'), $input, $key);
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
