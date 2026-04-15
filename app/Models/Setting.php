<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group', 'description'];

    /**
     * Get the typed value of a setting
     */
    public function typedValue()
    {
        $value = $this->value;

        // Try to parse as boolean
        if (strtolower($value) === 'true' || strtolower($value) === 'false') {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        // Try to parse as integer
        if (filter_var($value, FILTER_VALIDATE_INT) !== false) {
            return (int) $value;
        }

        // Try to parse as float
        if (filter_var($value, FILTER_VALIDATE_FLOAT) !== false) {
            return (float) $value;
        }

        // Try to parse as JSON
        if (is_string($value) &&
            (substr($value, 0, 1) === '{' || substr($value, 0, 1) === '[') &&
            substr($value, -1) === '}') {
            return json_decode($value, true);
        }

        return $value;
    }

    /**
     * Set a setting value by key
     */
    public static function set(string $key, $value, string $group = 'general', ?string $description = null)
    {
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value);
        } elseif (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        } else {
            $value = strval($value);
        }

        return self::updateOrCreate(
            ['key' => $key, 'group' => $group],
            ['value' => $value, 'description' => $description]
        );
    }

    /**
     * Get a setting value by key
     */
    public static function get(string $key, $default = null, string $group = 'general')
    {
        $setting = self::where('key', $key)
            ->where('group', $group)
            ->first();

        return $setting ? $setting->typedValue() : $default;
    }

    /**
     * Get all settings for a group
     */
    public static function group(string $group = 'general'): array
    {
        $settings = self::where('group', $group)->get();
        $result = [];

        foreach ($settings as $setting) {
            $result[$setting->key] = $setting->typedValue();
        }

        return $result;
    }
}
