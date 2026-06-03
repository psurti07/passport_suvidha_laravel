<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FbAdsEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'fbclid',
        'sent_data',
        'received_data',
    ];

    protected $casts = [
        'sent_data' => 'array',
        'received_data' => 'array'
    ];
}
