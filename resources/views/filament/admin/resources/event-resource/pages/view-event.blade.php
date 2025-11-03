<x-filament-panels::page>
    @php
        $event = $this->record;
        $isParticipant = $event->isParticipant(auth()->user());
        $isOrganizer = auth()->id() === $event->organizer_id;
        $confirmedCount = $event->confirmedParticipants()->count();
        $pendingCount = $event->pendingParticipants()->count();
    @endphp

    {{-- Event Header --}}
    <div class="mb-6">
        @if($event->image)
            <div class="mb-6 rounded-xl overflow-hidden" style="aspect-ratio: 16/10;">
                <img src="{{ Storage::disk('public')->url($event->image) }}" alt="{{ $event->name }}" class="w-full h-full object-cover">
            </div>
        @endif

        <div class="flex items-start justify-between gap-6">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-3">
                    <h1 class="text-4xl font-bold text-gray-900 dark:text-white">{{ $event->name }}</h1>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        @if($event->status === 'upcoming') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                        @elseif($event->status === 'ongoing') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                        @elseif($event->status === 'completed') bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                        @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                        @endif">
                        {{ ucfirst($event->status) }}
                    </span>
                </div>

                <div class="flex flex-wrap items-center gap-4 text-gray-600 dark:text-gray-400">
                    {{-- Event Type --}}
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        <span class="font-medium capitalize">{{ str_replace('-', ' ', $event->type) }}</span>
                    </div>

                    {{-- Date & Time --}}
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>{{ $event->start_date->format('F j, Y \a\t g:i A') }}</span>
                    </div>

                    @if($event->location)
                        {{-- Location --}}
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>{{ $event->location }}</span>
                        </div>
                    @endif

                    {{-- Organizer --}}
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span>Organized by <strong>{{ $event->organizer->name }}</strong></span>
                    </div>
                </div>

                {{-- Participation Status --}}
                @if($isParticipant)
                    <div class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-amber-100 dark:bg-amber-900/30">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm font-medium text-amber-800 dark:text-amber-200">
                            @if($isOrganizer)
                                You are the organizer
                            @else
                                You are participating in this event
                            @endif
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Description --}}
            @if($event->description)
                <x-filament::section>
                    <x-slot name="heading">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                            </svg>
                            About This Event
                        </div>
                    </x-slot>

                    <div class="prose dark:prose-invert max-w-none">
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $event->description }}</p>
                    </div>
                </x-filament::section>
            @endif

            {{-- Participants Section --}}
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Participants
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                {{ $confirmedCount }} Confirmed
                            </span>
                            @if($pendingCount > 0)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    {{ $pendingCount }} Pending
                                </span>
                            @endif
                        </div>
                    </div>
                </x-slot>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($event->confirmedParticipants as $participant)
                        <div class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                            <x-user-avatar :user="$participant" size="md" />
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $participant->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 capitalize">{{ $participant->pivot->role }}</p>
                            </div>
                            @if($participant->id === $event->organizer_id)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200">
                                    Organizer
                                </span>
                            @endif
                        </div>
                    @empty
                        <div class="col-span-2 text-center py-8 text-gray-500 dark:text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <p class="text-sm">No confirmed participants yet</p>
                        </div>
                    @endforelse
                </div>
            </x-filament::section>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Event Details Card --}}
            <x-filament::section>
                <x-slot name="heading">
                    Event Details
                </x-slot>

                <div class="space-y-4">
                    {{-- Start Date --}}
                    <div>
                        <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Start Date & Time</h4>
                        <p class="text-sm text-gray-900 dark:text-white font-medium">{{ $event->start_date->format('l, F j, Y') }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $event->start_date->format('g:i A') }}</p>
                    </div>

                    {{-- End Date --}}
                    @if($event->end_date)
                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                            <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">End Date & Time</h4>
                            <p class="text-sm text-gray-900 dark:text-white font-medium">{{ $event->end_date->format('l, F j, Y') }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $event->end_date->format('g:i A') }}</p>
                        </div>
                    @endif

                    {{-- Capacity --}}
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Capacity</h4>
                        @if($event->max_participants)
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-amber-500 h-2 rounded-full" style="width: {{ min(100, ($confirmedCount / $event->max_participants) * 100) }}%"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $confirmedCount }}/{{ $event->max_participants }}</span>
                            </div>
                            @if($event->isFull())
                                <p class="text-xs text-red-600 dark:text-red-400 mt-1">Event is full</p>
                            @endif
                        @else
                            <p class="text-sm text-gray-900 dark:text-white">Unlimited</p>
                        @endif
                    </div>

                    {{-- Time Until Event --}}
                    @if($event->status === 'upcoming')
                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                            <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Time Until Event</h4>
                            <p class="text-sm text-gray-900 dark:text-white font-medium">{{ $event->start_date->diffForHumans() }}</p>
                        </div>
                    @endif
                </div>
            </x-filament::section>

            {{-- Organizer Card --}}
            <x-filament::section>
                <x-slot name="heading">
                    Organizer
                </x-slot>

                <div class="flex items-center gap-3">
                    <x-user-avatar :user="$event->organizer" size="lg" />
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $event->organizer->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $event->organizer->email }}</p>
                    </div>
                </div>
            </x-filament::section>
        </div>
    </div>
</x-filament-panels::page>
