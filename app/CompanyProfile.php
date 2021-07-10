<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyProfile extends Model
{
    protected $fillable = [
        'name',
        'address', 
        'phone', 
        'email', 
        'website', 
        'logo',
    ];
}
