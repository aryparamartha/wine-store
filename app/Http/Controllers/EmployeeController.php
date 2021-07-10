<?php

namespace App\Http\Controllers;

use App\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::get();
        return view('employee.index', compact('employees'));
    }

    public function store(Request $request)
    { 
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $employee = Employee::create($input);
        return redirect()->route('employee.index')->with('success', 'New employee has been saved!');
    }

    public function edit(Employee $employee){
        return view('employee.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $input = $request->all();
        $input['password'] = ($input['password'] == '') ? $employee->password : Hash::make($input['password']);
        $employee->update($input);
        return redirect()->route('employee.index')->with('success', 'Employee data has been updated!');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('employee.index')->with('success', 'Employee data has been deleted!');
    }
}
