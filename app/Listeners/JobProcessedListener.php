<?php

namespace App\Listeners;

use App\Models\SuccessJob;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Str;

class JobProcessedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(JobProcessed $event): void
    {
        try {
            $payload = json_decode($event->job->getRawBody(), true);

            // Extract job information
            $jobName = 'Unknown Job';
            if (isset($payload['displayName'])) {
                $jobName = $payload['displayName'];
            } elseif (isset($payload['data']['commandName'])) {
                $jobName = class_basename($payload['data']['commandName']);
            }

            // Calculate execution time (in milliseconds)
            $executionTime = microtime(true) - LARAVEL_START;
            $executionTimeMs = (int) ($executionTime * 1000);

            // Store successful job
            SuccessJob::create([
                'uuid' => (string) Str::uuid(),
                'job_name' => $jobName,
                'queue' => $event->job->getQueue() ?? 'default',
                'connection' => $event->connectionName,
                'payload' => $payload,
                'execution_time' => $executionTimeMs,
                'completed_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Silently fail to avoid breaking the queue worker
            \Log::error('Failed to log successful job: ' . $e->getMessage());
        }
    }
}
