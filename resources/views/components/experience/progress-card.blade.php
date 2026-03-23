@props([
    'experience',
    'title' => 'Experience Progress',
    'compact' => false,
    'showRankModal' => true,
])

@php
    $currentRank = $experience['current_rank'];
    $nextRank = $experience['next_rank'];
    $isMaxRank = (bool) $experience['is_max_rank'];
    $allRanks = collect($experience['all_ranks'] ?? []);
    $position = (int) ($experience['position'] ?? 1);
    $rankCount = (int) ($experience['rank_count'] ?? $allRanks->count());
@endphp

<div x-data="{ open: false }" {{ $attributes->merge(['class' => 'rounded-xl border border-violet-200/70 dark:border-violet-700/60 bg-gradient-to-br from-violet-50 to-indigo-50 dark:from-violet-900/25 dark:to-indigo-900/20 p-4']) }}>
    @if ($showRankModal)
        <div
            role="button"
            tabindex="0"
            @click="open = true"
            @keydown.enter.prevent="open = true"
            @keydown.space.prevent="open = true"
            class="w-full text-left"
            title="View all ranks"
            aria-label="Open rank list"
        >
    @endif

    <div class="flex items-start justify-between gap-3">
        <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-violet-700 dark:text-violet-300">{{ $title }}</p>
            <p class="mt-1 text-3xl font-extrabold text-violet-900 dark:text-violet-100 tabular-nums">
                {{ number_format((int) $experience['total_exp']) }} EXP
            </p>
        </div>

        <span class="inline-flex items-center gap-1.5 rounded-full bg-violet-100/90 dark:bg-violet-800/60 px-3 py-1 text-xs font-semibold text-violet-800 dark:text-violet-100">
            <span>{{ $currentRank['badge'] ?? '🏅' }}</span>
            <span>{{ $currentRank['name'] }}</span>
        </span>
    </div>

    <div class="mt-3">
        <div class="flex items-center justify-between text-xs text-violet-700/90 dark:text-violet-300/90 mb-1.5">
            <span>Rank Progress</span>
            <span>{{ (int) $experience['progress_pct'] }}%</span>
        </div>

        <div class="h-2.5 rounded-full bg-violet-100 dark:bg-violet-900/40 overflow-hidden" x-data="{ pct: {{ (int) $experience['progress_pct'] }} }">
            <div
                class="h-full rounded-full bg-gradient-to-r from-violet-500 to-indigo-500 transition-all duration-500"
                :style="'width: ' + pct + '%'"
            ></div>
        </div>

        @if ($isMaxRank)
            <p class="mt-2 text-xs text-violet-700 dark:text-violet-300">You have reached the highest rank. Legendary focus unlocked. 🔥</p>
        @else
            <p class="mt-2 text-xs text-violet-700 dark:text-violet-300">
                {{ number_format((int) $experience['exp_to_next_rank']) }} EXP to reach
                <span class="font-semibold">{{ $nextRank['name'] }}</span>
                <span class="ml-1">{{ $nextRank['badge'] ?? '🏅' }}</span>
            </p>
        @endif

        @if (! $compact)
            <p class="mt-1 text-[11px] text-violet-700/80 dark:text-violet-300/80">
                Current rank started at {{ number_format((int) $currentRank['min_exp']) }} EXP
                @if (! $isMaxRank)
                    • Next threshold: {{ number_format((int) $nextRank['min_exp']) }} EXP
                @endif
            </p>
        @endif

        @if ($showRankModal)
            <p class="mt-2 text-[11px] text-violet-700/80 dark:text-violet-300/80">Click card to view all ranks and requirements.</p>
        @endif
    </div>

    @if ($showRankModal)
        </div>

        <div
            x-show="open"
            x-transition.opacity
            x-cloak
            @keydown.escape.window="open = false"
            class="fixed inset-0 z-50"
            style="display: none;"
        >
            <div class="absolute inset-0 bg-gray-950/70" @click="open = false"></div>

            <div class="relative h-full w-full flex items-center justify-center p-4">
                <div class="w-full max-w-2xl rounded-xl border border-violet-200 dark:border-violet-800 bg-white dark:bg-gray-900 shadow-2xl overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between gap-3">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Rank Roadmap</h3>
                            <p class="text-xs text-gray-600 dark:text-gray-400">You are rank {{ $position }} of {{ $rankCount }}.</p>
                        </div>

                        <button
                            type="button"
                            class="inline-flex items-center justify-center h-8 w-8 rounded-md text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800"
                            @click="open = false"
                            aria-label="Close rank roadmap"
                        >
                            ✕
                        </button>
                    </div>

                    <div class="max-h-[65vh] overflow-y-auto p-4 space-y-2">
                        @foreach ($allRanks as $rank)
                            @php
                                $isCurrent = ($rank['key'] ?? null) === ($currentRank['key'] ?? null);
                            @endphp

                            <div class="rounded-lg border p-3 {{ $isCurrent ? 'border-violet-400 bg-violet-50 dark:border-violet-600 dark:bg-violet-900/30' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900' }}">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg">{{ $rank['badge'] ?? '🏅' }}</span>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $rank['name'] }}</p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">Requires at least {{ number_format((int) $rank['min_exp']) }} EXP</p>
                                        </div>
                                    </div>

                                    @if ($isCurrent)
                                        <span class="inline-flex items-center rounded-full px-2 py-1 text-[11px] font-semibold bg-violet-100 dark:bg-violet-800 text-violet-700 dark:text-violet-100">Current</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
