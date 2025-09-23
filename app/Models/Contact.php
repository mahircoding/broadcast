<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'name',
        'phone_number',
        'email',
        'notes',
        'group',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Format phone number to include country code if not present
     */
    public function getFormattedPhoneAttribute()
    {
        $phone = $this->phone_number;

        // Remove any non-numeric characters except +
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        // If doesn't start with +, assume Indonesian number
        if (!str_starts_with($phone, '+')) {
            // Remove leading 0 if present and add +62
            $phone = '+62' . ltrim($phone, '0');
        }

        return $phone;
    }

    /**
     * Scope to get active contacts only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by group
     */
    public function scopeByGroup($query, $group)
    {
        return $query->where('group', $group);
    }
}
