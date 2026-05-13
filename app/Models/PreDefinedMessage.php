<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PreDefinedMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'status_id',
        'message_name',
        'message_remarks',
    ];

    // Relationships
    public function status(): BelongsTo
    {
        return $this->belongsTo(ApplicationStatus::class, 'status_id');
    }
}
