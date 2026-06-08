<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Customer;
use App\Models\Service;

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
        'deleted_at'
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime:H:i',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    // public function service(): BelongsTo
    // {
    //     return $this->belongsTo(Service::class)->withTrashed();
    // }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
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

    const SCHEDULED = 1;
    const COMPLETED = 2;
    const CANCELLED = 3;
    const NOT_REACHABLE = 4;

    const LANGUAGE_HINDI = 1;
    const LANGUAGE_ENGLISH = 2;
    const LANGUAGE_GUJRATI = 3;

    public static function getLanguages()
    {
        return [
            self::LANGUAGE_ENGLISH => 'English',
            self::LANGUAGE_HINDI => 'Hindi',
            self::LANGUAGE_GUJRATI => 'Gujarati'
        ];
    }

    public static function getLangusgeName($id)
    {
        return self::getLanguages()[$id] ?? null;
    }

    public static function getLanguageId($name)
    {
        return array_search($name, self::getLanguages());
    }
}
