@props([
    'percentage' => 0,
    'label'      => 'Completion',
    'sublabel'   => 'of tasks completed',
    'color'      => 'indigo',
])

@php
    $r           = 52;
    $circumference = round(2 * M_PI * $r, 2);
    $offset      = round($circumference - ($percentage / 100) * $circumference, 2);
    $strokeClass = match ($color) {
        'green'  => 'stroke-emerald-500',
        'amber'  => 'stroke-amber-500',
        'red'    => 'stroke-red-500',
        'purple' => 'stroke-violet-500',
        default  => 'stroke-indigo-500',
    };
    $textClass = match ($color) {
        'green'  => 'text-emerald-600 dark:text-emerald-400',
        'amber'  => 'text-amber-600 dark:text-amber-400',
        'red'    => 'text-red-600 dark:text-red-400',
        'purple' => 'text-violet-600 dark:text-violet-400',
        default  => 'text-indigo-600 dark:text-indigo-400',
    };
@endphp

<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700
            shadow-sm p-4 flex flex-col items-center justify-center gap-2 h-full">
    <span class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">
        {{ $label }}
    </span>

    <div class="relative w-32 h-32">
        <svg class="w-full h-full -rotate-90" viewBox="0 0 120 120">
            {{-- Track --}}
            <circle cx="60" cy="60" r="{{ $r }}"
                    fill="none" stroke-width="10"
                    class="stroke-gray-100 dark:stroke-gray-700" />
            {{-- Progress --}}
            <circle cx="60" cy="60" r="{{ $r }}"
                    fill="none" stroke-width="10"
                    class="{{ $strokeClass }}"
                    stroke-linecap="round"
                    stroke-dasharray="{{ $circumference }}"
                    stroke-dashoffset="{{ $offset }}"
                    style="transition: stroke-dashoffset 0.8s ease" />
        </svg>
        <div class="absolute inset-0 flex flex-col items-center justify-center">
            <span class="text-2xl font-bold {{ $textClass }} tabular-nums leading-none">
                {{ $percentage }}%
            </span>
        </div>
    </div>

    <p class="text-xs text-gray-400 dark:text-gray-500 text-center">{{ $sublabel }}</p>
</div>
