<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReturn extends Model
{
    use HasFactory;

    public function customer(){
        return $this->belongsTo('App\Models\Customer');
    }

    public function salesreturndetail(){
        return $this->hasMany('App\Models\SalesReturnDetail');
    }

    public function orders(){
        return $this->belongsTo('App\Models\Order');
    }
}
