<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoices extends Model
{
    use HasFactory;
    protected $table = "invoices";  
    protected $fillable = [
        'id',
        'customer_id',
        'card_id',
        'inv_date',
        'inv_no',
        'net_amount',
        'cgst',
        'sgst',
        'igst',
        'total_amount',
        'created_at',
        'updated_at',
        'deleted_at',
        'service_id',
    ];
}
