<?php

namespace App\Http\Controllers;

use App\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Carbon;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::get();
        return view('unit.index', compact('units'));
    }

    public function store(Request $request)
    { 
        $input = $request->all();
        $unit = Unit::create($input);
        return redirect()->route('unit.index')->with('success', 'New unit has been saved!');
    }

    public function edit(Unit $unit){
        return view('unit.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit)
    {
        $input = $request->all();
        $unit->update($input);
        return redirect()->route('unit.index')->with('success', 'Unit data has been updated!');
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();
        return redirect()->route('unit.index')->with('success', 'Unit data has been deleted!');
    }
}
