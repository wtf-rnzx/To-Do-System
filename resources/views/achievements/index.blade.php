<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Achievements
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ $unlockedCount }} / {{ $totalCount }} unlocked
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900/40 dark:bg-emerald-900/20 dark:text-emerald-300">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900/40 dark:bg-red-900/20 dark:text-red-300">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($achievements as $achievement)
                    <div @class([
                            'rounded-xl border p-4 shadow-sm transition',
                            'border-emerald-200 bg-emerald-50/70 dark:border-emerald-800/50 dark:bg-emerald-900/20' => $achievement['unlocked'],
                            'border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800/80 opacity-85' => ! $achievement['unlocked'],
                        ])>
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-2xl leading-none">{{ $achievement['badge_icon'] }}</p>
                                <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $achievement['title'] }}</h3>
                                <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">{{ $achievement['description'] }}</p>
                            </div>
                            <span @class([
                                    'inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold',
                                    'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300' => $achievement['unlocked'],
                                    'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' => ! $achievement['unlocked'],
                                ])>
                                {{ $achievement['unlocked'] ? 'Unlocked' : 'Locked' }}
                            </span>
                        </div>

                        <div class="mt-3">
                            <div class="h-2 rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden">
                                <div class="h-full bg-indigo-500" x-data style="width: 0" :style="{ width: '{{ $achievement['progress_pct'] }}%' }"></div>
                            </div>
                            <p class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">{{ $achievement['raw_progress'] }} / {{ $achievement['threshold'] }}</p>
                        </div>

                        <form class="mt-3" method="POST" action="{{ route('achievements.toggle-visibility', $achievement['id']) }}">
                            @csrf
                            @method('PATCH')
                            <button
                                type="submit"
                                @disabled(! $achievement['unlocked'])
                                class="w-full inline-flex items-center justify-center rounded-md px-3 py-2 text-xs font-semibold transition disabled:cursor-not-allowed disabled:opacity-50 {{ $achievement['is_visible'] ? 'bg-indigo-600 text-white hover:bg-indigo-700' : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600' }}"
                            >
                                {{ $achievement['is_visible'] ? 'Visible on profile' : 'Show on profile' }}
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
