<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public function customer(){
        return $this->belongsTo('App\Models\Customer');
    }

    public function orderdetails(){
        return $this->hasMany('App\Models\OrderDetail');
    }

    // public function orderdetails()
    // {
    //     return $this->hasOne(OrderDetail::class, 'order_id', 'id');
    // }

    public function salesreturn(){
        return $this->hasMany('App\Models\SalesReturn');
    }
}
