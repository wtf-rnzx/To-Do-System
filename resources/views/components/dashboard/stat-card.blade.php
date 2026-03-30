@props([
    'label'      => '',
    'value'      => 0,
    'iconBg'     => 'bg-blue-50 dark:bg-blue-900/20',
    'trend'      => null,
    'trendValue' => null,
    'sublabel'   => null,
])

<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700
            shadow-sm p-4 flex flex-col justify-between h-full">
    <div class="flex items-start justify-between gap-3">
        <div class="flex flex-col gap-1 min-w-0">
            <span class="text-xs font-semibold uppercase tracking-wider
                         text-gray-500 dark:text-gray-400 break-words">
                {{ $label }}
            </span>
            <span class="text-3xl font-bold text-gray-900 dark:text-white tabular-nums leading-none mt-1">
                {{ $value }}
            </span>
            @if ($sublabel)
                <span class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $sublabel }}</span>
            @endif
        </div>

        {{-- Icon slot --}}
        <div class="h-10 w-10 rounded-xl {{ $iconBg }} flex items-center justify-center shrink-0">
            {{ $icon }}
        </div>
    </div>

    @if ($trend && $trendValue !== null)
        <div class="flex items-center gap-1.5 mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
            @if ($trend === 'up')
                <svg class="h-3.5 w-3.5 text-emerald-500 shrink-0" fill="none" stroke="currentColor"
                     stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                </svg>
                <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">{{ $trendValue }}</span>
            @else
                <svg class="h-3.5 w-3.5 text-red-500 shrink-0" fill="none" stroke="currentColor"
                     stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M2.25 6L9 12.75l4.306-4.307a11.95 11.95 0 015.814 5.519l2.74 1.22m0 0l-5.94 2.28m5.94-2.28l-2.28-5.941" />
                </svg>
                <span class="text-xs font-semibold text-red-600 dark:text-red-400">{{ $trendValue }}</span>
            @endif
            <span class="text-xs text-gray-400 dark:text-gray-500">vs last week</span>
        </div>
    @endif
</div>
