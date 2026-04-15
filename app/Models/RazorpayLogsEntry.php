<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RazorpayLogsEntry extends Model
{
    use HasFactory;
     protected $table = 'razorpay_logs_entry';

    protected $fillable = [
        'customer_id',  
        'order_id',
        'order_amount',
        'order_note',
        'reference_id',
        'tx_status',
        'payment_mode',
    ];

}
