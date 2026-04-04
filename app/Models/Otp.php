<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    use HasFactory;

    protected $fillable = [
        'mobile_number',
        'otp',
        'purpose',
        'sent_at',
        'is_verified'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'is_verified' => 'boolean'
    ];

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }
}
