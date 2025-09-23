<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
        'is_encrypted'
    ];

    protected $casts = [
        'is_encrypted' => 'boolean',
    ];

    /**
     * Get setting value with automatic decryption if needed
     */
    public function getValueAttribute($value)
    {
        if ($this->is_encrypted && $value) {
            try {
                return Crypt::decryptString($value);
            } catch (\Exception $e) {
                return $value;
            }
        }

        return $value;
    }

    /**
     * Set setting value with automatic encryption if needed
     */
    public function setValueAttribute($value)
    {
        if ($this->is_encrypted && $value) {
            $this->attributes['value'] = Crypt::encryptString($value);
        } else {
            $this->attributes['value'] = $value;
        }
    }

    /**
     * Get setting by key
     */
    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set setting value
     */
    public static function set($key, $value, $type = 'string', $description = null, $isEncrypted = false)
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'description' => $description,
                'is_encrypted' => $isEncrypted
            ]
        );
    }

    /**
     * Get all WaboxApp settings
     */
    public static function getWaboxSettings()
    {
        return [
            'wabox_token' => static::get('wabox_token'),
            'wabox_uid' => static::get('wabox_uid'),
            'wabox_api_url' => static::get('wabox_api_url', 'https://www.waboxapp.com/api'),
            'wabox_broadcast_delay' => static::get('wabox_broadcast_delay', '3'),
            'wabox_batch_size' => static::get('wabox_batch_size', '50'),
        ];
    }
}
