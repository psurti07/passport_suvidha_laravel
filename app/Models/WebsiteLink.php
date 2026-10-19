<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteLink extends Model
{
    use HasFactory;
    protected $fillable = [
        'link_type',
        'title',
        'link',
        'short_link',
        'status',
    ];
}
