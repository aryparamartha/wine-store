<?php

namespace App\Http\Controllers;

use App\Company;
use App\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::get();
        $employees = Employee::get();
        return view('company.index', compact('companies', 'employees'));
    }

    public function store(Request $request)
    { 
        $input = $request->all();
        $company = Company::create($input);
        return redirect()->route('company.index')->with('success', 'New company has been saved!');
    }

    public function edit(Company $company){
        return view('company.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $input = $request->all();
        $company->update($input);
        return redirect()->route('company.index')->with('success', 'Company data has been updated!');
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return redirect()->route('company.index')->with('success', 'Company data has been deleted!');
    }
}
