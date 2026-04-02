<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreDefinedMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'status_id',
        'message_name',
        'message_remarks',
    ];

    /**
     * Get the route key name for Laravel's route model binding.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'id';
    }

    public function status()
    {
        return $this->belongsTo(ApplicationStatus::class, 'status_id');
    }
}
