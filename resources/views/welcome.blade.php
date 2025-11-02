<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches) }"
      x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))"
      :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Club Forge') }} - Your Gaming Community Hub</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-20px); }
            }

            @keyframes rotate-slow {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }

            .animate-float {
                animation: float 6s ease-in-out infinite;
            }

            .animate-rotate-slow {
                animation: rotate-slow 20s linear infinite;
            }

            @keyframes gradient-x {
                0%, 100% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
            }

            .animate-gradient {
                background-size: 200% 200%;
                animation: gradient-x 15s ease infinite;
            }
        </style>
    </head>
    <body class="antialiased bg-gradient-to-br from-slate-50 to-amber-50/30 dark:from-gray-950 dark:to-amber-950/20 transition-colors duration-300">
        <!-- Background Pattern -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none opacity-30">
            <div class="absolute -top-1/2 -right-1/2 w-full h-full bg-gradient-to-br from-amber-200/20 to-orange-300/20 dark:from-amber-500/10 dark:to-orange-600/10 rounded-full blur-3xl animate-rotate-slow"></div>
            <div class="absolute -bottom-1/2 -left-1/2 w-full h-full bg-gradient-to-tr from-indigo-200/20 to-sky-300/20 dark:from-indigo-500/10 dark:to-sky-600/10 rounded-full blur-3xl animate-rotate-slow" style="animation-direction: reverse; animation-duration: 25s;"></div>
        </div>

        <!-- Navigation -->
        <nav class="fixed top-0 left-0 right-0 z-50 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-800 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-amber-600 to-orange-500 rounded-lg flex items-center justify-center shadow-lg hover:shadow-xl transition-all hover:scale-105">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-gray-900 dark:text-white">Club Forge</span>
                    </div>

                    <!-- Navigation Items -->
                    <div class="flex items-center space-x-4">
                        <!-- Dark Mode Toggle -->
                        <button
                            @click="darkMode = !darkMode"
                            class="relative p-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-all duration-300"
                            aria-label="Toggle dark mode"
                        >
                            <!-- Sun Icon -->
                            <svg
                                x-show="!darkMode"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 rotate-90 scale-0"
                                x-transition:enter-end="opacity-100 rotate-0 scale-100"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100 rotate-0 scale-100"
                                x-transition:leave-end="opacity-0 -rotate-90 scale-0"
                                class="w-5 h-5 text-amber-500"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>

                            <!-- Moon Icon -->
                            <svg
                                x-show="darkMode"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 -rotate-90 scale-0"
                                x-transition:enter-end="opacity-100 rotate-0 scale-100"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100 rotate-0 scale-100"
                                x-transition:leave-end="opacity-0 rotate-90 scale-0"
                                class="w-5 h-5 text-indigo-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </button>

                        @auth
                            <a
                                href="{{ url('/admin') }}"
                                class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-amber-600 dark:hover:text-amber-400 transition-colors"
                            >
                                Dashboard
                            </a>
                        @else
                            <a
                                href="{{ route('filament.admin.auth.login') }}"
                                class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-amber-600 dark:hover:text-amber-400 transition-colors"
                            >
                                Log in
                            </a>

                            <a
                                href="{{ route('filament.admin.auth.register') }}"
                                class="px-6 py-2 text-sm font-semibold text-white bg-gradient-to-r from-amber-600 to-orange-500 rounded-lg hover:from-amber-700 hover:to-orange-600 shadow-md hover:shadow-lg transition-all hover:scale-105"
                            >
                                Get Started
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="relative pt-32 pb-20 px-4 sm:px-6 lg:px-8 overflow-hidden">
            <div class="max-w-7xl mx-auto relative z-10">
                <div class="text-center">
                    <!-- Animated Badge -->
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-gradient-to-r from-amber-100 to-orange-100 dark:from-amber-900/30 dark:to-orange-900/30 text-amber-800 dark:text-amber-300 text-sm font-medium mb-6 animate-pulse">
                        <svg class="w-4 h-4 mr-2 animate-spin" style="animation-duration: 3s;" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        Your Gaming Community Hub
                    </div>

                    <!-- Headline with Animation -->
                    <h1 class="text-5xl sm:text-6xl lg:text-7xl font-bold text-gray-900 dark:text-white mb-6 animate-float">
                        <span class="block mb-2">Build. Play. Connect.</span>
                        <span class="bg-gradient-to-r from-amber-600 to-orange-500 dark:from-amber-400 dark:to-orange-400 bg-clip-text text-transparent animate-gradient">
                            Forge Your Club
                        </span>
                    </h1>

                    <!-- Subheadline -->
                    <p class="text-xl sm:text-2xl text-gray-600 dark:text-gray-300 mb-12 max-w-3xl mx-auto leading-relaxed">
                        The ultimate platform for managing your gaming club, organizing tournaments, tracking member participation, and building an engaged community.
                    </p>

                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                        @guest
                            <a
                                href="{{ route('filament.admin.auth.register') }}"
                                class="group px-8 py-4 text-lg font-semibold text-white bg-gradient-to-r from-amber-600 to-orange-500 rounded-xl hover:from-amber-700 hover:to-orange-600 shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all inline-flex items-center gap-2"
                            >
                                <span>Start Your Journey</span>
                                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </a>
                            <button
                                @click="alert('Demo video coming soon!')"
                                class="px-8 py-4 text-lg font-semibold text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 rounded-xl border-2 border-gray-200 dark:border-gray-700 hover:border-indigo-500 dark:hover:border-sky-500 shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all inline-flex items-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" />
                                </svg>
                                <span>See Demo</span>
                            </button>
                        @else
                            <a
                                href="{{ url('/admin') }}"
                                class="px-8 py-4 text-lg font-semibold text-white bg-gradient-to-r from-amber-600 to-orange-500 rounded-xl hover:from-amber-700 hover:to-orange-600 shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all"
                            >
                                Go to Dashboard
                            </a>
                        @endguest
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-20 px-4 sm:px-6 lg:px-8 bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm relative">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                        Everything You Need
                    </h2>
                    <p class="text-xl text-gray-600 dark:text-gray-300">
                        Powerful features to manage and grow your gaming community
                    </p>
                </div>

                <!-- Community Features -->
                <div class="mb-12">
                    <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-amber-600 to-orange-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        Community
                    </h3>
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Feature 1 -->
                        <div class="group bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-950/20 dark:to-orange-950/20 rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all border border-amber-100 dark:border-amber-900/30 hover:-translate-y-1">
                            <div class="w-14 h-14 bg-gradient-to-br from-amber-600 to-orange-500 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Member Management</h3>
                            <p class="text-gray-600 dark:text-gray-300">
                                Easily manage members with role-based access control, avatars, and detailed profiles.
                            </p>
                        </div>

                        <!-- Feature 2 -->
                        <div class="group bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-950/20 dark:to-orange-950/20 rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all border border-amber-100 dark:border-amber-900/30 hover:-translate-y-1">
                            <div class="w-14 h-14 bg-gradient-to-br from-amber-600 to-orange-500 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Message Board</h3>
                            <p class="text-gray-600 dark:text-gray-300">
                                Keep your community engaged with posts, comments, likes, and the ability to pin important announcements.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Organization Features -->
                <div class="mb-12">
                    <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-blue-600 dark:from-sky-400 dark:to-blue-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        Organization
                    </h3>
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Feature 3 -->
                        <div class="group bg-gradient-to-br from-indigo-50 to-blue-50 dark:from-indigo-950/20 dark:to-blue-950/20 rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all border border-indigo-100 dark:border-indigo-900/30 hover:-translate-y-1">
                            <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-blue-600 dark:from-sky-400 dark:to-blue-500 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Event Management</h3>
                            <p class="text-gray-600 dark:text-gray-300">
                                Organize games, tournaments, and events with dedicated Game Master roles and management tools.
                            </p>
                        </div>

                        <!-- Feature 4 -->
                        <div class="group bg-gradient-to-br from-indigo-50 to-blue-50 dark:from-indigo-950/20 dark:to-blue-950/20 rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all border border-indigo-100 dark:border-indigo-900/30 hover:-translate-y-1">
                            <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-blue-600 dark:from-sky-400 dark:to-blue-500 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Tournament System</h3>
                            <p class="text-gray-600 dark:text-gray-300">
                                Create and manage competitive tournaments with brackets, scores, and live updates.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Management Features -->
                <div>
                    <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-teal-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        Management & Security
                    </h3>
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Feature 5 -->
                        <div class="group bg-gradient-to-br from-green-50 to-teal-50 dark:from-green-950/20 dark:to-teal-950/20 rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all border border-green-100 dark:border-green-900/30 hover:-translate-y-1">
                            <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-teal-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Role-Based Access</h3>
                            <p class="text-gray-600 dark:text-gray-300">
                                Five default roles including Administrator, Owner, Game Master, Member, and Guest with customizable permissions.
                            </p>
                        </div>

                        <!-- Feature 6 -->
                        <div class="group bg-gradient-to-br from-green-50 to-teal-50 dark:from-green-950/20 dark:to-teal-950/20 rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all border border-green-100 dark:border-green-900/30 hover:-translate-y-1">
                            <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-teal-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Admin Dashboard</h3>
                            <p class="text-gray-600 dark:text-gray-300">
                                Modern, intuitive admin interface built with Filament 3.3 for powerful content management.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-amber-600 to-orange-500 dark:from-amber-700 dark:to-orange-600 opacity-90"></div>
            <div class="absolute inset-0">
                <div class="absolute top-0 left-1/4 w-72 h-72 bg-white/10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
            </div>

            <div class="max-w-4xl mx-auto text-center relative z-10">
                <h2 class="text-4xl font-bold text-white mb-4">
                    Ready to Build Your Community?
                </h2>
                <p class="text-xl text-amber-50 mb-8">
                    Join Club Forge today and start creating amazing gaming experiences.
                </p>
                @guest
                    <a
                        href="{{ route('filament.admin.auth.register') }}"
                        class="inline-flex items-center gap-2 px-8 py-4 text-lg font-semibold text-amber-600 bg-white rounded-xl hover:bg-gray-50 shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all"
                    >
                        <span>Get Started Free</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                @else
                    <a
                        href="{{ url('/admin') }}"
                        class="inline-block px-8 py-4 text-lg font-semibold text-amber-600 bg-white rounded-xl hover:bg-gray-50 shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all"
                    >
                        Go to Dashboard
                    </a>
                @endguest
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-900 dark:bg-black text-gray-300 transition-colors duration-300">
            <div class="max-w-7xl mx-auto text-center">
                <div class="flex items-center justify-center space-x-3 mb-4">
                    <div class="w-8 h-8 bg-gradient-to-br from-amber-600 to-orange-500 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <span class="text-lg font-bold text-white">Club Forge</span>
                </div>
                <p class="text-sm">
                    &copy; {{ date('Y') }} Club Forge. Built with Laravel 12 & Filament 3.3
                </p>
            </div>
        </footer>
    </body>
</html>
