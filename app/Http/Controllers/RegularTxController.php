<?php

namespace App\Http\Controllers;

use App\GoodsLog;
use App\Customer;
use App\CompanyProfile;
use App\DetRegTx;
use App\Goods;
use App\RegularTx;
use App\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

use DB;
use Carbon;

class RegularTxController extends Controller
{
    public function index()
    {
        $transactions = RegularTx::with('customer','employee')->get();
        return view('transaction.regular.index', compact('transactions'));
    }

    
    public function invoice(RegularTx $tx){
        $comp_profile = CompanyProfile::find(1);
        $items = DetRegTx::with('goods','unit')->where('regular_tx_id', '=', $tx->id)->get();
        return view('transaction.invoice', compact('tx', 'items', 'comp_profile'));
    }

    public function create(){
        $goods = Goods::with('unit')->get();
        $customers = Customer::get();
        $sellers = Seller::get();
        return view('transaction.regular.create', compact('goods', 'customers', 'sellers'));
    }

    public function store(Request $request)
    {
        $notif = [];
        $input = $request->all();
        
        $tx = new RegularTx;
        $tx->invoice_id = RegularTx::genInvoiceNo();
        $tx->employee_id = Auth::user()->id;
        $tx->customer_id = $input['customer_id'];
        $tx->seller_id = $input['seller_id'];
        $tx->total = $input['total'];
        $tx->tax = $input['tax'];
        $tx->grand_total = $input['grand_total'];
        $tx->payment_type = $input['payment_type'];
        $tx->status = $input['status'];

        if($tx->status=='paid'){
            $tx->payment_date =  Carbon::now()->format('Y-m-d H:i:s');
        }

        //upload
        if($request->file('tf_proof') != null){
            $tx->transfer_proof = $tx->invoice_id . "_transfer." . $request->file('tf_proof')->getClientOriginalExtension();
            $tx->uploadPhoto($request->file('tf_proof'), $tx->transfer_proof);
        }

        try{
            DB::beginTransaction();

            $tx->save();

            $tx_details = [];
            $logs = [];
            foreach($input['goods_id'] as $key => $goods_id){
                $detail = new DetRegTx;
                $detail->regular_tx_id = $tx->id;
                $detail->goods_id = $goods_id;
                $detail->qty = $input['qty'][$key];
                $detail->unit_id = $input['unit_id'][$key];
                $detail->price = $input['price'][$key];
                $detail->sub_total = $input['sub_total'][$key];
                $detail->created_at = Carbon::now()->format('Y-m-d H:i:s');
                $detail->updated_at = Carbon::now()->format('Y-m-d H:i:s');
                $tx_details[] = $detail->attributesToArray();

                if($tx->status=='paid'){
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
            DetRegTx::insert($tx_details);
            if($tx->status=='paid'){
                GoodsLog::insert($logs);
            }

            DB::commit();
            $notif = [
                "type" => "success",
                "message" => "Regular transaction has been created!"
            ];
        }catch(\Exception $e){
            DB::rollback();
            die($e);
            if($request->file('tf_proof') != null){
                File::delete($tx->getPublicPath($tx->transfer_proof));
            }
            $notif = [
                "type" => "failed",
                "message" => "Failed to create regular transaction!"
            ];
        }

        return redirect()->route('tx.regular.index')->with($notif['type'], $notif['message']);
    }

    public function draft(RegularTx $tx){
        $details = DetRegTx::with('goods','unit')->where('regular_tx_id', '=', $tx->id)->get();
        $goods = Goods::with('unit')->get();
        $customers = Customer::get();
        $sellers = Seller::get();
        return view('transaction.regular.draft', compact('tx', 'details', 'goods', 'customers', 'sellers'));
    }

    public function update(Request $request, RegularTx $tx)
    { 
        $input = $request->all();
        
        $tx->employee_id = Auth::user()->id;
        $tx->customer_id = $input['customer_id'];
        $tx->seller_id = $input['seller_id'];
        $tx->total = $input['total'];
        $tx->tax = $input['tax'];
        $tx->grand_total = $input['grand_total'];
        $tx->payment_type = $input['payment_type'];
        $tx->status = $input['status'];
        
        if($tx->status=='paid'){
            $tx->payment_date =  Carbon::now()->format('Y-m-d H:i:s');
        }

        //upload
        if($request->file('tf_proof') != null){
            $tx->transfer_proof = $tx->id . "_" . $tx->invoice_id . "_transfer." . $request->file('tf_proof')->getClientOriginalExtension();
            $tx->uploadPhoto($request->file('tf_proof'), $tx->transfer_proof);
        }

        try{
            DB::beginTransaction();

            $tx->save();
            DetRegTx::where('regular_tx_id', $tx->id)->delete();
            
            $tx_details = [];
            $logs = [];
            foreach($input['goods_id'] as $key => $goods_id){
                $detail = new DetRegTx;
                $detail->regular_tx_id = $tx->id;
                $detail->goods_id = $goods_id;
                $detail->qty = $input['qty'][$key];
                $detail->unit_id = $input['unit_id'][$key];
                $detail->price = $input['price'][$key];
                $detail->sub_total = $input['sub_total'][$key];
                $detail->created_at = Carbon::now()->format('Y-m-d H:i:s');
                $detail->updated_at = Carbon::now()->format('Y-m-d H:i:s');
                $tx_details[] = $detail->attributesToArray();
                
                if($tx->status=='paid'){
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
            DetRegTx::insert($tx_details);
            if($tx->status=='paid'){
                GoodsLog::insert($logs);
            }

            DB::commit();
            $notif = [
                "type" => "success",
                "message" => "Regular transaction has been updated!"
            ];
        }catch(\Exception $e){
            DB::rollback();
            if($request->file('tf_proof') != null){
                File::delete($tx->getPublicPath($tx->transfer_proof));
            }
            $notif = [
                "type" => "failed",
                "message" => "Failed to update regular transaction!"
            ];
        }

        return redirect()->route('tx.regular.index')->with($notif['type'], $notif['message']);
    }
    
}
