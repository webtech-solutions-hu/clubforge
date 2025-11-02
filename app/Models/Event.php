<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = [
        'organizer_id',
        'name',
        'type',
        'description',
        'location',
        'start_date',
        'end_date',
        'max_participants',
        'image',
        'status',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_user')
            ->withPivot('status', 'role', 'notes')
            ->withTimestamps();
    }

    public function confirmedParticipants(): BelongsToMany
    {
        return $this->participants()->wherePivot('status', 'confirmed');
    }

    public function pendingParticipants(): BelongsToMany
    {
        return $this->participants()->wherePivot('status', 'pending');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }

    public function isParticipant(User $user): bool
    {
        return $this->participants()->where('user_id', $user->id)->exists();
    }

    public function isConfirmedParticipant(User $user): bool
    {
        return $this->confirmedParticipants()->where('user_id', $user->id)->exists();
    }

    public function isFull(): bool
    {
        if (!$this->max_participants) {
            return false;
        }

        return $this->confirmedParticipants()->count() >= $this->max_participants;
    }
}
