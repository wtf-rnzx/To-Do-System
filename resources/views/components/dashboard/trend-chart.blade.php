@props([
    'data'  => [],
    'title' => 'Activity Trend',
    'color' => 'blue',
])

@php
    $palette = [
        'blue'   => ['border' => 'rgb(99,102,241)',  'fill' => 'rgba(99,102,241,0.08)'],
        'green'  => ['border' => 'rgb(16,185,129)',  'fill' => 'rgba(16,185,129,0.08)'],
        'purple' => ['border' => 'rgb(168,85,247)',  'fill' => 'rgba(168,85,247,0.08)'],
        'amber'  => ['border' => 'rgb(245,158,11)',  'fill' => 'rgba(245,158,11,0.08)'],
    ];
    $c       = $palette[$color] ?? $palette['blue'];
    $chartId = 'chart-' . md5($title);
    $labels  = json_encode(collect($data)->pluck('label')->all());
    $values  = json_encode(collect($data)->pluck('value')->all());
@endphp

<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700
            shadow-sm p-4 flex flex-col h-full">
    <span class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-3">
        {{ $title }}
    </span>
    <div class="flex-1 relative min-h-0">
        <canvas id="{{ $chartId }}"></canvas>
    </div>
</div>

@pushOnce('scripts', 'chartjs-cdn')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endPushOnce

@push('scripts')
<script>
(function () {
    const isDark = () => document.documentElement.classList.contains('dark');

    const gridColor = () => isDark() ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
    const tickColor = () => isDark() ? 'rgba(255,255,255,0.35)' : 'rgba(0,0,0,0.35)';

    const canvas = document.getElementById('{{ $chartId }}');
    const chart = new Chart(canvas, {
        type: 'line',
        data: {
            labels: {!! $labels !!},
            datasets: [{
                data: {!! $values !!},
                borderColor: '{{ $c['border'] }}',
                backgroundColor: '{{ $c['fill'] }}',
                borderWidth: 2,
                tension: 0.45,
                fill: true,
                pointRadius: 3,
                pointHoverRadius: 6,
                pointBackgroundColor: '{{ $c['border'] }}',
                pointBorderColor: isDark() ? '#1f2937' : '#ffffff',
                pointBorderWidth: 2,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 600, easing: 'easeInOutQuart' },
            plugins: {
                legend: { display: false },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: isDark() ? '#374151' : '#ffffff',
                    titleColor: isDark() ? '#f9fafb' : '#111827',
                    bodyColor:  isDark() ? '#d1d5db' : '#374151',
                    borderColor: isDark() ? '#4b5563' : '#e5e7eb',
                    borderWidth: 1,
                    padding: 10,
                    callbacks: {
                        label: (ctx) => ` ${ctx.parsed.y} ${ctx.parsed.y === 1 ? 'task' : 'tasks'}`,
                    },
                },
            },
            scales: {
                x: {
                    grid: { color: gridColor(), drawBorder: false },
                    ticks: { color: tickColor(), font: { size: 11 }, maxRotation: 0 },
                    border: { display: false },
                },
                y: {
                    grid: { color: gridColor(), drawBorder: false },
                    ticks: { color: tickColor(), font: { size: 11 }, precision: 0, stepSize: 1 },
                    border: { display: false },
                    beginAtZero: true,
                },
            },
        },
    });

    // Re-theme when dark mode class toggles
    const observer = new MutationObserver(() => {
        chart.options.scales.x.grid.color = gridColor();
        chart.options.scales.x.ticks.color = tickColor();
        chart.options.scales.y.grid.color = gridColor();
        chart.options.scales.y.ticks.color = tickColor();
        chart.data.datasets[0].pointBorderColor = isDark() ? '#1f2937' : '#ffffff';
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
