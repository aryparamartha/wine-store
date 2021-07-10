<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetComplTx extends Model
{   
    protected $fillable = [
        'compliment_tx_id', 
        'goods_id', 
        'qty', 
        'unit_id', 
        'price', 
    ];

    public function tx()
    {
        return $this->belongsTo('App\ComplimentTx');
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
