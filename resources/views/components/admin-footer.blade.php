@php
    $version = config('version.full')();
    $stage = config('version.stage');
    $codename = config('version.codename');

    $badgeColor = match($stage) {
        'alpha' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
        'beta' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
        'stable' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
    };
@endphp

<footer class="fi-footer mt-auto border-t border-gray-200 py-4 dark:border-white/10">
    <div class="fi-container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-2 text-sm text-gray-600 dark:text-gray-400">
            {{-- Creator Info --}}
            <span class="flex items-center gap-1.5">
                Created by
                <a
                    href="https://webtech-solutions.hu"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="font-semibold text-primary-600 transition hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300"
                >
                    Webtech-Solutions
                </a>
            </span>

            {{-- Separator --}}
            <span class="text-gray-400 dark:text-gray-600">•</span>

            {{-- Version Info --}}
            <span class="flex items-center gap-2">
                <span>Version</span>
                <span class="font-mono font-semibold text-gray-900 dark:text-white">
                    {{ $version }}
                </span>
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $badgeColor }}">
                    {{ ucfirst($stage) }}
                </span>
            </span>

            @if($codename)
                {{-- Separator --}}
                <span class="hidden sm:inline text-gray-400 dark:text-gray-600">•</span>

                {{-- Codename --}}
                <span class="hidden sm:inline text-gray-500 dark:text-gray-500">
                    "{{ $codename }}"
                </span>
            @endif
        </div>
    </div>
</footer>
