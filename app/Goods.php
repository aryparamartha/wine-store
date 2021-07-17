<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Goods extends Model
{
    protected $fillable = [
        'code',
        'name',
        'unit_id',
        'amount',
        'purchase_price',
        'selling_price',
        'tax_price',
        'added_by',
    ];

    public function showCurrency($n){
        return "Rp".number_format($n, 0, "", ".");
    }
    
    public function unit()
    {
        return $this->belongsTo('App\Unit');
    }

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'added_by');
    }
}
