<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'mobile_number',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'pin_code',
        'gender',
        'date_of_birth',
        'place_of_birth',
        'nationality',
        'payment_info_id',
        'service_code',
        'is_paid',
        'registration_step',
        'passport_type',
        'book_size',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'date_of_birth' => 'date',
        'is_paid' => 'boolean',
        'registration_step' => 'integer',
    ];

    // Get full name attribute
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
    
    /**
     * Get all appointment letters for the customer.
     */
    public function appointmentLetters(): HasMany
    {
        return $this->hasMany(AppointmentLetter::class);
    }

    /**
     * Get all application progress entries for the customer.
     */
    public function applicationProgress(): HasMany
    {
        return $this->hasMany(ApplicationProgress::class);
    }

    /**
     * Get all application documents for the customer.
     */
    public function applicationDocuments(): HasMany
    {
        return $this->hasMany(ApplicationDocument::class);
    }
}
