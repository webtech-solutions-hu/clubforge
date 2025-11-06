<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-6">
            {{-- Header --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary-100 dark:bg-primary-900/20">
                        <svg class="h-6 w-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $tier_name }} Plan
                        </h3>
                        @if($license_key)
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                License: {{ substr($license_key, 0, 8) }}...{{ substr($license_key, -8) }}
                            </p>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                No license activated
                            </p>
                        @endif
                    </div>
                </div>

                {{-- Status Badge --}}
                <div>
                    @if($is_expired)
                        <span class="inline-flex items-center rounded-full bg-danger-100 px-3 py-1 text-xs font-medium text-danger-800 dark:bg-danger-900/20 dark:text-danger-400">
                            <svg class="mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                            Expired
                        </span>
                    @elseif($is_active)
                        <span class="inline-flex items-center rounded-full bg-success-100 px-3 py-1 text-xs font-medium text-success-800 dark:bg-success-900/20 dark:text-success-400">
                            <svg class="mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            Active
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-warning-100 px-3 py-1 text-xs font-medium text-warning-800 dark:bg-warning-900/20 dark:text-warning-400">
                            <svg class="mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            Inactive
                        </span>
                    @endif
                </div>
            </div>

            {{-- Usage Stats Grid --}}
            <div class="grid gap-4 md:grid-cols-2">
                {{-- Users --}}
                <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-white/10 dark:bg-gray-800">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Users</span>
                        </div>
                        <span class="text-2xl font-bold text-gray-900 dark:text-white">
                            @if($users['unlimited'])
                                {{ number_format($users['current']) }}
                            @else
                                {{ number_format($users['current']) }}<span class="text-base text-gray-400">/{{ number_format($users['max']) }}</span>
                            @endif
                        </span>
                    </div>
                    @if(!$users['unlimited'])
                        <div class="mt-3">
                            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                                <span>{{ number_format($users['remaining']) }} remaining</span>
                                <span>{{ number_format($users['percentage'], 1) }}%</span>
                            </div>
                            <div class="mt-1 h-2 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                                <div
                                    class="h-2 rounded-full transition-all {{ $users['percentage'] > 80 ? 'bg-danger-500' : ($users['percentage'] > 50 ? 'bg-warning-500' : 'bg-success-500') }}"
                                    style="width: {{ min(100, $users['percentage']) }}%"
                                ></div>
                            </div>
                        </div>
                    @else
                        <p class="mt-2 text-xs text-success-600 dark:text-success-400">✓ Unlimited users</p>
                    @endif
                </div>

                {{-- Storage --}}
                <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-white/10 dark:bg-gray-800">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                            </svg>
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Storage</span>
                        </div>
                        <span class="text-2xl font-bold text-gray-900 dark:text-white">
                            @if($storage['unlimited'])
                                {{ $storage['current_gb'] }} GB
                            @else
                                {{ $storage['current_gb'] }}<span class="text-base text-gray-400">/{{ $storage['max_gb'] }} GB</span>
                            @endif
                        </span>
                    </div>
                    @if(!$storage['unlimited'])
                        <div class="mt-3">
                            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                                <span>{{ $storage['remaining_gb'] }} GB remaining</span>
                                <span>{{ number_format($storage['percentage'], 1) }}%</span>
                            </div>
                            <div class="mt-1 h-2 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                                <div
                                    class="h-2 rounded-full transition-all {{ $storage['percentage'] > 80 ? 'bg-danger-500' : ($storage['percentage'] > 50 ? 'bg-warning-500' : 'bg-success-500') }}"
                                    style="width: {{ min(100, $storage['percentage']) }}%"
                                ></div>
                            </div>
                        </div>
                    @else
                        <p class="mt-2 text-xs text-success-600 dark:text-success-400">✓ Unlimited storage</p>
                    @endif
                </div>
            </div>

            {{-- Features --}}
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-white/10 dark:bg-gray-900/50">
                <h4 class="mb-3 text-sm font-medium text-gray-700 dark:text-gray-300">Enabled Features</h4>
                <div class="grid gap-2 md:grid-cols-2">
                    <div class="flex items-center gap-2 text-sm {{ $features['analytics_advanced'] ? 'text-success-600 dark:text-success-400' : 'text-gray-400 dark:text-gray-600' }}">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                            @if($features['analytics_advanced'])
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            @else
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            @endif
                        </svg>
                        <span>Advanced Analytics</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm {{ $features['webhooks_enabled'] ? 'text-success-600 dark:text-success-400' : 'text-gray-400 dark:text-gray-600' }}">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                            @if($features['webhooks_enabled'])
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            @else
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            @endif
                        </svg>
                        <span>Webhooks & API</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm {{ $features['remove_branding'] ? 'text-success-600 dark:text-success-400' : 'text-gray-400 dark:text-gray-600' }}">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                            @if($features['remove_branding'])
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            @else
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            @endif
                        </svg>
                        <span>White Label</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm {{ $features['custom_domain'] ? 'text-success-600 dark:text-success-400' : 'text-gray-400 dark:text-gray-600' }}">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                            @if($features['custom_domain'])
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            @else
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            @endif
                        </svg>
                        <span>Custom Domain</span>
                    </div>
                </div>
            </div>

            @if($expires_at)
                <div class="rounded-lg border-l-4 {{ $is_expired ? 'border-danger-500 bg-danger-50 dark:bg-danger-900/20' : 'border-warning-500 bg-warning-50 dark:bg-warning-900/20' }} p-3">
                    <p class="text-sm {{ $is_expired ? 'text-danger-800 dark:text-danger-400' : 'text-warning-800 dark:text-warning-400' }}">
                        @if($is_expired)
                            License expired on {{ $expires_at->format('M d, Y') }}
                        @else
                            License expires on {{ $expires_at->format('M d, Y') }}
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
