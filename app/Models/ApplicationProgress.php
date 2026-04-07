<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class ApplicationProgress extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'application_progress';

    protected $fillable = [
        'customer_id',
        'status_id',
        'status_date',
        'remark',
        'remarked_by',
        'file_type',
        'file',
    ];

    protected $casts = [
        'status_date' => 'datetime',
    ];

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function status()
    {
        return $this->belongsTo(ApplicationStatus::class, 'status_id');
    }

    public function remarkedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'remarked_by');
    }

    /**
     * Get the related file based on file_type.
     */
    public function getRelatedFileAttribute()
    {
        if (!$this->file_type || !$this->file) {
            return null;
        }

        if ($this->file_type === 'appointment_letters') {
            return AppointmentLetter::find($this->file);
        } elseif ($this->file_type === 'final_details') {
            return FinalDetail::find($this->file);
        }

        return null;
    }
} 