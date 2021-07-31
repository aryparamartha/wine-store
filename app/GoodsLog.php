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

    public static function getLatestLog($goods_id){
        return GoodsLog::where("goods_id", $goods_id)->orderBy('id', 'DESC')->first();
    }

    public function goods()
    {
        return $this->belongsTo('App\Goods');
    }

    public function logable()
    {
        return $this->morphTo();
    }
}
