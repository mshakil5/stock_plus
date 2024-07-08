<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReturnDetail extends Model
{
    use HasFactory;

    public function salesreturn(){
        return $this->belongsTo('App\Models\SalesReturn');
    }

    public function product(){
        return $this->belongsTo('App\Models\Product');
    }
}
