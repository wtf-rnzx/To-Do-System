@props([
    'users' => collect(),
    'title' => 'Top Users by Tasks Completed',
])

@php
    $chartId = 'top-users-chart-' . md5($title);
    $labels  = json_encode($users->pluck('name')->all());
    $values  = json_encode($users->pluck('todos_count')->all());
@endphp

<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700
            shadow-sm p-4 flex flex-col h-full">
    <span class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-3 shrink-0">
        {{ $title }}
    </span>
    <div class="flex-1 relative min-h-0">
        @if ($users->isEmpty())
            <div class="absolute inset-0 flex items-center justify-center">
                <p class="text-sm text-gray-400 dark:text-gray-500">No users yet.</p>
            </div>
        @else
            <canvas id="{{ $chartId }}"></canvas>
        @endif
    </div>
</div>

@pushOnce('scripts', 'chartjs-cdn')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endPushOnce

@if (!$users->isEmpty())
@push('scripts')
<script>
(function () {
    const isDark    = () => document.documentElement.classList.contains('dark');
    const gridColor = () => isDark() ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
    const tickColor = () => isDark() ? 'rgba(255,255,255,0.35)' : 'rgba(0,0,0,0.35)';

    const canvas = document.getElementById('{{ $chartId }}');
    if (!canvas) return;

    // Build indigo → violet vertical gradient at render time
    const gradientPlugin = {
        id: 'barGradient-{{ $chartId }}',
        beforeDatasetsDraw(chart) {
            const { ctx, chartArea } = chart;
            if (!chartArea) return;
            const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
            gradient.addColorStop(0, 'rgba(99,102,241,0.9)');
            gradient.addColorStop(1, 'rgba(139,92,246,0.55)');
            chart.data.datasets[0].backgroundColor = gradient;
        },
    };

    const chart = new Chart(canvas, {
        type: 'bar',
        plugins: [gradientPlugin],
        data: {
            labels: {!! $labels !!},
            datasets: [{
                data: {!! $values !!},
                backgroundColor: 'rgba(99,102,241,0.8)',   // overridden by plugin
                hoverBackgroundColor: 'rgba(99,102,241,1)',
                borderRadius: 6,
                borderSkipped: false,
                barPercentage: 0.55,
                categoryPercentage: 0.7,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 700, easing: 'easeInOutQuart' },
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
                        label: (ctx) => ` ${ctx.parsed.y} ${ctx.parsed.y === 1 ? 'task' : 'tasks'} completed`,
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
                    grid: { color: gridColor() },
                    ticks: { color: tickColor(), font: { size: 11 }, precision: 0, stepSize: 1 },
                    border: { display: false },
                    beginAtZero: true,
                },
            },
        },
    });

    // Re-theme on dark mode toggle
    const observer = new MutationObserver(() => {
        chart.options.scales.x.ticks.color          = tickColor();
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
