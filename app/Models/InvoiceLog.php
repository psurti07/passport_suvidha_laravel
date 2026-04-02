<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'log_detail',
        'card_number',
        'invoice_id',
        'staff_id'
    ];

    protected $casts = [
        'invoice_id' => 'integer',
        'staff_id' => 'integer',
    ];

    // Relationships
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}
