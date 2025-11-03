<div x-data="{
    open: @entangle('showDropdown'),
    init() {
        this.$watch('open', value => {
            if (value) {
                this.$nextTick(() => {
                    const dropdown = this.$refs.dropdown;
                    const button = this.$refs.button;
                    const buttonRect = button.getBoundingClientRect();
                    const dropdownWidth = Math.min(384, window.innerWidth - 16);

                    // Position dropdown below button
                    dropdown.style.top = `${buttonRect.bottom + 8}px`;

                    // Ensure dropdown doesn't overflow right edge
                    const rightSpace = window.innerWidth - buttonRect.right;
                    if (rightSpace >= dropdownWidth) {
                        // Enough space on the right, align to right of button
                        dropdown.style.right = `${window.innerWidth - buttonRect.right}px`;
                        dropdown.style.left = 'auto';
                    } else {
                        // Not enough space, align to right edge of viewport with margin
                        dropdown.style.right = '8px';
                        dropdown.style.left = 'auto';
                    }
                });
            }
        });
    }
}">
    <!-- Notification Bell Button -->
    <button
        x-ref="button"
        @click="open = !open"
        type="button"
        class="relative p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
        aria-label="Notifications"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>

        @if($unreadCount > 0)
            <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-amber-500 text-[10px] font-bold text-white">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Dropdown Panel -->
    <div
        x-ref="dropdown"
        x-show="open"
        @click.away="open = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed w-96 max-w-[calc(100vw-1rem)] origin-top-right rounded-xl bg-white dark:bg-gray-800 shadow-2xl ring-1 ring-black ring-opacity-5 focus:outline-none z-[60]"
        style="display: none;"
        x-cloak
    >
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Notifications</h3>
                @if($unreadCount > 0)
                    <button
                        wire:click="markAllAsRead"
                        class="text-sm text-amber-600 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300 font-medium"
                    >
                        Mark all read
                    </button>
                @endif
            </div>
        </div>

        <div class="max-h-96 overflow-y-auto">
            @forelse($notifications as $notification)
                <div
                    wire:key="notification-{{ $notification['id'] }}"
                    class="border-b border-gray-100 dark:border-gray-700 last:border-b-0 transition-all duration-300 {{ $notification['read_at'] ? 'opacity-60' : '' }}"
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                >
                    <div class="p-4 flex gap-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 group">
                        <!-- Icon -->
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-{{ $notification['icon_color'] ?? 'gray' }}-100 dark:bg-{{ $notification['icon_color'] ?? 'gray' }}-900/30 flex items-center justify-center">
                                @if($notification['icon'])
                                    <x-dynamic-component :component="$notification['icon']" class="w-5 h-5 text-{{ $notification['icon_color'] ?? 'gray' }}-600 dark:text-{{ $notification['icon_color'] ?? 'gray' }}-400" />
                                @else
                                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @endif
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            @if($notification['action_url'])
                                <a
                                    href="{{ $notification['action_url'] }}"
                                    wire:click="markAsRead({{ $notification['id'] }})"
                                    class="block"
                                >
                            @endif
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ $notification['title'] }}
                                        </p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            {{ $notification['message'] }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">
                                            {{ \Carbon\Carbon::parse($notification['created_at'])->diffForHumans() }}
                                        </p>
                                    </div>

                                    <!-- Unread Indicator -->
                                    @if(!$notification['read_at'])
                                        <div class="w-2 h-2 bg-amber-500 rounded-full flex-shrink-0 mt-1"></div>
                                    @endif
                                </div>
                            @if($notification['action_url'])
                                </a>
                            @endif

                            <!-- Actions -->
                            <div class="flex items-center gap-2 mt-3" onclick="event.stopPropagation();">
                                @if(!$notification['read_at'])
                                    <button
                                        type="button"
                                        wire:click="markAsRead({{ $notification['id'] }})"
                                        onclick="event.stopPropagation();"
                                        class="p-1.5 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700 transition-colors"
                                        title="Mark as read"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>
                                @endif

                                <button
                                    type="button"
                                    wire:click="deleteNotification({{ $notification['id'] }})"
                                    onclick="event.stopPropagation();"
                                    class="p-1.5 rounded-lg text-red-600 hover:text-red-700 hover:bg-red-50 dark:text-red-400 dark:hover:text-red-300 dark:hover:bg-red-900/20 transition-colors"
                                    title="Delete"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <p class="text-sm text-gray-600 dark:text-gray-400">No notifications yet</p>
                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">We'll notify you when something happens</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
