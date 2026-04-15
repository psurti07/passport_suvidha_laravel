<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Customer extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    protected $table = "customers";

    protected $fillable = [
        'first_name',
        'last_name',
        'mobile_number',
        'email',
        'password',
        'address',
        'pin_code',
        'city',
        'state',
        'gender',
        'date_of_birth',
        'place_of_birth',
        'nationality',
        'service_id',
        'payment_info_id',
        'service_code',
        'passport_type',
        'book_size',
        'gstno',
        'is_paid',
        'registration_step',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        // 'password' => 'hashed',
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

    public static function getDashboardData($type = null, $paid = null, $service = null)
    {
        $query = DB::table('customers')
            ->selectRaw('YEAR(created_at) as recyear,
                        MONTH(created_at) as recmonth,
                        DAY(created_at) as recday,
                        COUNT(id) as totaluser')
            ->whereNull('deleted_at');

        if ($type) {
            $query->where('passport_type', $type);
        }

        if (!is_null($paid)) {
            $query->where('is_paid', $paid);
        }

        if ($service) {
            $query->where('service_code', $service);
        }

        return $query->groupByRaw('YEAR(created_at), MONTH(created_at), DAY(created_at)')
            ->orderByRaw('YEAR(created_at) desc, MONTH(created_at) desc, DAY(created_at) desc')
            ->limit(10)
            ->get();
    }
}
