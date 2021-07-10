<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'address', 
        'number', 
        'email', 
        'pic', 
    ];

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'pic');
    }
}
