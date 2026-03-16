<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GstRecord extends Model
{
    use HasFactory;

    // Optional: Define fillable properties if you plan to use mass assignment
    protected $fillable = [
        'inv_date',
        'inv_no',
        'net_amount',
        'cgst',
        'sgst',
        'igst',
        'total_amount',
        'fullname',
        'mobile',
        'email',
        'gst_no',
        'city',
        'state',
    ];

    // Optional: Define casts for specific attributes
    protected $casts = [
        'inv_date' => 'date',
        'net_amount' => 'decimal:2',
        'cgst' => 'decimal:2',
        'sgst' => 'decimal:2',
        'igst' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    // Define the table associated with the model (optional if table name matches plural snake_case of model name)
    protected $table = 'gst_records';
}
