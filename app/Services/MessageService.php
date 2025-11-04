<?php

namespace App\Services;

use App\Models\Message;
use App\Models\User;

class MessageService
{
    /**
     * Create a message for a user (system notification)
     */
    public static function create(
        User|int $user,
        string $category,
        string $title,
        string $message,
        ?string $icon = null,
        ?string $iconColor = 'gray',
        ?string $actionUrl = null,
        ?array $data = null,
        ?string $priority = 'normal'
    ): Message {
        $userId = $user instanceof User ? $user->id : $user;

        return Message::create([
            'user_id' => $userId,
            'recipient_type' => 'user',
            'category' => $category,
            'title' => $title,
            'message' => $message,
            'priority' => $priority,
            'icon' => $icon,
            'icon_color' => $iconColor,
            'action_url' => $actionUrl,
            'data' => $data,
        ]);
    }

    /**
     * Notify user about a new post like
     */
    public static function postLiked(User $user, $post, User $likedBy): Message
    {
        return self::create(
            user: $user,
            category: 'post_liked',
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
    public static function postCommented(User $user, $post, User $commentedBy, string $commentText): Message
    {
        return self::create(
            user: $user,
            category: 'post_commented',
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
    public static function eventReminder(User $user, $event): Message
    {
        return self::create(
            user: $user,
            category: 'event_reminder',
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
    public static function eventParticipationConfirmed(User $user, $event): Message
    {
        return self::create(
            user: $user,
            category: 'event_participation_confirmed',
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
    public static function roleAssigned(User $user, string $roleName): Message
    {
        return self::create(
            user: $user,
            category: 'role_assigned',
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
    public static function mentioned(User $user, User $mentionedBy, string $context, ?string $url = null): Message
    {
        return self::create(
            user: $user,
            category: 'mentioned',
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
    public static function welcome(User $user): Message
    {
        return self::create(
            user: $user,
            category: 'welcome',
            title: 'Welcome to Club Forge!',
            message: "We're excited to have you join our gaming community. Start by exploring the message board and upcoming events!",
            icon: 'heroicon-o-hand-raised',
            iconColor: 'amber',
            actionUrl: route('filament.admin.pages.dashboard'),
        );
    }

    /**
     * Notify user about profile update
     */
    public static function profileUpdated(User $user): Message
    {
        return self::create(
            user: $user,
            category: 'profile_updated',
            title: 'Profile Updated',
            message: 'Your profile has been successfully updated',
            icon: 'heroicon-o-check-circle',
            iconColor: 'green',
            actionUrl: route('filament.admin.pages.profile'),
        );
    }

    /**
     * Notify user about post creation
     */
    public static function postCreated(User $user, $post): Message
    {
        return self::create(
            user: $user,
            category: 'post_created',
            title: 'Post Created',
            message: 'Your post has been successfully created',
            icon: 'heroicon-o-check-circle',
            iconColor: 'green',
            actionUrl: route('filament.admin.resources.posts.view', ['record' => $post->id]),
            data: [
                'post_id' => $post->id,
            ]
        );
    }

    /**
     * Notify user about post deletion
     */
    public static function postDeleted(User $user): Message
    {
        return self::create(
            user: $user,
            category: 'post_deleted',
            title: 'Post Deleted',
            message: 'Your post has been successfully deleted',
            icon: 'heroicon-o-trash',
            iconColor: 'red',
        );
    }

    /**
     * Notify multiple users
     */
    public static function notifyMultiple(
        array $userIds,
        string $category,
        string $title,
        string $message,
        ?string $icon = null,
        ?string $iconColor = 'gray',
        ?string $actionUrl = null,
        ?array $data = null,
        ?string $priority = 'normal'
    ): void {
        foreach ($userIds as $userId) {
            self::create(
                user: $userId,
                category: $category,
                title: $title,
                message: $message,
                icon: $icon,
                iconColor: $iconColor,
                actionUrl: $actionUrl,
                data: $data,
                priority: $priority
            );
        }
    }
}
