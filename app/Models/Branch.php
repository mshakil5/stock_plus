<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    public function Stocks()
    {
        return $this->hasMany('App\Models\Stock');
    }

    public function stocktransferrequest()
    {
        return $this->hasMany('App\Models\StockTransferRequest');
    }

    public function user()
    {
        return $this->hasMany('App\Models\User');
    }
}
