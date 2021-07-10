<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Breakage extends Model
{
    protected $fillable = [
        'goods_id',
        'reason', 
        'qty', 
        'created_by',
    ];

    public function goods()
    {
        return $this->belongsTo('App\Goods');
    }

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'created_by');
    }
}
