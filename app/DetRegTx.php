<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetRegTx extends Model
{   
    protected $fillable = [
        'regular_tx_id',
        'goods_id',
        'qty',
        'unit_id',
        'price',
        'disc',
    ];

    public function tx()
    {
        return $this->belongsTo('App\RegularTx');
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
