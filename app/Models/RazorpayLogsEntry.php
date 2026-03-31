<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RazorpayLogsEntry extends Model
{
    use HasFactory;
     protected $table = 'razorpay_logs_entry';

    protected $fillable = [
        'entryfor',
        'userid',
        'orderid',
        'orderamount',
        'ordernote',
        'referenceid',
        'txstatus',
        'paymentmode',
    ];

}
