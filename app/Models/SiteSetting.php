<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value'];
    protected $table    = 'site_settings';

    public static function get(string $key, $default = null)
    {
        return Cache::remember("setting:{$key}", 3600, function () use ($key, $default) {
            $row = static::where('key', $key)->first();
            return $row ? $row->value : $default;
        });
    }

    public static function set(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting:{$key}");
    }

    public static function all_settings(): array
    {
        return Cache::remember('site_settings_all', 3600, function () {
            return static::all()->pluck('value', 'key')->toArray();
        });
    }
}
