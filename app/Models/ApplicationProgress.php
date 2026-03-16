<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationProgress extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'application_progress';

    protected $fillable = [
        'customer_id',
        'application_status',
        'status_date',
        'remark',
        'remarked_by',
        'file_type',
        'file',
    ];

    protected $casts = [
        'status_date' => 'datetime',
    ];

    /**
     * Get the customer that owns the application progress.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the user who remarked the application progress.
     */
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