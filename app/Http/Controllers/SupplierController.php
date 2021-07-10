<?php

namespace App\Http\Controllers;

use App\Supplier;
use App\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::get();
        $employees = Employee::get();
        return view('supplier.index', compact('suppliers', 'employees'));
    }

    public function store(Request $request)
    { 
        $input = $request->all();
        $supplier = Supplier::create($input);
        return redirect()->route('supplier.index')->with('success', 'New supplier has been saved!');
    }

    public function edit(Supplier $supplier){
        return view('supplier.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $input = $request->all();
        $supplier->update($input);
        return redirect()->route('supplier.index')->with('success', 'Supplier data has been updated!');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('supplier.index')->with('success', 'Supplier data has been deleted!');
    }
}
