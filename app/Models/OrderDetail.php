<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    public function orders(){
        return $this->belongsTo('App\Models\Order');
    }

    // public function orders()
    // {
    //     return $this->belongsTo(Order::class, 'order_id');
    // }

    public function product(){
        return $this->belongsTo('App\Models\Product');
    }
}
