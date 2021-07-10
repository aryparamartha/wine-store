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

    public function store(Request $request)
    { 
        $input = $request->all();
        $input['added_by'] = Auth::user()->id;
        $goods = Goods::create($input);
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
