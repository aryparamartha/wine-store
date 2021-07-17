<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetReceiving extends Model
{   
    protected $fillable = [
        'receiving_id',
        'goods_id',
        'qty',
        'unit_id',
        'price',
        'sub_total',
        'tax',
    ];

    public function receiving()
    {
        return $this->belongsTo('App\Receiving');
    }

    public function goods()
    {
        return $this->belongsTo('App\Goods');
    }

    public function unit()
    {
        return $this->belongsTo('App\Unit');
    }
}
