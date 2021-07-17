<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::get();
        $employees = Employee::get();
        return view('customer.index', compact('customers', 'employees'));
    }

    public function store(Request $request)
    { 
        $input = $request->all();
        $customer = Customer::create($input);
        return redirect()->route('customer.index')->with('success', 'New customer has been saved!');
    }

    public function edit(Customer $customer){
        return view('customer.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $input = $request->all();
        $customer->update($input);
        return redirect()->route('customer.index')->with('success', 'Customer data has been updated!');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customer.index')->with('success', 'Customer data has been deleted!');
    }
}
