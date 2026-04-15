<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'user_id',
        'log_detail',
        'card_number'
    ];

    protected $casts = [
        'invoice_id' => 'integer',
        'user_id' => 'integer',
    ];

    // Relationships
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
