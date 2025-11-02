<x-filament-panels::page>
    <div class="space-y-6" wire:poll.30s>

        {{-- New Post Form --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <form wire:submit="createPost">
                {{ $this->form }}

                <div class="mt-4 flex justify-end">
                    <x-filament::button type="submit" icon="heroicon-o-paper-airplane">
                        Post
                    </x-filament::button>
                </div>
            </form>
        </div>

        {{-- Posts Feed --}}
        @foreach($this->getPosts() as $post)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">

                {{-- Post Header --}}
                <div class="p-4 flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        @if($post->user->avatar)
                            <img src="{{ asset('storage/' . $post->user->avatar) }}"
                                 alt="{{ $post->user->name }}"
                                 class="w-10 h-10 rounded-full object-cover">
                        @else
                            <div class="w-10 h-10 rounded-full bg-amber-500 flex items-center justify-center text-white font-semibold">
                                {{ strtoupper(substr($post->user->name, 0, 1)) }}
                            </div>
                        @endif

                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $post->user->name }}</span>
                                @if($post->is_pinned)
                                    <x-filament::badge color="warning" icon="heroicon-o-bookmark">
                                        Pinned
                                    </x-filament::badge>
                                @endif
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $post->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>

                    @if(auth()->id() === $post->user_id || auth()->user()->hasRole('administrator'))
                        <x-filament::dropdown placement="bottom-end">
                            <x-slot name="trigger">
                                <x-filament::icon-button icon="heroicon-o-ellipsis-vertical" />
                            </x-slot>

                            <x-filament::dropdown.list>
                                <x-filament::dropdown.list.item
                                    wire:click="deletePost({{ $post->id }})"
                                    wire:confirm="Are you sure you want to delete this post?"
                                    icon="heroicon-o-trash"
                                    color="danger">
                                    Delete Post
                                </x-filament::dropdown.list.item>
                            </x-filament::dropdown.list>
                        </x-filament::dropdown>
                    @endif
                </div>

                {{-- Post Content --}}
                <div class="px-4 pb-3">
                    @if($post->title)
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                            {{ $post->title }}
                        </h3>
                    @endif

                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $post->content }}</p>
                </div>

                {{-- Post Image --}}
                @if($post->image)
                    <div class="px-4 pb-3">
                        <img src="{{ asset('storage/' . $post->image) }}"
                             alt="Post image"
                             class="rounded-lg w-full max-h-96 object-cover">
                    </div>
                @endif

                {{-- Post Stats --}}
                <div class="px-4 py-2 border-t border-b border-gray-200 dark:border-gray-700 flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                    <span>{{ $post->likes_count }} {{ Str::plural('like', $post->likes_count) }}</span>
                    <span>{{ $post->comments_count }} {{ Str::plural('comment', $post->comments_count) }}</span>
                </div>

                {{-- Post Actions --}}
                <div class="px-4 py-2 flex items-center gap-2">
                    <x-filament::button
                        wire:click="toggleLike({{ $post->id }})"
                        color="{{ $post->likes()->where('user_id', auth()->id())->exists() ? 'warning' : 'gray' }}"
                        outlined
                        icon="heroicon-o-heart"
                        size="sm">
                        Like
                    </x-filament::button>

                    <x-filament::button
                        x-data
                        @click="$el.closest('.bg-white').querySelector('.comment-input-{{ $post->id }}').focus()"
                        color="gray"
                        outlined
                        icon="heroicon-o-chat-bubble-left"
                        size="sm">
                        Comment
                    </x-filament::button>
                </div>

                {{-- Comments Section --}}
                @if($post->comments->count() > 0)
                    <div class="px-4 pb-3 space-y-3 max-h-64 overflow-y-auto">
                        @foreach($post->comments as $comment)
                            <div class="flex gap-2">
                                @if($comment->user->avatar)
                                    <img src="{{ asset('storage/' . $comment->user->avatar) }}"
                                         alt="{{ $comment->user->name }}"
                                         class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                                @else
                                    <div class="w-8 h-8 rounded-full bg-gray-400 flex items-center justify-center text-white text-xs font-semibold flex-shrink-0">
                                        {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                    </div>
                                @endif

                                <div class="flex-1 bg-gray-100 dark:bg-gray-700 rounded-lg p-2">
                                    <div class="flex items-center justify-between">
                                        <span class="font-semibold text-sm text-gray-900 dark:text-white">
                                            {{ $comment->user->name }}
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $comment->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">{{ $comment->content }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Comment Input --}}
                <div class="px-4 pb-4 flex gap-2">
                    @if(auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                             alt="{{ auth()->user()->name }}"
                             class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                    @else
                        <div class="w-8 h-8 rounded-full bg-amber-500 flex items-center justify-center text-white text-xs font-semibold flex-shrink-0">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif

                    <form class="flex-1 flex gap-2" x-data="{ comment: '' }"
                          @submit.prevent="$wire.addComment({{ $post->id }}, comment).then(() => comment = '')">
                        <input
                            type="text"
                            x-model="comment"
                            class="comment-input-{{ $post->id }} flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white text-sm"
                            placeholder="Write a comment..."
                            required>
                        <x-filament::button type="submit" size="sm" icon="heroicon-o-paper-airplane">
                            Send
                        </x-filament::button>
                    </form>
                </div>
            </div>
        @endforeach

        @if($this->getPosts()->count() === 0)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                <x-filament::icon icon="heroicon-o-chat-bubble-left-right" class="w-12 h-12 mx-auto text-gray-400 mb-4" />
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No posts yet</h3>
                <p class="text-gray-500 dark:text-gray-400">Be the first to share something with the community!</p>
            </div>
        @endif
    </div>
</x-filament-panels::page>
