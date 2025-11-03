<?php

namespace App\Livewire;

use App\Models\Notification;
use Livewire\Component;
use Livewire\Attributes\On;

class NotificationBell extends Component
{
    public $unreadCount = 0;
    public $notifications = [];
    public $showDropdown = false;

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $user = auth()->user();

        $this->notifications = Notification::forUser($user->id)
            ->recent(10)
            ->get()
            ->toArray();

        $this->unreadCount = Notification::forUser($user->id)
            ->unread()
            ->count();
    }

    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;
    }

    public function markAsRead($notificationId)
    {
        $notification = Notification::find($notificationId);

        if ($notification && $notification->user_id === auth()->id()) {
            $notification->markAsRead();

            // Update the specific notification in the array
            foreach ($this->notifications as $key => $notif) {
                if ($notif['id'] === $notificationId) {
                    $this->notifications[$key]['read_at'] = now()->toDateTimeString();
                    break;
                }
            }

            $this->unreadCount = Notification::forUser(auth()->id())
                ->unread()
                ->count();
        }
    }

    public function markAllAsRead()
    {
        Notification::forUser(auth()->id())
            ->unread()
            ->update(['read_at' => now()]);

        $this->loadNotifications();
    }

    public function deleteNotification($notificationId)
    {
        $notification = Notification::find($notificationId);

        if ($notification && $notification->user_id === auth()->id()) {
            $notification->delete();

            // Remove the notification from the array without reloading
            $this->notifications = array_values(
                array_filter($this->notifications, function($notif) use ($notificationId) {
                    return $notif['id'] !== $notificationId;
                })
            );

            $this->unreadCount = Notification::forUser(auth()->id())
                ->unread()
                ->count();
        }
    }

    #[On('notification-created')]
    public function refreshNotifications()
    {
        $this->loadNotifications();
    }

    public function render()
    {
        return view('livewire.notification-bell');
    }
}
