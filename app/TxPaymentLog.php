<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Eloquent;
use Image;

class TxPaymentLog extends Eloquent 
{
    public static $dir_reg_photo = "/uploads/transaction/regular/";
    public static $dir_compl_photo = "/uploads/transaction/compliment/";
    protected $fillable = [
        'tx_id',
        'payment_type_id',
        'paid_amount',
        'tx_type',
        'pic',
        'payment_proof',
        'logable_id',
        'logable_type',
    ];

    public function getPaymentProof(){
        return public_path($this->getDir()) . "/" . $this->transfer_proof;
    }
    
    public function getDir(){
        return ($this->tx_type == 1) ? self::$dir_reg_photo : self::$dir_compl_photo;
    }

    public function uploadPhoto($file, $filename){
        $destinationPath = public_path($this->getDir());
        $img = Image::make($file);
        $img->resize(null, 500, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath. '/'. $filename);
    }

    public function logable()
    {
        return $this->morphTo();
    }

    public function payment_type()
    {
        return $this->belongsTo('App\PaymentType');
    }
}
