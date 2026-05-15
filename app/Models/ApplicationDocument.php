<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'document_type_id',
        'is_submitted',
        'file_path',
        'is_verified',
    ];

    protected $casts = [
        'is_submitted' => 'boolean',
        'is_verified' => 'boolean',
    ];

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    // public function getFileUrlAttribute()
    // {
    //     return $this->file_path ? asset('storage/' . $this->file_path) : null;
    // }
} 