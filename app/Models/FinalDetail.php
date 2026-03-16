<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinalDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'file_path',
        'upload_date',
        'uploaded_by',
        'is_approved',
        'approved_date',
        'approved_by_role',
        'approved_by',
    ];

    protected $casts = [
        'upload_date' => 'datetime',
        'approved_date' => 'datetime',
        'is_approved' => 'boolean',
    ];

    /**
     * Get the customer that owns the final detail.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the user who uploaded the final detail.
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the entity who approved the final detail.
     */
    public function approver()
    {
        if (!$this->approved_by_role || !$this->approved_by) {
            return null;
        }
        
        if ($this->approved_by_role === 'user') {
            return User::find($this->approved_by);
        } else {
            return Customer::find($this->approved_by);
        }
    }

    /**
     * Get the approver name.
     */
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
