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

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'service_id',
        'full_name',
        'mobile_number',
        'email',
        'fbclid',

        'father_name',
        'mother_name',
        'marital_status',
        'spouse_name',

        'emergency_contact_name',
        'emergency_contact_mobile',
        'emergency_contact_email',

        'address',
        'pin_code',
        'police_station_name',
        'city',
        'state',

        'is_address_permanent',
        'permanent_address',
        'permanent_pin_code',
        'permanent_city',
        'permanent_state',

        'gender',
        'date_of_birth',
        'place_of_birth',
        'education_qualification',
        'employment_type',
        'organisation_name',
        'nationality',
        'registration_step',
        'is_paid',
        'payment_date',
        'is_active',
        'is_dnd',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_paid' => 'boolean',
        'is_active' => 'boolean',
        'is_dnd' => 'boolean',
        'registration_step' => 'integer',
        'service_id' => 'integer',
        'payment_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // public function getFullNameAttribute()
    // {
    //     return "{$this->full_name}";
    // }

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

    public function latestApplicationProgress()
    {
        return $this->hasOne(ApplicationProgress::class)
            ->latestOfMany('id');
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

    public function applicationOrders()
    {
        return $this->hasOne(ApplicationOrder::class);
    }

    public function fbadsentry()
    {
        return $this->hasMany(FbAdsEntry::class);
    }
    // public static function getDashboardData($type = null, $paid = null)
    // {
    //     $query = DB::table('customers')
    //         ->join('services', 'customers.service_id', '=', 'services.id')
    //         ->selectRaw('
    //             YEAR(customers.created_at) as recyear,
    //             MONTH(customers.created_at) as recmonth,
    //             DAY(customers.created_at) as recday,
    //             COUNT(customers.id) as totaluser
    //         ')
    //         ->whereNull('customers.deleted_at');

    //     if ($type == 'normal') {
    //         $query->whereIn('services.service_code', ['NP36', 'NP60']);
    //     }

    //     if ($type == 'tatkal') {
    //         $query->whereIn('services.service_code', ['TP36', 'TP60']);
    //     }

    //     if (!is_null($paid)) {
    //         $query->where('customers.is_paid', $paid);
    //     }

    //     return $query->groupByRaw('YEAR(customers.created_at), MONTH(customers.created_at), DAY(customers.created_at)')
    //         ->orderByRaw('YEAR(customers.created_at) DESC, MONTH(customers.created_at) DESC, DAY(customers.created_at) DESC')
    //         ->limit(10)
    //         ->get();
    // }

    public static function getDashboardData($type = null, $paid = null)
    {
        $date = ($paid == 1) ? 'customers.payment_date' : 'customers.created_at';

        $query = DB::table('customers')
            ->join('services', 'customers.service_id', '=', 'services.id')
            ->selectRaw("
                YEAR($date) as recyear,
                MONTH($date) as recmonth,
                DAY($date) as recday,
                COUNT(customers.id) as totaluser
            ")
            ->whereNull('customers.deleted_at');

        if ($type == 'normal') {
            $query->whereIn('services.service_code', ['NP36', 'NP60']);
        }

        if ($type == 'tatkal') {
            $query->whereIn('services.service_code', ['TP36', 'TP60']);
        }

        if (!is_null($paid)) {
            $query->where('customers.is_paid', $paid);
        }

        if ($paid == 1) {
            $query->whereNotNull('customers.payment_date');
        }

        return $query->groupByRaw("
                YEAR($date),
                MONTH($date),
                DAY($date)
            ")
            ->orderByRaw("
                YEAR($date) DESC,
                MONTH($date) DESC,
                DAY($date) DESC
            ")
            ->limit(10)
            ->get();
    }

    public function passportAccount()
    {
        return $this->hasOne(PassportAccount::class);
    }
}
