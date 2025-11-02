<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'causer_id',
        'event_type',
        'ip_address',
        'user_agent',
        'properties',
        'description',
    ];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function causer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'causer_id');
    }

    public static function log(string $eventType, ?User $user = null, ?array $properties = null, ?string $description = null): void
    {
        $request = request();

        static::create([
            'user_id' => $user?->id ?? auth()->id(),
            'causer_id' => auth()->id(),
            'event_type' => $eventType,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'properties' => $properties,
            'description' => $description,
        ]);
    }
}
