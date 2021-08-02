<?php

namespace App\Http\Controllers;

use App\Goods;
use App\GoodsLog;
use App\CompanyProfile;
use App\DetReceiving;
use App\Receiving;
use App\Supplier;
use App\Unit;
use Illuminate\Http\Request;

use Auth;
use Carbon;
use DB;
use File;
use View;

class ReceivingController extends Controller
{
    public function __construct()
    {
        View::share('sidebar', 'receiving');
    }

    public function index()
    {
        $receivings = Receiving::with('supplier','employee')->get();
        return view('receiving.index', compact('receivings'));
    }

    
    public function invoice(Receiving $receiving){
        $comp_profile = CompanyProfile::find(1);
        $items = DetReceiving::with('goods','unit')->where('receiving_id', '=', $receiving->id)->get();
        return view('receiving.invoice', compact('receiving', 'items', 'comp_profile'));
    }

    public function create(){
        $goods = Goods::with('unit')->get();
        $suppliers = Supplier::get();
        $units = Unit::get();
        return view('receiving.create', compact('goods', 'suppliers', 'units'));
    }

    public function store(Request $request)
    {
        $notif = [];
        $input = $request->all();
        
        $receiving = new Receiving;
        $receiving->invoice_id = $input['invoice_id'];
        $receiving->employee_id = Auth::user()->id;
        $receiving->supplier_id = $input['supplier_id'];
        $receiving->grand_total = $input['grand_total'];
        $receiving->receiving_date = $input['receiving_date'];

        try{
            DB::beginTransaction();

            $receiving->save();
            DetReceiving::where('receiving_id', $receiving->id)->delete();
            
            $receiving_details = [];
            foreach($input['goods_id'] as $key => $goods_id){
                $detail = new DetReceiving;
                $detail->receiving_id = $receiving->id;
                $detail->goods_id = $goods_id;
                $detail->qty = $input['qty'][$key];
                $detail->unit_id = $input['unit_id'][$key];
                $detail->price = $input['price'][$key];
                $detail->sub_total = $input['sub_total'][$key];
                $detail->created_at = Carbon::now()->format('Y-m-d H:i:s');
                $detail->updated_at = Carbon::now()->format('Y-m-d H:i:s');
                $receiving_details[] = $detail->attributesToArray();
                
                $supplier = Supplier::find($receiving->supplier_id);
                $latest_log = GoodsLog::getLatestLog($detail->goods_id);
                $log = new GoodsLog();
                $log->goods_id = $detail->goods_id;
                $log->status = "IN";
                $log->date = Carbon::now()->format('Y-m-d H:i:s');
                $log->qty = $detail->qty;
                $log->post_amount = ($latest_log->post_amount ?? 0) + $detail->qty;
                $log->price = $detail->sub_total;
                $log->source = $supplier->name;
                $log->logable_id = $supplier->id;
                $log->logable_type = 'App\Supplier';
                $log->created_at = Carbon::now()->format('Y-m-d H:i:s');
                $log->updated_at = Carbon::now()->format('Y-m-d H:i:s');
                $logs[] = $log->attributesToArray();
            }
            DetReceiving::insert($receiving_details);
            GoodsLog::insert($logs);

            DB::commit();
            $notif = [
                "type" => "success",
                "message" => "Receiving has been created!"
            ];
        }catch(\Exception $e){
            DB::rollback();
            die($e);
            $notif = [
                "type" => "failed",
                "message" => "Failed to create receiving!"
            ];
        }

        return redirect()->route('receiving.index')->with($notif['type'], $notif['message']);
    }

    public function edit(Receiving $receiving){
        $details = DetReceiving::with('goods','unit')->where('receiving_id', '=', $receiving->id)->get();
        $goods = Goods::with('unit')->get();
        $suppliers = Supplier::get();
        $units = Unit::get();
        return view('receiving.edit', compact('receiving', 'details', 'goods', 'suppliers', 'units'));
    }

    public function update(Request $request, Receiving $receiving)
    {
        $notif = [];
        $input = $request->all();
        
        $receiving->invoice_id = $input['invoice_id'];
        $receiving->employee_id = Auth::user()->id;
        $receiving->supplier_id = $input['supplier_id'];
        $receiving->grand_total = $input['grand_total'];
        $receiving->receiving_date = $input['receiving_date'];

        try{
            DB::beginTransaction();
            $supplier = Supplier::find($receiving->supplier_id);

            $receiving->save();
            $details = $receiving->details;
            $logs = [];
            
            foreach($details as $key => $detail){ //log previous goods as out
                $latest_log = GoodsLog::getLatestLog($detail->goods_id);
                $log = new GoodsLog();
                $log->goods_id = $detail->goods_id;
                $log->status = "OUT";
                $log->date = Carbon::now()->format('Y-m-d H:i:s');
                $log->qty = $detail->qty;
                $log->post_amount = ($latest_log->post_amount ?? 0) - $detail->qty;
                $log->price = $detail->sub_total;
                $log->source = $supplier->name;
                $log->note = "Delete receiving";
                $log->created_at = Carbon::now()->format('Y-m-d H:i:s');
                $log->updated_at = Carbon::now()->format('Y-m-d H:i:s');
                $logs[] = $log->attributesToArray();
            }
            DetReceiving::where('receiving_id', $receiving->id)->delete();
            GoodsLog::insert($logs);
            
            $receiving_details = [];
            $logs = [];
            foreach($input['goods_id'] as $key => $goods_id){
                $detail = new DetReceiving;
                $detail->receiving_id = $receiving->id;
                $detail->goods_id = $goods_id;
                $detail->qty = $input['qty'][$key];
                $detail->unit_id = $input['unit_id'][$key];
                $detail->price = $input['price'][$key];
                $detail->sub_total = $input['sub_total'][$key];
                $detail->created_at = Carbon::now()->format('Y-m-d H:i:s');
                $detail->updated_at = Carbon::now()->format('Y-m-d H:i:s');
                $receiving_details[] = $detail->attributesToArray();

                $latest_log = GoodsLog::getLatestLog($detail->goods_id);
                $log = new GoodsLog();
                $log->goods_id = $detail->goods_id;
                $log->status = "IN";
                $log->date = Carbon::now()->format('Y-m-d H:i:s');
                $log->qty = $detail->qty;
                $log->post_amount = ($latest_log->post_amount ?? 0) + $detail->qty;
                $log->price = $detail->sub_total;
                $log->source = $supplier->name;
                $log->logable_id = $supplier->id;
                $log->logable_type = 'App\Supplier';
                $log->created_at = Carbon::now()->format('Y-m-d H:i:s');
                $log->updated_at = Carbon::now()->format('Y-m-d H:i:s');
                $logs[] = $log->attributesToArray();
            }
            DetReceiving::insert($receiving_details);
            GoodsLog::insert($logs);

            DB::commit();
            $notif = [
                "type" => "success",
                "message" => "Receiving has been updated!"
            ];
        }catch(\Exception $e){
            DB::rollback();
            die($e);
            $notif = [
                "type" => "failed",
                "message" => "Failed to update receiving!"
            ];
        }

        return redirect()->route('receiving.index')->with($notif['type'], $notif['message']);
    }

    public function destroy(Receiving $receiving)
    {
        try{
            DB::beginTransaction();
            $supplier = Supplier::find($receiving->supplier_id);

            $details = $receiving->details;
            $logs = [];
            foreach($details as $key => $detail){ //log previous goods as out
                $latest_log = GoodsLog::getLatestLog($detail->goods_id);
                $log = new GoodsLog();
                $log->goods_id = $detail->goods_id;
                $log->status = "OUT";
                $log->date = Carbon::now()->format('Y-m-d H:i:s');
                $log->qty = $detail->qty;
                $log->post_amount = ($latest_log->post_amount ?? 0) - $detail->qty;
                $log->price = $detail->sub_total;
                $log->source = $supplier->name;
                $log->logable_id = $supplier->id;
                $log->logable_type = 'App\Supplier';
                $log->note = "Delete receiving";
                $log->created_at = Carbon::now()->format('Y-m-d H:i:s');
                $log->updated_at = Carbon::now()->format('Y-m-d H:i:s');
                $logs[] = $log->attributesToArray();
            }
            DetReceiving::where('receiving_id', $receiving->id)->delete();
            GoodsLog::insert($logs);
            $receiving->delete();
        
            DB::commit();
            $notif = [
                "type" => "success",
                "message" => "Receiving has been deleted!"
            ];
        }catch(\Exception $e){
            DB::rollback();
            die($e);
            $notif = [
                "type" => "failed",
                "message" => "Failed to delete receiving!"
            ];
        }
        return redirect()->route('receiving.index')->with('success', 'Receiving data has been deleted!');
    }
}
