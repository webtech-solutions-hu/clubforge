@props([
    'user',
    'size' => 'md', // xs, sm, md, lg, xl
    'ring' => false,
])

@php
    $sizeClasses = match($size) {
        'xs' => 'w-8 h-8',      // 32px
        'sm' => 'w-10 h-10',    // 40px
        'md' => 'w-12 h-12',    // 48px
        'lg' => 'w-16 h-16',    // 64px
        'xl' => 'w-24 h-24',    // 96px
        '2xl' => 'w-32 h-32',   // 128px
        default => 'w-12 h-12', // 48px default
    };

    $ringClass = $ring ? 'ring-4 ring-white dark:ring-gray-900' : '';
@endphp

<div class="flex-shrink-0 {{ $sizeClasses }}">
    @if($user->avatar)
        <img
            src="{{ Storage::disk('public')->url($user->avatar) }}"
            alt="{{ $user->name }}"
            class="w-full h-full rounded-full object-cover {{ $ringClass }}"
        >
    @else
        <img
            src="{{ url('/images/default-avatar.svg') }}"
            alt="{{ $user->name }}"
            class="w-full h-full rounded-full {{ $ringClass }}"
        >
    @endif
</div>
