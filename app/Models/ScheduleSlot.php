<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleSlot extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'service_id',
        'date',
        'time',
        'language',
        'remarks',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime:H:i',
    ];

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class)->withTrashed();
    }

    // Helper Methods
    public function getLanguageTextAttribute(): string
    {
        return match ($this->language) {
            1 => 'Hindi',
            2 => 'English',
            3 => 'Gujarati',
            default => 'Unknown',
        };
    }

    public function getStatusTextAttribute(): string
    {
        return match ($this->status) {
            1 => 'Schedule',
            2 => 'Completed',
            3 => 'Cancelled',
            4 => 'Not Reachable',
            default => 'Unknown',
        };
    }
}
