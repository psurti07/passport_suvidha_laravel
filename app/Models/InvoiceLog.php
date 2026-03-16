<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'log_detail',
        'card_number',
        'invoice_id',
        'staff_id'
    ];
}
