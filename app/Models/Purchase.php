<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    public function purchasehistory(){
        return $this->hasMany('App\Models\PurchaseHistory');
    }

    public function vendor(){
        return $this->belongsTo('App\Models\Vendor');
    }
}
