<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FailedJob extends Model
{
    protected $table = 'failed_jobs';

    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'connection',
        'queue',
        'payload',
        'exception',
        'failed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'failed_at' => 'datetime',
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

    public function getExceptionMessageAttribute(): string
    {
        $lines = explode("\n", $this->exception);
        return $lines[0] ?? 'No exception message';
    }
}
