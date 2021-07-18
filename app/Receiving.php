<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon;

class Receiving extends Model
{   
    protected $fillable = [
        'invoice_id',
        'employee_id',
        'supplier_id',
        'grand_total',
        'receiving_date',
    ];

    public function showCurrency($n){
        return "Rp".number_format($n, 0, "", ".");
    }

    public function localTz($carbon, $format="Y-m-d H:i:s"){
        if($carbon==null || $carbon=="") return "";
        $time = Carbon::createFromFormat($format, $carbon, config('app.timezone'));
        return $time->setTimezone(config('app.local_timezone'))->format($format);
    }

    public function employee()
    {
        return $this->belongsTo('App\Employee');
    }

    public function supplier()
    {
        return $this->belongsTo('App\Supplier');
    }

    public function detail()
    {
        return $this->hasMany('App\DetReceiving');
    }
}
