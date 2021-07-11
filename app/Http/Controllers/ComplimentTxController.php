<?php

namespace App\Http\Controllers;

use App\Client;
use App\CompanyProfile;
use App\DetComplTx;
use App\Goods;
use App\ComplimentTx;
use App\Seller;
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
        return view('transaction.invoice', compact('tx', 'items', 'comp_profile'));
    }

    public function create(){
        $goods = Goods::with('unit')->get();
        $customers = Client::get();
        $sellers = Seller::get();
        return view('transaction.compliment.create', compact('goods', 'customers', 'sellers'));
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
            foreach($input['goods_id'] as $key => $goods_id){
                $detail = new DetComplTx;
                $detail->compliment_tx_id = $tx->id;
                $detail->goods_id = $goods_id;
                $detail->qty = $input['qty'][$key];
                $detail->unit_id = $input['unit_id'][$key];
                $detail->price = $input['price'][$key];
                $detail->disc = $input['disc'][$key] ?: 0;
                $detail->sub_total = $input['sub_total'][$key];
                $tx_details[] = $detail->attributesToArray();
                
                if($tx->status=='paid'){
                    Goods::where("id", $detail['goods_id'])->update(["amount" => DB::raw("amount - " . $detail['qty'])]);
                }
            }
            DetComplTx::insert($tx_details);
            
            DB::commit();
            $notif = [
                "type" => "success",
                "message" => "Compliment transaction has been created!"
            ];
        }catch(\Exception $e){
            DB::rollback();
            if($request->file('tf_proof') != null){
                File::delete($tx->getPublicPath($tx->transfer_proof));
            }
            $notif = [
                "type" => "failed",
                "message" => "Failed to create compliment transaction!"
            ];
        }

        return redirect()->route('tx.compliment.index')->with($notif['type'], $notif['message']);
    }

    public function draft(ComplimentTx $tx){
        $details = DetComplTx::with('goods','unit')->where('compliment_tx_id', '=', $tx->id)->get();
        $goods = Goods::with('unit')->get();
        $customers = Client::get();
        $sellers = Seller::get();
        return view('transaction.compliment.draft', compact('tx', 'details', 'goods', 'customers', 'sellers'));
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
            DetComplTx::where('compliment_tx_id', $tx->id)->delete();
            
            $tx_details = [];
            foreach($input['goods_id'] as $key => $goods_id){
                $detail = new DetComplTx;
                $detail->compliment_tx_id = $tx->id;
                $detail->goods_id = $goods_id;
                $detail->qty = $input['qty'][$key];
                $detail->unit_id = $input['unit_id'][$key];
                $detail->price = $input['price'][$key];
                $detail->disc = $input['disc'][$key] ?: 0;
                $detail->sub_total = $input['sub_total'][$key];
                $tx_details[] = $detail->attributesToArray();
                
                if($tx->status=='paid'){
                    Goods::where("id", $detail['goods_id'])->update(["amount" => DB::raw("amount - " . $detail['qty'])]);
                }
            }
            DetComplTx::insert($tx_details);

            DB::commit();
            $notif = [
                "type" => "success",
                "message" => "Compliment transaction has been updated!"
            ];
        }catch(\Exception $e){
            DB::rollback();
            if($request->file('tf_proof') != null){
                File::delete($tx->getPublicPath($tx->transfer_proof));
            }
            $notif = [
                "type" => "failed",
                "message" => "Failed to update compliment transaction!"
            ];
        }

        return redirect()->route('tx.compliment.index')->with($notif['type'], $notif['message']);
    }
    
}
