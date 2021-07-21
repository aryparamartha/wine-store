<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Image;

class CompanyProfile extends Model
{
    public static $dir_photo = "/assets/images/logo/";
    protected $fillable = [
        'name',
        'address', 
        'phone', 
        'email', 
        'website', 
        'logo',
    ];


    public function getLogo(){
        return self::$dir_photo . $this->logo;
    }

    public function uploadPhoto($file, $filename){
        $destinationPath = public_path(self::$dir_photo);
        $img = Image::make($file);
        $img->resize(null, 500, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath. '/'. $filename);
    }
}
