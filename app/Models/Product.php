<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;


    public function category()
    {
      return $this->belongsTo('App\Models\Category');
    }

    public function brand()
    {
      return $this->belongsTo('App\Models\Brand');
    }

    public function size()
    {
      return $this->belongsTo('App\Models\Size');
    }

    public function group()
    {
      return $this->belongsTo('App\Models\Group');
    }

//     public function Brands()
//   {
//       return $this->belongsTo('App\Models\Brand')->select(array('id', 'name'));
//   }

//   public function Categories()
//     {
//         return $this->belongsTo('App\Models\Category')->select(array('id', 'name'));
//     }

//     public function Sizes()
//     {
//         return $this->belongsTo('App\Models\Size')->select(array('id', 'name'));
//     }

    public function Stocks()
    {
        return $this->hasMany('App\Models\Stock');
    }

    public function orderdetails()
    {
        return $this->hasMany('App\Models\OrderDetail');
    }

    public function salesreturndetails()
    {
        return $this->hasMany('App\Models\SalesReturnDetail');
    }

    public function stocktransferrequest()
      {
          return $this->hasMany('App\Models\StockTransferRequest');
      }

    public function purchasereturn()
    {
        return $this->hasMany('App\Models\PurchaseReturn');
    }

    public function purchasehistory()
    {
        return $this->hasMany('App\Models\PurchaseHistory');
    }

    public function alternativeproduct()
    {
        return $this->hasMany('App\Models\AlternativeProduct');
    }

    public function replacement()
    {
        return $this->hasMany('App\Models\Replacement');
    }

    public function stocktransfer()
    {
        return $this->hasMany('App\Models\StockTransfer');
    }



}
