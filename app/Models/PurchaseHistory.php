<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseHistory extends Model
{
    use HasFactory;

    public function product(){
        return $this->belongsTo('App\Models\Product');
    }

    public function purchase(){
        return $this->belongsTo('App\Models\Purchase');
    }

    public function purchasereturn(){
        return $this->hasMany('App\Models\PurchaseReturn');
    }
}
