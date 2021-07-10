<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
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
