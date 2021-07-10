<?php

namespace App\Http\Controllers;

use App\Breakage;
use App\Goods;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class BreakageController extends Controller
{
    public function index()
    {
        $breakages = Breakage::with('employee', 'goods')->get();
        $goods = Goods::get();
        return view('breakage.index', compact('breakages', 'goods'));
    }

    public function store(Request $request)
    { 
        $input = $request->all();
        $input['created_by'] = Auth::user()->id;
        $breakage = Breakage::create($input);
        return redirect()->route('breakage.index')->with('success', 'New breakage has been saved!');
    }

    public function edit(Breakage $breakage){
        return view('breakage.edit', compact('breakage'));
    }

    public function update(Request $request, Breakage $breakage)
    {
        $input = $request->all();
        $input['created_by'] = Auth::user()->id;
        $breakage->update($input);
        return redirect()->route('breakage.index')->with('success', 'Breakage data has been updated!');
    }

    public function destroy(Breakage $breakage)
    {
        $breakage->delete();
        return redirect()->route('breakage.index')->with('success', 'Breakage data has been deleted!');
    }
}
