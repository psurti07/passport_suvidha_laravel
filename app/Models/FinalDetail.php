<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinalDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'file_path',
        'upload_date',
        'uploaded_by',
        'is_approved',
        'status',
        'approved_date',
        'approved_by_role',
        'approved_by',
    ];

    protected $casts = [
        'upload_date' => 'datetime',
        'approved_date' => 'datetime',
        'is_approved' => 'boolean',
    ];

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Custom logic 
     */
    public function approver()
    {
        if (!$this->approved_by || !$this->approved_by_role) {
            return null;
        }

        return match ($this->approved_by_role) {
            'user' => User::find($this->approved_by),
            'customer' => Customer::find($this->approved_by),
            default => null,
        };
    }

    public function getApproverNameAttribute()
    {
        $approver = $this->approver();

        if (!$approver) {
            return 'Not approved';
        }

        if ($this->approved_by_role === 'user') {
            return $approver->name . ' (Staff)';
        } else {
            return $approver->full_name . ' (Customer)';
        }
    }
}
