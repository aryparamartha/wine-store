<?php

namespace App\Http\Controllers;

use App\Goods;
use App\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
        $goods = Goods::create($input);
        return $goods;
    }

    public function storeAPI(Request $request){
        $goods = $this->insert($request);
        $response['status'] = 200;
        $response['message'] = "Success insert goods";
        $response['data'] = Goods::where("id", $goods->id)->with('unit')->first();

        return response()->json($response, 200);
    }

    public function store(Request $request)
    { 
        $this->insert($request);
        return redirect()->route('goods.index')->with('success', 'New goods has been saved!');
    }

    public function edit(Goods $goods){
        return view('goods.edit', compact('goods'));
    }

    public function update(Request $request, Goods $goods)
    {
        $input = $request->all();
        $goods->update($input);
        return redirect()->route('goods.index')->with('success', 'Goods data has been updated!');
    }

    public function destroy(Goods $goods)
    {
        $goods->delete();
        return redirect()->route('goods.index')->with('success', 'Goods data has been deleted!');
    }
}
