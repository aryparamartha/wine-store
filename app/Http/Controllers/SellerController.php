<?php

namespace App\Http\Controllers;

use App\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SellerController extends Controller
{
    public function index()
    {
        $sellers = Seller::get();
        return view('seller.index', compact('sellers'));
    }

    public function store(Request $request)
    { 
        $input = $request->all();
        $seller = Seller::create($input);
        return redirect()->route('seller.index')->with('success', 'New seller has been saved!');
    }

    public function edit(Seller $seller){
        return view('seller.edit', compact('seller'));
    }

    public function update(Request $request, Seller $seller)
    {
        $input = $request->all();
        $seller->update($input);
        return redirect()->route('seller.index')->with('success', 'Seller data has been updated!');
    }

    public function destroy(Seller $seller)
    {
        $seller->delete();
        return redirect()->route('seller.index')->with('success', 'Seller data has been deleted!');
    }
}
