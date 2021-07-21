<?php

namespace App\Http\Controllers;

use App\CompanyProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class CompanyProfileController extends Controller
{
    public function index()
    {
        $profile = CompanyProfile::first();
        return view('company.index', compact('profile'));
    }

    public function update(Request $request, CompanyProfile $profile)
    {
        $input = $request->all();
        $profile->update($input);
        $file = $request->file('logo_file');
        if($file != null){
            File::delete($profile->getLogo());
            $profile->logo = "company_logo." . $file->getClientOriginalExtension();
            $profile->uploadPhoto($file, $profile->logo);
            $profile->save();
        }
        return redirect()->route('profile.index')->with('success', 'Company profile has been updated!');
    }
}
