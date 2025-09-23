<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BroadcastLog extends Model
{
    protected $fillable = [
        'message',
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
}
