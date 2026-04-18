<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferOrder extends Model
{
    use HasFactory;
    protected $table = 'offer_order';
    protected $fillable = [
        'full_name',
        'mobile',
        'email',
        'card_number',
        'offer_type',
        'amount',
        'payment_id',
        'created_at',
        'updated_at',
    ];

}
