<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoodsLog extends Model
{   
    protected $fillable = [
        'goods_id',
        'status',
        'date',
        'qty',
        'post_amount',
        'price',
        'source',
        'note',
    ];

    public function goods()
    {
        return $this->belongsTo('App\Goods');
    }
}
