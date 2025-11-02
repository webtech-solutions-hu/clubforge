<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Create a notification for a user
     */
    public static function create(
        User|int $user,
        string $type,
        string $title,
        string $message,
        ?string $icon = null,
        ?string $iconColor = 'gray',
        ?string $actionUrl = null,
        ?array $data = null
    ): Notification {
        $userId = $user instanceof User ? $user->id : $user;

        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'icon' => $icon,
            'icon_color' => $iconColor,
            'action_url' => $actionUrl,
            'data' => $data,
        ]);
    }

    /**
     * Notify user about a new post like
     */
    public static function postLiked(User $user, $post, User $likedBy): Notification
    {
        return self::create(
            user: $user,
            type: 'post_liked',
            title: 'Someone liked your post',
            message: "{$likedBy->name} liked your post",
            icon: 'heroicon-o-heart',
            iconColor: 'red',
            actionUrl: route('filament.admin.resources.posts.view', ['record' => $post->id]),
            data: [
                'post_id' => $post->id,
                'liked_by' => $likedBy->id,
            ]
        );
    }

    /**
     * Notify user about a new comment on their post
     */
    public static function postCommented(User $user, $post, User $commentedBy, string $commentText): Notification
    {
        return self::create(
            user: $user,
            type: 'post_commented',
            title: 'New comment on your post',
            message: "{$commentedBy->name} commented: " . \Illuminate\Support\Str::limit($commentText, 50),
            icon: 'heroicon-o-chat-bubble-left',
            iconColor: 'blue',
            actionUrl: route('filament.admin.resources.posts.view', ['record' => $post->id]),
            data: [
                'post_id' => $post->id,
                'commented_by' => $commentedBy->id,
            ]
        );
    }

    /**
     * Notify user about an upcoming event
     */
    public static function eventReminder(User $user, $event): Notification
    {
        return self::create(
            user: $user,
            type: 'event_reminder',
            title: 'Upcoming Event',
            message: "Event '{$event->name}' is starting soon!",
            icon: 'heroicon-o-calendar',
            iconColor: 'amber',
            actionUrl: route('filament.admin.resources.events.view', ['record' => $event->id]),
            data: [
                'event_id' => $event->id,
            ]
        );
    }

    /**
     * Notify user about event participation confirmation
     */
    public static function eventParticipationConfirmed(User $user, $event): Notification
    {
        return self::create(
            user: $user,
            type: 'event_participation_confirmed',
            title: 'Event Participation Confirmed',
            message: "You're confirmed for '{$event->name}'",
            icon: 'heroicon-o-check-circle',
            iconColor: 'green',
            actionUrl: route('filament.admin.resources.events.view', ['record' => $event->id]),
            data: [
                'event_id' => $event->id,
            ]
        );
    }

    /**
     * Notify user about role assignment
     */
    public static function roleAssigned(User $user, string $roleName): Notification
    {
        return self::create(
            user: $user,
            type: 'role_assigned',
            title: 'New Role Assigned',
            message: "You've been assigned the role: {$roleName}",
            icon: 'heroicon-o-shield-check',
            iconColor: 'purple',
            actionUrl: route('filament.admin.pages.profile'),
            data: [
                'role' => $roleName,
            ]
        );
    }

    /**
     * Notify user about a mention in a post or comment
     */
    public static function mentioned(User $user, User $mentionedBy, string $context, ?string $url = null): Notification
    {
        return self::create(
            user: $user,
            type: 'mentioned',
            title: 'You were mentioned',
            message: "{$mentionedBy->name} mentioned you in {$context}",
            icon: 'heroicon-o-at-symbol',
            iconColor: 'indigo',
            actionUrl: $url,
            data: [
                'mentioned_by' => $mentionedBy->id,
            ]
        );
    }

    /**
     * Welcome notification for new users
     */
    public static function welcome(User $user): Notification
    {
        return self::create(
            user: $user,
            type: 'welcome',
            title: 'Welcome to Club Forge!',
            message: "We're excited to have you join our gaming community. Start by exploring the message board and upcoming events!",
            icon: 'heroicon-o-hand-raised',
            iconColor: 'amber',
            actionUrl: route('filament.admin.pages.dashboard'),
        );
    }

    /**
     * Notify multiple users
     */
    public static function notifyMultiple(
        array $userIds,
        string $type,
        string $title,
        string $message,
        ?string $icon = null,
        ?string $iconColor = 'gray',
        ?string $actionUrl = null,
        ?array $data = null
    ): void {
        foreach ($userIds as $userId) {
            self::create(
                user: $userId,
                type: $type,
                title: $title,
                message: $message,
                icon: $icon,
                iconColor: $iconColor,
                actionUrl: $actionUrl,
                data: $data
            );
        }
    }
}
