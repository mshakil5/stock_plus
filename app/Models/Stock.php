<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    public function Products()
  {
      return $this->belongsTo('App\Models\Product')->select('*');
  }

  public function Branches()
  {
      return $this->belongsTo('App\Models\Branch')->select('*');
  }
}
