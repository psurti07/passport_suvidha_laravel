<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetaKeyword extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'descriptions',
        'keywords',
    ];
}
