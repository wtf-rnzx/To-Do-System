<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Accomplishments') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('These are the accomplishments you have achieved.') }}
        </p>
    </header>

    <div class="mt-6 overflow-x-auto">
        <div class="flex gap-3 min-w-max pb-1">
            @forelse(($visibleAchievements ?? []) as $userAchievement)
                <div class="w-56 rounded-lg border border-emerald-200 bg-emerald-50/60 dark:border-emerald-800/60 dark:bg-emerald-900/20 p-4">
                    <p class="text-2xl leading-none">{{ $userAchievement->achievement->badge_icon }}</p>
                    <p class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">
                        {{ $userAchievement->achievement->title }}
                    </p>
                    <p class="mt-0.5 text-xs text-gray-600 dark:text-gray-400 line-clamp-2">
                        {{ $userAchievement->achievement->description }}
                    </p>
                    <p class="mt-2 text-[11px] text-emerald-700 dark:text-emerald-300">
                        Unlocked {{ optional($userAchievement->unlocked_at)->diffForHumans() }}
                    </p>
                </div>
        @empty
            <div class="rounded-lg border border-dashed border-gray-300 dark:border-gray-700 p-4 text-sm text-gray-600 dark:text-gray-300">
                No visible badges yet. Unlock badges in Achievements and mark them visible.
            </div>
        @endforelse
        </div>
    </div>
</section>
