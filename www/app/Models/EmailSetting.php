<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailSetting extends Model
{
    protected $table = 'email_settings';
    
    protected $fillable = ['key', 'value'];

    /**
     * Helper to retrieve a config value by key efficiently.
     */
    public static function getVal(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }
}
