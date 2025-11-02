<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventInvitation extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Event $event,
        public string $role = 'player'
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('You have been invited to: ' . $this->event->name)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('You have been invited to participate in an event.')
            ->line('**Event:** ' . $this->event->name)
            ->line('**Type:** ' . ucfirst($this->event->type))
            ->line('**Role:** ' . ucfirst($this->role))
            ->line('**Start Date:** ' . $this->event->start_date->format('F j, Y g:i A'))
            ->when($this->event->location, fn ($mail) => $mail->line('**Location:** ' . $this->event->location))
            ->action('View Event Details', url('/admin/events/' . $this->event->id))
            ->line('Please confirm your participation as soon as possible.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'event_id' => $this->event->id,
            'event_name' => $this->event->name,
            'event_type' => $this->event->type,
            'role' => $this->role,
            'start_date' => $this->event->start_date->toDateTimeString(),
            'organizer_name' => $this->event->organizer->name,
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'event_id' => $this->event->id,
            'event_name' => $this->event->name,
            'role' => $this->role,
        ];
    }
}
