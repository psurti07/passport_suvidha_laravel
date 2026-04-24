<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class zaakpayLog extends Model
{
    use HasFactory;

    protected $table = 'zaakpay_logs_entry';

    protected $fillable = [
        'customer_id',
        'order_id',
        'order_amount',
        'order_note',
        'payment_id',
        'offer_type',
        'reference_id',
        'tx_status',
        'payment_mode',
        'service_type'
    ];
}
