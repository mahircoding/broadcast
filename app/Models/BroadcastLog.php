<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BroadcastLog extends Model
{
    protected $fillable = [
        'message',
        'image_path',
        'recipients',
        'total_sent',
        'total_success',
        'total_failed',
        'status',
        'response_data'
    ];

    protected $casts = [
        'recipients' => 'array',
        'response_data' => 'array',
    ];

    /**
     * Get contacts that were recipients of this broadcast
     */
    public function contacts()
    {
        return Contact::whereIn('id', $this->recipients ?? []);
    }

    /**
     * Check if broadcast has an image
     */
    public function hasImage(): bool
    {
        return !empty($this->image_path) && file_exists(storage_path('app/public/' . $this->image_path));
    }

    /**
     * Get the full URL for the image
     */
    public function getImageUrl(): ?string
    {
        if ($this->hasImage()) {
            return asset('storage/' . $this->image_path);
        }
        return null;
    }

    /**
     * Get the full path for the image
     */
    public function getImagePath(): ?string
    {
        if ($this->hasImage()) {
            return storage_path('app/public/' . $this->image_path);
        }
        return null;
    }
}
