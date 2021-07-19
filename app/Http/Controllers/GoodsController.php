<?php

namespace App\Http\Controllers;

use App\Goods;
use App\GoodsLog;
use App\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use DB;
use Carbon;

class GoodsController extends Controller
{
    public function index()
    {
        $goods = Goods::get();
        $units = Unit::get();
        return view('goods.index', compact('goods', 'units'));
    }

    public function insert(Request $request)
    {
        $input = $request->all();
        $input["tax_price"] = bcdiv(10/11 * $input["selling_price"], 1, 2) + 0.01;
        try{
            DB::beginTransaction();
            $goods = Goods::create($input);
            //log first register goods
            $log = new GoodsLog();
            $log->goods_id = $goods->id;
            $log->status = "IN";
            $log->date = Carbon::now()->format('Y-m-d H:i:s');
            $log->qty = $goods->amount;
            $log->post_amount = $goods->amount;
            $log->price = 0;
            $log->source = "First Register";
            $log->save();
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
        }
        return $goods;
    }

    public function storeAPI(Request $request){
        $goods = $this->insert($request);
        $response['status'] = 200;
        $response['message'] = "Success insert product";
        $response['data'] = Goods::where("id", $goods->id)->with('unit')->first();

        return response()->json($response, 200);
    }

    public function store(Request $request)
    { 
        $this->insert($request);
        return redirect()->route('goods.index')->with('success', 'New product has been saved!');
    }

    public function edit(Goods $goods){
        return view('goods.edit', compact('goods'));
    }

    public function update(Request $request, Goods $goods)
    {
        $input = $request->all();
        $goods->update($input);
        return redirect()->route('goods.index')->with('success', 'Product data has been updated!');
    }

    public function destroy(Goods $goods)
    {
        $goods->delete();
        return redirect()->route('goods.index')->with('success', 'Product data has been deleted!');
    }
}
