<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'service_name',
        'service_code',
        'service_gov_amount',
        'service_charges',
        'service_gst',
        'service_total_amount',
    ];

    protected $casts = [
        'service_gov_amount' => 'decimal:2',
        'service_charges' => 'decimal:2',
        'service_gst' => 'decimal:2',
        'service_total_amount' => 'decimal:2',
    ];
}
