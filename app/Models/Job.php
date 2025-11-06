<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = 'jobs';

    public $timestamps = false;

    protected $fillable = [
        'queue',
        'payload',
        'attempts',
        'reserved_at',
        'available_at',
        'created_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'reserved_at' => 'integer',
        'available_at' => 'integer',
        'created_at' => 'integer',
    ];

    public function getJobNameAttribute(): string
    {
        $payload = $this->payload;
        if (isset($payload['displayName'])) {
            return $payload['displayName'];
        }
        if (isset($payload['job'])) {
            return class_basename(unserialize($payload['job']));
        }
        return 'Unknown Job';
    }

    public function getCreatedAtHumanAttribute(): string
    {
        return date('Y-m-d H:i:s', $this->created_at);
    }

    public function getAvailableAtHumanAttribute(): string
    {
        return date('Y-m-d H:i:s', $this->available_at);
    }
}
