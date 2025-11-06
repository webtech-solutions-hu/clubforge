<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuccessJob extends Model
{
    protected $table = 'success_jobs';

    protected $fillable = [
        'uuid',
        'job_name',
        'queue',
        'connection',
        'payload',
        'execution_time',
        'completed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'completed_at' => 'datetime',
        'execution_time' => 'integer',
    ];

    public function getExecutionTimeHumanAttribute(): string
    {
        $ms = $this->execution_time;
        if ($ms < 1000) {
            return "{$ms}ms";
        }
        $seconds = round($ms / 1000, 2);
        return "{$seconds}s";
    }
}
