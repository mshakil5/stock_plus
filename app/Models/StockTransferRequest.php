<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransferRequest extends Model
{
    use HasFactory;

    public function product()
  {
      return $this->belongsTo('App\Models\Product')->select('*');
  }

  public function branch()
  {
      return $this->belongsTo('App\Models\Branch')->select('*');
  }
}
