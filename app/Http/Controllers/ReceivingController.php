<?php

namespace App\Http\Controllers;

use App\Supplier;
use App\CompanyProfile;
use App\DetReceiving;
use App\Goods;
use App\Receiving;
use App\Seller;
use App\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

use DB;
use Carbon;

class ReceivingController extends Controller
{
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

        $receiving_details = [];
        foreach($input['goods_id'] as $key => $goods_id){
            $detail = new DetReceiving;
            $detail->goods_id = $goods_id;
            $detail->qty = $input['qty'][$key];
            $detail->unit_id = $input['unit_id'][$key];
            $detail->price = $input['price'][$key];
            $detail->sub_total = $input['sub_total'][$key];
            $receiving_details[] = $detail;
        }
        try{
            DB::beginTransaction();

            $receiving->save();

            foreach($receiving_details as $detail) {
                $detail->receiving_id = $receiving->id;
                $detail->save();
            }
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

        $receiving_details = [];
        foreach($input['goods_id'] as $key => $goods_id){
            $detail = new DetReceiving;
            $detail->goods_id = $goods_id;
            $detail->qty = $input['qty'][$key];
            $detail->unit_id = $input['unit_id'][$key];
            $detail->price = $input['price'][$key];
            $detail->sub_total = $input['sub_total'][$key];
            $receiving_details[] = $detail;
        }
        try{
            DB::beginTransaction();

            $receiving->save();
            DetReceiving::where('receiving_id', $receiving->id)->delete();

            foreach($receiving_details as $detail) {
                $detail->receiving_id = $receiving->id;
                $detail->save();
            }
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
                "message" => "Failed to updated receiving!"
            ];
        }

        return redirect()->route('receiving.index')->with($notif['type'], $notif['message']);
    }

    public function destroy(Receiving $receiving)
    {
        DetReceiving::where('receiving_id', $receiving->id)->delete();
        $receiving->delete();
        return redirect()->route('receiving.index')->with('success', 'Receiving data has been deleted!');
    }
}
