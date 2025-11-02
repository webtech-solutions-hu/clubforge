<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Result extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',
        'score',
        'ranking',
        'experience_points',
        'narrative_outcome',
        'achievements',
        'notes',
    ];

    protected $casts = [
        'achievements' => 'array',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getRankingBadgeAttribute(): string
    {
        return match ($this->ranking) {
            1 => '🥇 1st',
            2 => '🥈 2nd',
            3 => '🥉 3rd',
            default => $this->ranking ? "#{$this->ranking}" : '—',
        };
    }
}
