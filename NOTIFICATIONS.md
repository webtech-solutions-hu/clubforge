# Notifications System

## Overview

Club Forge features a comprehensive, real-time notifications system that keeps users informed about all important activities and events within their gaming community.

## Features

### 🔔 **Notification Bell in Header**
- **Visual Badge**: Displays unread notification count with an amber badge
- **Dropdown Panel**: Click the bell icon to view recent notifications
- **Real-time Updates**: Notifications refresh automatically using Livewire
- **Responsive Design**: Optimized for both desktop and mobile devices

### 📋 **Notification Types**

The system supports various notification types:

1. **Post Interactions**
   - `post_liked` - When someone likes your post
   - `post_commented` - When someone comments on your post

2. **Event Notifications**
   - `event_reminder` - Upcoming event reminders
   - `event_participation_confirmed` - Event participation confirmation

3. **System Notifications**
   - `role_assigned` - New role assignments
   - `mentioned` - When you're mentioned in a post/comment
   - `welcome` - Welcome message for new users
   - `announcement` - Important announcements

### ✨ **Key Features**

- **Mark as Read/Unread**: Individual notification management
- **Mark All as Read**: Bulk action for all unread notifications
- **Delete Notifications**: Remove unwanted notifications
- **Action URLs**: Click notifications to navigate to relevant content
- **Rich Content**: Icons, colors, and timestamps for better UX
- **Empty State**: Helpful message when no notifications exist

## Database Schema

### Notifications Table

```sql
- id (bigint, primary key)
- user_id (foreign key to users)
- type (string) - Notification type
- title (string) - Notification title
- message (text) - Notification message
- icon (string, nullable) - Heroicon name
- icon_color (string) - Icon color (amber, blue, red, etc.)
- action_url (string, nullable) - URL to navigate on click
- data (json, nullable) - Additional metadata
- read_at (timestamp, nullable) - When notification was read
- created_at (timestamp)
- updated_at (timestamp)
```

**Indexes:**
- `(user_id, read_at)` - Optimized for unread queries
- `(user_id, created_at)` - Optimized for recent notifications

## Usage

### Creating Notifications

#### Using NotificationService (Recommended)

```php
use App\Services\NotificationService;
use App\Models\User;

// Simple notification
NotificationService::create(
    user: $user,
    type: 'announcement',
    title: 'New Feature Available',
    message: 'Check out our new notifications system!',
    icon: 'heroicon-o-sparkles',
    iconColor: 'purple',
    actionUrl: '/admin/dashboard'
);

// Pre-built notification methods
NotificationService::welcome($user);
NotificationService::postLiked($user, $post, $likedBy);
NotificationService::postCommented($user, $post, $commentedBy, $commentText);
NotificationService::eventReminder($user, $event);
NotificationService::roleAssigned($user, $roleName);
NotificationService::mentioned($user, $mentionedBy, 'a post');
```

#### Notify Multiple Users

```php
NotificationService::notifyMultiple(
    userIds: [1, 2, 3, 4],
    type: 'announcement',
    title: 'Server Maintenance',
    message: 'The server will be down for maintenance tonight at 10 PM.',
    icon: 'heroicon-o-wrench',
    iconColor: 'amber'
);
```

#### Direct Model Usage

```php
use App\Models\Notification;

Notification::create([
    'user_id' => $user->id,
    'type' => 'custom',
    'title' => 'Custom Notification',
    'message' => 'Your custom message here',
    'icon' => 'heroicon-o-bell',
    'icon_color' => 'blue',
]);
```

### Querying Notifications

```php
// Get user's notifications
$notifications = $user->notifications;

// Get unread notifications
$unread = $user->unreadNotifications;

// Get unread count
$count = $user->unreadNotificationsCount();

// Query builder examples
$recent = Notification::forUser($user->id)
    ->recent(10)
    ->get();

$unread = Notification::forUser($user->id)
    ->unread()
    ->get();

$read = Notification::forUser($user->id)
    ->read()
    ->get();
```

### Managing Notifications

```php
// Mark as read
$notification->markAsRead();

// Mark as unread
$notification->markAsUnread();

// Check status
if ($notification->isUnread()) {
    // Do something
}

// Mark all user notifications as read
Notification::forUser($user->id)
    ->unread()
    ->update(['read_at' => now()]);
```

## Integration Examples

### In Controllers

```php
use App\Services\NotificationService;

class PostController extends Controller
{
    public function like(Post $post)
    {
        $post->likes()->create([
            'user_id' => auth()->id()
        ]);

        // Notify post author
        if ($post->user_id !== auth()->id()) {
            NotificationService::postLiked(
                user: $post->user,
                post: $post,
                likedBy: auth()->user()
            );
        }

        return back();
    }
}
```

### In Observers

```php
use App\Services\NotificationService;

class PostObserver
{
    public function created(Post $post)
    {
        // Notify followers
        $followers = $post->user->followers;

        foreach ($followers as $follower) {
            NotificationService::create(
                user: $follower,
                type: 'new_post',
                title: 'New Post from ' . $post->user->name,
                message: $post->title,
                icon: 'heroicon-o-document-text',
                iconColor: 'blue',
                actionUrl: route('posts.show', $post)
            );
        }
    }
}
```

### In Jobs/Commands

```php
use App\Services\NotificationService;
use App\Models\Event;

class SendEventReminders extends Command
{
    public function handle()
    {
        $upcomingEvents = Event::where('starts_at', '>', now())
            ->where('starts_at', '<', now()->addHours(2))
            ->get();

        foreach ($upcomingEvents as $event) {
            foreach ($event->participants as $participant) {
                NotificationService::eventReminder($participant, $event);
            }
        }
    }
}
```

## Livewire Component

The notification bell is powered by a Livewire component located at:
- **Component**: `app/Livewire/NotificationBell.php`
- **View**: `resources/views/livewire/notification-bell.blade.php`

### Component Methods

- `loadNotifications()` - Refresh notifications list
- `toggleDropdown()` - Show/hide dropdown
- `markAsRead($id)` - Mark single notification as read
- `markAllAsRead()` - Mark all notifications as read
- `deleteNotification($id)` - Delete a notification

### Events

```php
// Dispatch event to refresh notifications
$this->dispatch('notification-created');
```

## Customization

### Custom Notification Types

Add new notification types in `NotificationService.php`:

```php
public static function customNotification(User $user, $data): Notification
{
    return self::create(
        user: $user,
        type: 'custom_type',
        title: 'Custom Title',
        message: 'Custom message with data',
        icon: 'heroicon-o-custom-icon',
        iconColor: 'custom-color',
        actionUrl: '/custom/url',
        data: $data
    );
}
```

### Styling

The notification dropdown uses Tailwind CSS. Customize colors in:
- `resources/views/livewire/notification-bell.blade.php`

### Icons

Icons use Heroicons. Available icons:
- `heroicon-o-bell` - Bell
- `heroicon-o-heart` - Like
- `heroicon-o-chat-bubble-left` - Comment
- `heroicon-o-calendar` - Event
- `heroicon-o-trophy` - Achievement
- `heroicon-o-sparkles` - Special
- And many more at [heroicons.com](https://heroicons.com)

### Icon Colors

Supported Tailwind colors:
- `amber`, `blue`, `red`, `green`, `purple`, `indigo`, `pink`, `gray`

## Performance Considerations

1. **Indexes**: The notifications table has indexes on frequently queried columns
2. **Pagination**: Only 10 most recent notifications are loaded by default
3. **Eager Loading**: Use `with('user')` when querying multiple notifications
4. **Cleanup**: Consider implementing a job to delete old read notifications

## Testing

### Seed Sample Notifications

```bash
docker exec clubforge-php-fpm-1 php artisan db:seed --class=NotificationSeeder
```

This creates 6 sample notifications for each user:
- Welcome notification
- Event reminder
- Post liked
- Post commented
- System notification
- Feature announcement

## API Endpoints (Future Enhancement)

Consider adding API endpoints for mobile apps:

```php
// Get notifications
GET /api/notifications

// Mark as read
POST /api/notifications/{id}/read

// Mark all as read
POST /api/notifications/read-all

// Delete notification
DELETE /api/notifications/{id}
```

## Best Practices

1. **Don't Spam**: Only send meaningful notifications
2. **Batch Operations**: Use `notifyMultiple()` for bulk notifications
3. **Context URLs**: Always provide actionable URLs when possible
4. **Clear Messages**: Keep titles short, messages descriptive
5. **Appropriate Icons**: Use consistent icons for notification types
6. **Color Coding**: Use colors to indicate importance/type

## Troubleshooting

### Notifications not appearing?

1. Check if migration ran: `php artisan migrate:status`
2. Clear cache: `php artisan optimize:clear`
3. Check Livewire is working: Look for `wire:` attributes in browser
4. Verify user has notifications: Check database

### Badge count not updating?

1. The component uses Livewire's `@entangle` directive
2. Ensure Alpine.js is loaded (comes with Filament)
3. Check browser console for JavaScript errors

### Dropdown not closing?

1. Verify Alpine.js `@click.away` directive is working
2. Check for z-index conflicts
3. Clear browser cache

## Future Enhancements

- [ ] Browser push notifications
- [ ] Email digest of notifications
- [ ] Notification preferences/settings
- [ ] Notification categories/filtering
- [ ] Sound alerts for new notifications
- [ ] Real-time updates with broadcasting
- [ ] Notification history page
- [ ] Bulk delete/archive options

## Credits

Built with:
- Laravel 12
- Filament 3.3
- Livewire 3
- Alpine.js
- Tailwind CSS 4
- Heroicons
