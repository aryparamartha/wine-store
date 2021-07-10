<?php

namespace App\Http\Controllers;

use App\Client;
use App\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::get();
        $employees = Employee::get();
        return view('client.index', compact('clients', 'employees'));
    }

    public function store(Request $request)
    { 
        $input = $request->all();
        $client = Client::create($input);
        return redirect()->route('client.index')->with('success', 'New client has been saved!');
    }

    public function edit(Client $client){
        return view('client.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $input = $request->all();
        $client->update($input);
        return redirect()->route('client.index')->with('success', 'Client data has been updated!');
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('client.index')->with('success', 'Client data has been deleted!');
    }
}
