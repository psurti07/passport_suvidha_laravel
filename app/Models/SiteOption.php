<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'option_key',
        'option_value'
    ];

    public static function getValue($key)
    {
        return static::where('option_key', $key)
            ->value('option_value');
    }
}
