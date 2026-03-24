<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Prevent flash of wrong theme -->
        <script>
            (function () {
                const theme = localStorage.getItem('theme');
                if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                }
            })();
        </script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-stone-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>

            @if (session('newly_unlocked_achievements'))
                @php($latestUnlock = collect(session('newly_unlocked_achievements'))->last())
                <div
                    x-data="{ open: true }"
                    x-show="open"
                    x-transition.opacity
                    class="fixed inset-0 z-50"
                    style="display: none;"
                >
                    <div class="absolute inset-0 bg-gray-950/80 backdrop-blur-sm"></div>
                    <div class="relative h-full w-full flex items-center justify-center px-4">
                        <div class="w-full max-w-lg rounded-2xl border border-amber-200/60 bg-white dark:bg-gray-900 shadow-2xl p-8 text-center">
                            <p class="text-5xl leading-none">{{ $latestUnlock['badge_icon'] ?? '🏅' }}</p>
                            <p class="mt-4 text-xs font-semibold uppercase tracking-widest text-amber-600 dark:text-amber-400">Achievement Unlocked</p>
                            <h3 class="mt-2 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $latestUnlock['title'] ?? 'Great Job!' }}</h3>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ $latestUnlock['description'] ?? 'You unlocked a new badge.' }}</p>
                            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">Keep the momentum going — your future self says thanks. 🚀</p>

                            <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-3">
                                <a href="{{ route('achievements.index') }}" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors">
                                    View Achievements
                                </a>
                                <button type="button" @click="open = false" class="inline-flex items-center justify-center rounded-md bg-gray-200 dark:bg-gray-700 px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                                    Continue
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <script>
            function toggleTheme() {
                const html = document.documentElement;
                if (html.classList.contains('dark')) {
                    html.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                } else {
                    html.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                }
            }
        </script>
        @stack('scripts')
    </body>
</html>
