<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RazorpayLog extends Model
{    
    use HasFactory;

    protected $table = 'razorpay_logs_entry';

    protected $fillable = [
        'customer_id',
        'order_id',
        'order_amount',
        'order_note',
        'reference_id',
        'payment_id',
        'tx_status',
        'payment_mode',
        'service_type',
        'offer_type',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }   

    public function getServiceTextAttribute()
    {
        return match($this->service_type) {
            'NP36' => 'New Passport - 36 Pages',
            'NP60' => 'New Passport - 60 Pages',
            'TP36' => 'Tatkal Passport - 36 Pages',
            'TP60' => 'Tatkal Passport - 60 Pages',
            default => $this->service_type ?? 'N/A',
        };
    }

    public function getOfferTextAttribute()
    {
        return match($this->offer_type) {
            1 => 'Card Offer',
            2 => 'Star Offer',
            default => 'Direct',
        };
    }
}
