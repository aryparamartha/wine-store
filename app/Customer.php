<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'address',
        'number',
        'email',
        'pic',
        'type',
        'note',
    ];

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'pic');
    }
}
