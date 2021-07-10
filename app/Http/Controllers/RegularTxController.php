<?php

namespace App\Http\Controllers;

use App\Company;
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
        $customers = Company::get();
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

        if($input['status']=='paid'){
            $tx->payment_date =  Carbon::now()->format('Y-m-d H:i:s');
        }

        //upload
        if($request->file('tf_proof') != null){
            $tx->transfer_proof = $tx->invoice_id . "_transfer." . $request->file('tf_proof')->getClientOriginalExtension();
            $tx->uploadPhoto($request->file('tf_proof'), $tx->transfer_proof);
        }

        $tx_details = [];
        foreach($input['goods_id'] as $key => $goods_id){
            $detail = new DetRegTx;
            $detail->goods_id = $goods_id;
            $detail->qty = $input['qty'][$key];
            $detail->unit_id = $input['unit_id'][$key];
            $detail->price = $input['price'][$key];
            $detail->sub_total = $input['sub_total'][$key];
            $tx_details[] = $detail;
        }
        try{
            DB::beginTransaction();

            $tx->save();

            foreach($tx_details as $detail) {
                $detail->regular_tx_id = $tx->id;
                $detail->save();
            }
            DB::commit();
            $notif = [
                "type" => "success",
                "message" => "Regular transaction has been created!"
            ];
        }catch(\Exception $e){
            DB::rollback();
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
        $customers = Company::get();
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
        
        if($input['status']=='paid'){
            $tx->payment_date =  Carbon::now()->format('Y-m-d H:i:s');
        }

        //upload
        if($request->file('tf_proof') != null){
            $tx->transfer_proof = $tx->id . "_" . $tx->invoice_id . "_transfer." . $request->file('tf_proof')->getClientOriginalExtension();
            $tx->uploadPhoto($request->file('tf_proof'), $tx->transfer_proof);
        }

        $tx_details = [];
        foreach($input['goods_id'] as $key => $goods_id){
            $detail = new DetRegTx;
            $detail->goods_id = $goods_id;
            $detail->qty = $input['qty'][$key];
            $detail->unit_id = $input['unit_id'][$key];
            $detail->price = $input['price'][$key];
            $detail->sub_total = $input['sub_total'][$key];
            $tx_details[] = $detail;
        }
        try{
            DB::beginTransaction();

            $tx->save();
            DetRegTx::where('regular_tx_id', $tx->id)->delete();
            foreach($tx_details as $detail) {
                $detail->regular_tx_id = $tx->id;
                $detail->save();
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
