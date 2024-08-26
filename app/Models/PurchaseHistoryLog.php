<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseHistoryLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'available_quantity',
        'purchase_price',
        'total_amount',
        'log_date',
    ];
}
