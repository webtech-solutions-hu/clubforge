<x-filament-panels::page>
    @php
        $user = auth()->user();
    @endphp

    {{-- Profile Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-6">
            <x-user-avatar :user="$user" size="2xl" :ring="true" />
            <div class="flex-1">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h2>
                <p class="text-lg text-gray-600 dark:text-gray-400 mt-1">{{ $user->email }}</p>
                @if($user->roles->isNotEmpty())
                    <div class="flex flex-wrap gap-2 mt-3">
                        @foreach($user->roles as $role)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200">
                                {{ $role->name }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Profile Information --}}
    <div class="grid grid-cols-1 gap-6">
        {{-- Contact Information --}}
        @if($user->mobile || $user->city || $user->address)
            <x-filament::section>
                <x-slot name="heading">
                    Contact Information
                </x-slot>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if($user->mobile)
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Mobile</h4>
                            <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $user->mobile }}</p>
                        </div>
                    @endif

                    @if($user->city)
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">City</h4>
                            <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $user->city }}</p>
                        </div>
                    @endif

                    @if($user->address)
                        <div class="md:col-span-2">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Address</h4>
                            <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $user->address }}</p>
                        </div>
                    @endif
                </div>
            </x-filament::section>
        @endif

        {{-- Social Media Links --}}
        @if($user->social_media_links && count($user->social_media_links) > 0)
            <x-filament::section>
                <x-slot name="heading">
                    Social Media
                </x-slot>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($user->social_media_links as $link)
                        <a href="{{ $link['url'] }}" target="_blank" class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <span class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-full bg-amber-100 dark:bg-amber-900">
                                @if($link['platform'] === 'facebook')
                                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                @elseif($link['platform'] === 'twitter')
                                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                @elseif($link['platform'] === 'instagram')
                                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/></svg>
                                @elseif($link['platform'] === 'discord')
                                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="currentColor" viewBox="0 0 24 24"><path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028 14.09 14.09 0 0 0 1.226-1.994.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"/></svg>
                                @else
                                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                @endif
                            </span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white capitalize">{{ str_replace('-', ' ', $link['platform']) }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $link['url'] }}</p>
                            </div>
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>
                    @endforeach
                </div>
            </x-filament::section>
        @endif

        {{-- Biography --}}
        @if($user->bio)
            <x-filament::section>
                <x-slot name="heading">
                    About
                </x-slot>

                <div class="prose dark:prose-invert max-w-none">
                    <p class="text-gray-900 dark:text-white whitespace-pre-wrap">{{ $user->bio }}</p>
                </div>
            </x-filament::section>
        @endif

        {{-- Account Information --}}
        <x-filament::section>
            <x-slot name="heading">
                Account Information
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Member Since</h4>
                    <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $user->created_at->format('F j, Y') }}</p>
                </div>

                @if($user->email_verified_at)
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Email Verified</h4>
                        <p class="mt-1 text-base text-gray-900 dark:text-white">
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Yes
                            </span>
                        </p>
                    </div>
                @endif
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
