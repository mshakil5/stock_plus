<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyStockLog extends Model
{
    use HasFactory;

        protected $fillable = [
            'product_id',
            'quantity',
            'log_date',
        ];
}
