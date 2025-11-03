<?php

namespace App\Filament\Admin\Resources\PostResource\Pages;

use App\Filament\Admin\Resources\PostResource;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Notifications\NewComment;
use App\Services\NotificationService;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Storage;

class MessageBoard extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = PostResource::class;

    protected static string $view = 'filament.admin.resources.post-resource.pages.message-board';

    protected static ?string $title = 'Message Board';

    protected static ?string $navigationLabel = 'Wall';

    public ?array $newPostData = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('manage')
                ->label('Manage Posts')
                ->icon('heroicon-o-cog-6-tooth')
                ->url(fn () => static::getResource()::getUrl('manage'))
                ->visible(fn () => auth()->user()?->hasRole(['administrator', 'game-master'])),
            Action::make('refresh')
                ->label('Refresh')
                ->icon('heroicon-o-arrow-path')
                ->action('$refresh'),
        ];
    }

    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Textarea::make('content')
                            ->label('What\'s on your mind?')
                            ->required()
                            ->rows(3)
                            ->placeholder('Share something with the community...')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('title')
                            ->label('Title (optional)')
                            ->maxLength(255)
                            ->placeholder('Add a title to your post'),
                        Forms\Components\FileUpload::make('image')
                            ->label('Image (optional)')
                            ->image()
                            ->directory('posts')
                            ->disk('public')
                            ->imageEditor(),
                    ])
                    ->columns(2),
            ])
            ->statePath('newPostData');
    }

    public function createPost(): void
    {
        $data = $this->form->getState();

        $post = Post::create([
            'user_id' => auth()->id(),
            'title' => $data['title'] ?? null,
            'content' => $data['content'],
            'image' => $data['image'] ?? null,
        ]);

        $this->form->fill();
        $this->newPostData = [];

        // Create notification in database
        NotificationService::postCreated(auth()->user(), $post);

        // Dispatch event to refresh notification bell
        $this->dispatch('notification-created');
        $this->dispatch('post-created');
    }

    public function toggleLike(int $postId): void
    {
        $post = Post::findOrFail($postId);
        $existing = Like::where('post_id', $postId)
            ->where('user_id', auth()->id())
            ->first();

        if ($existing) {
            $existing->delete();
        } else {
            Like::create([
                'post_id' => $postId,
                'user_id' => auth()->id(),
            ]);
        }

        $this->dispatch('like-toggled');
    }

    public function deletePost(int $postId): void
    {
        $post = Post::findOrFail($postId);

        if (auth()->id() === $post->user_id || auth()->user()->hasRole('administrator')) {
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            $post->delete();

            // Create notification in database
            NotificationService::postDeleted(auth()->user());

            // Dispatch event to refresh notification bell
            $this->dispatch('notification-created');
            $this->dispatch('post-deleted');
        }
    }

    public function addComment(int $postId, string $content): void
    {
        if (empty(trim($content))) {
            return;
        }

        $comment = Comment::create([
            'post_id' => $postId,
            'user_id' => auth()->id(),
            'content' => $content,
        ]);

        // Send notification to post author if they're not the commenter
        $post = Post::findOrFail($postId);
        if ($post->user_id !== auth()->id()) {
            $post->user->notify(new NewComment($comment, $post));
        }

        $this->dispatch('comment-added');
    }

    public function getPosts()
    {
        return Post::with(['user', 'comments.user', 'likes'])
            ->withCount(['comments', 'likes'])
            ->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
