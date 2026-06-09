<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'option_key',
        'option_value'
    ];

    // public static function getValue($key)
    // {
    //     return static::where('option_key', $key)
    //         ->value('option_value');
    // }

    public static function getValue($key, $default = null)
    {
        return Cache::remember("site_option_{$key}", 3600, function () use ($key, $default) {

            return static::where('option_key', $key)
                ->value('option_value') ?? $default;

        });
    }

    // public static function setValue($key, $value)
    // {
    //     Cache::forget("site_option_{$key}");

    //     return static::updateOrCreate(
    //         ['option_key' => $key],
    //         ['option_value' => $value]
    //     );
    // }
}
