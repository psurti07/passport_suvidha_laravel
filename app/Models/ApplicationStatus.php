<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApplicationStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'status_name',
        'slug',
        'priority_no',
        'step'
    ];

    protected $casts = [
        'priority_no' => 'integer',
    ];

    // Relationships
    public function applicationProgresses(): HasMany
    {
        return $this->hasMany(ApplicationProgress::class, 'status_id');
    }
}
