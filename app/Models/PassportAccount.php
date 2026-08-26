<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PassportAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'username',
        'password',
        'is_email',
    ];

    protected $casts = [
        'is_email' => 'boolean',
        'password' => 'encrypted',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
