<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashfreeLog extends Model
{
    use HasFactory;

    protected $table = 'cashfree_logs_entry';

    protected $fillable = [
        'customer_id',
        'order_id',
        'order_amount',
        'order_note',
        'reference_id',
        'tx_status',
        'payment_mode',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
