<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewComment extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Comment $comment,
        public Post $post
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $postTitle = $this->post->title ?: 'Untitled Post';

        return (new MailMessage)
            ->subject($this->comment->user->name . ' commented on your post')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line($this->comment->user->name . ' left a comment on your post.')
            ->line('**Post:** ' . $postTitle)
            ->line('**Comment:** ' . \Illuminate\Support\Str::limit($this->comment->content, 100))
            ->action('View Post', url('/admin/posts/' . $this->post->id))
            ->line('Thank you for being an active member of our community!');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'post_id' => $this->post->id,
            'post_title' => $this->post->title,
            'comment_id' => $this->comment->id,
            'comment_content' => \Illuminate\Support\Str::limit($this->comment->content, 100),
            'commenter_id' => $this->comment->user_id,
            'commenter_name' => $this->comment->user->name,
            'commenter_avatar' => $this->comment->user->avatar,
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'post_id' => $this->post->id,
            'comment_id' => $this->comment->id,
            'commenter_name' => $this->comment->user->name,
        ];
    }
}
