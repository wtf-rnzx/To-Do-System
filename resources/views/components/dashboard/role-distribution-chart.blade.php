@props([
    'data' => [],
    'title' => 'User Roles Distribution',
])

@php
    $chartId = 'role-distribution-chart-' . md5($title);
    $labels  = json_encode(collect($data)->pluck('label')->all());
    $values  = json_encode(collect($data)->pluck('value')->all());
@endphp

<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700
            shadow-sm p-4 flex flex-col h-full">
    <span class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-3 shrink-0">
        {{ $title }}
    </span>

    <div class="flex-1 relative min-h-0">
        @if (collect($data)->sum('value') === 0)
            <div class="absolute inset-0 flex items-center justify-center">
                <p class="text-sm text-gray-400 dark:text-gray-500">No role data available.</p>
            </div>
        @else
            <canvas id="{{ $chartId }}"></canvas>
        @endif
    </div>
</div>

@pushOnce('scripts', 'chartjs-cdn')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endPushOnce

@if (collect($data)->sum('value') > 0)
@push('scripts')
<script>
(function () {
    const isDark    = () => document.documentElement.classList.contains('dark');
    const gridColor = () => isDark() ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
    const tickColor = () => isDark() ? 'rgba(255,255,255,0.35)' : 'rgba(0,0,0,0.35)';

    const canvas = document.getElementById('{{ $chartId }}');
    if (!canvas) return;

    const chart = new Chart(canvas, {
        type: 'bar',
        data: {
            labels: {!! $labels !!},
            datasets: [{
                data: {!! $values !!},
                backgroundColor: [
                    'rgba(99,102,241,0.85)',
                    'rgba(14,165,233,0.85)',
                    'rgba(16,185,129,0.85)',
                    'rgba(245,158,11,0.85)',
                    'rgba(168,85,247,0.85)',
                ],
                borderRadius: 8,
                borderSkipped: false,
                barPercentage: 0.55,
                categoryPercentage: 0.7,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: isDark() ? '#374151' : '#ffffff',
                    titleColor:      isDark() ? '#f9fafb' : '#111827',
                    bodyColor:       isDark() ? '#d1d5db' : '#374151',
                    borderColor:     isDark() ? '#4b5563' : '#e5e7eb',
                    borderWidth: 1,
                    padding: 10,
                    callbacks: {
                        label: (ctx) => ` ${ctx.parsed.y} ${ctx.parsed.y === 1 ? 'user' : 'users'}`,
                    },
                },
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: tickColor(), font: { size: 11 }, maxRotation: 0 },
                    border: { display: false },
                },
                y: {
                    beginAtZero: true,
                    grid: { color: gridColor() },
                    ticks: { color: tickColor(), font: { size: 11 }, precision: 0, stepSize: 1 },
                    border: { display: false },
                },
            },
        },
    });

    const observer = new MutationObserver(() => {
        chart.options.scales.x.ticks.color           = tickColor();
        chart.options.scales.y.grid.color            = gridColor();
        chart.options.scales.y.ticks.color           = tickColor();
        chart.options.plugins.tooltip.backgroundColor = isDark() ? '#374151' : '#ffffff';
        chart.options.plugins.tooltip.titleColor      = isDark() ? '#f9fafb' : '#111827';
        chart.options.plugins.tooltip.bodyColor       = isDark() ? '#d1d5db' : '#374151';
        chart.options.plugins.tooltip.borderColor     = isDark() ? '#4b5563' : '#e5e7eb';
        chart.update('none');
    });

    observer.observe(document.documentElement, { attributeFilter: ['class'] });
})();
</script>
@endpush
@endif
