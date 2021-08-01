<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon;
use Image;

class ComplimentTx extends Model
{   
    public static $dir_photo = "/uploads/transaction/compliment/";
    protected $fillable = [
        'invoice_id',
        'employee_id',
        'customer_id',
        'seller_id',
        'total',
        'tax',
        'grand_total',
        'status',
        'payment_date',
        'payment_type',
        'total_paid',
        'remainder',
    ];

    public function showCurrency($n){
        return "Rp".number_format($n, 2, ",", ".");
    }

    
    public function localTz($carbon){
        if($carbon==null || $carbon=="") return "";
        $time = Carbon::createFromFormat('Y-m-d H:i:s', $carbon, config('app.timezone'));
        return $time->setTimezone(config('app.local_timezone'));
    }

    public static function genInvoiceNo(){
        $now = Carbon::now()->setTimezone(config('app.local_timezone'))->format('Y-m-d');
        $timestamp1 = $now . ' 00:00:00';
        $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $timestamp1, config('app.local_timezone'));
        $timestamp2 = $now . ' 23:59:59';
        $endDate = Carbon::createFromFormat('Y-m-d H:i:s', $timestamp2, config('app.local_timezone'));

        $startDate->setTimezone(config('app.timezone'));
        $endDate->setTimezone(config('app.timezone'));

        $count = self::whereBetween('created_at',[$startDate->format('Y-m-d H:i:s'), $endDate->format('Y-m-d H:i:s')])->count();
        
        return "C" . Carbon::now()->setTimezone(config('app.local_timezone'))->format('ymdHi') . sprintf('%04d',$count+1);
    }

    public function employee()
    {
        return $this->belongsTo('App\Employee');
    }

    public function seller()
    {
        return $this->belongsTo('App\Seller');
    }

    public function customer()
    {
        return $this->belongsTo('App\Customer', 'customer_id');
    }

    public function details()
    {
        return $this->hasMany('App\DetComplTx');
    }

    public function payment_logs()
    {
        return $this->morphMany('App\TxPaymentLog', 'logable');
    }
}
