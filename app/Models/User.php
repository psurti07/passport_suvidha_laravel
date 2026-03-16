<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\TicketRemark; // Import the renamed model

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'created_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Check if user is an admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is a staff member
     */
    public function isStaff()
    {
        return $this->role === 'staff';
    }

    /**
     * Get the admin who created this user
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the remarks (now ticketRemarks) made by the user (admin/staff).
     */
    public function ticketRemarks(): HasMany // Renamed method and updated class reference
    {
        return $this->hasMany(TicketRemark::class);
    }
    
    /**
     * Get the appointment letters uploaded by the user.
     */
    public function uploadedAppointmentLetters(): HasMany
    {
        return $this->hasMany(AppointmentLetter::class, 'uploaded_by');
    }
}
