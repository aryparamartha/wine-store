<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Employee extends Authenticatable
{
    protected $fillable = [
        'name',
        'address', 
        'birthpl', 
        'birthdate', 
        'email',
    ];

    protected $hidden = [
        'password',
    ];
}
