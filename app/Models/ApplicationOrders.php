<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationOrders extends Model
{
    use HasFactory;
    protected $table = "application_orders";
    protected $fillable = [
        'id',
        'customer_id',
        'card_number',
        'amount',
        'payment_id',
        'created_at',
        'updated_at',
    ];
}
