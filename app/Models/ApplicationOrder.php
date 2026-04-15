<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationOrder extends Model
{
    use HasFactory;
        
    protected $fillable = [
        'customer_id',
        'card_number',
        'amount',
        'payment_id'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
