<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Customer extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    protected $fillable = [
        'service_id',
        'first_name',
        'last_name',
        'mobile_number',
        'email',
        'address',
        'pin_code',
        'city',
        'state',
        'gender',
        'date_of_birth',
        'place_of_birth',
        'nationality',
        'is_paid',
        'registration_step',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_paid' => 'boolean',
        'is_active' => 'boolean',
        'registration_step' => 'integer',
        'service_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Get full name attribute
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
    
    // Relationships
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function appointmentLetters(): HasMany
    {
        return $this->hasMany(AppointmentLetter::class);
    }

    public function applicationProgress(): HasMany
    {
        return $this->hasMany(ApplicationProgress::class);
    }

    public function applicationDocuments(): HasMany
    {
        return $this->hasMany(ApplicationDocument::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function finalDetails(): HasMany
    {
        return $this->hasMany(FinalDetail::class);
    }

    public function order()
    {
        return $this->hasOne(ApplicationOrder::class);
    }

    // public static function getDashboardData($type = null, $paid = null, $service = null)
    // {
    //     $query = DB::table('customers')
    //         ->selectRaw('YEAR(created_at) as recyear,
    //                     MONTH(created_at) as recmonth,
    //                     DAY(created_at) as recday,
    //                     COUNT(id) as totaluser')
    //         ->whereNull('deleted_at');

    //     if ($type) {
    //         $query->where('passport_type', $type);
    //     }

    //     if (!is_null($paid)) {
    //         $query->where('is_paid', $paid);
    //     }

    //     if ($service) {
    //         $query->where('service_code', $service);
    //     }

    //     return $query->groupByRaw('YEAR(created_at), MONTH(created_at), DAY(created_at)')
    //         ->orderByRaw('YEAR(created_at) desc, MONTH(created_at) desc, DAY(created_at) desc')
    //         ->limit(10)
    //         ->get();
    // }
}
