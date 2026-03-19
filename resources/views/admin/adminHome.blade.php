<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Admin Dashboard
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    User analytics overview — {{ now()->format('F j, Y') }}
                </p>
            </div>
            <time id="ph-time"
                  class="inline-flex items-center rounded-lg border border-gray-200 dark:border-gray-700
                         bg-white/90 dark:bg-gray-800/80 px-3.5 py-2 text-sm font-medium
                         text-gray-700 dark:text-gray-200 tabular-nums"
                  aria-live="polite">
                {{ now('Asia/Manila')->format('F j, Y — h:i A') }} PHT
            </time>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4">

                {{-- ── Row 1: User Stats ───────────────────────────────────── --}}

                <x-dashboard.stat-card
                    label="Total Users"
                    :value="number_format($totalUsers)"
                    :sublabel="'Active: ' . number_format($activeUsers) . ' • Inactive: ' . number_format($inactiveUsers)"
                    icon-bg="bg-indigo-50 dark:bg-indigo-900/20">
                    <x-slot:icon>
                        <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none"
                             stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                    </x-slot:icon>
                </x-dashboard.stat-card>

                <!-- <x-dashboard.stat-card
                    label="Recent Activity"
                    :value="number_format($recentActivity->count())"
                    icon-bg="bg-purple-50 dark:bg-purple-900/20"
                    sublabel="Latest events shown below">
                    <x-slot:icon>
                        <svg class="h-5 w-5 text-purple-600 dark:text-purple-400" fill="none"
                             stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </x-slot:icon>
                </x-dashboard.stat-card> -->

                {{-- ── Row 2: User Growth (Line) + Roles Distribution (Bar) ── --}}

                <div class="col-span-2 md:col-span-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-4 h-80 flex flex-col">
                    <div class="flex items-center justify-between gap-3 mb-3">
                        <span class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">
                            New User Growth
                        </span>

                        <div class="inline-flex rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden text-xs">
                            <a href="{{ route('home', ['growth_range' => 'daily']) }}"
                               class="px-3 py-1.5 font-medium transition-colors {{ $growthRange === 'daily' ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                                Daily
                            </a>
                            <a href="{{ route('home', ['growth_range' => 'weekly']) }}"
                               class="px-3 py-1.5 font-medium border-l border-gray-200 dark:border-gray-700 transition-colors {{ $growthRange === 'weekly' ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                                Weekly
                            </a>
                            <a href="{{ route('home', ['growth_range' => 'monthly']) }}"
                               class="px-3 py-1.5 font-medium border-l border-gray-200 dark:border-gray-700 transition-colors {{ $growthRange === 'monthly' ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                                Monthly
                            </a>
                        </div>
                    </div>

                    <div class="flex-1 min-h-0">
                        <x-dashboard.trend-chart
                            :data="$userGrowthData"
                            :title="match ($growthRange) {
                                'weekly' => 'Weekly New Users — Last 12 Weeks',
                                'monthly' => 'Monthly New Users — Last 12 Months',
                                default => 'Daily New Users — Last 14 Days',
                            }"
                            color="indigo"
                            unit-singular="new user"
                            unit-plural="new users" />
                    </div>
                </div>

                <div class="col-span-2 md:col-span-2 h-80">
                    <x-dashboard.role-distribution-chart
                        :data="$roleDistribution"
                        title="User Roles Distribution" />
                </div>

                {{-- ── Row 3: Recent User Activity Log ──────────────────────── --}}

                <div class="col-span-2 md:col-span-6 h-80">
                    <x-dashboard.recent-activity
                        :activities="$recentActivity"
                        title="Recent User Activity Log" />
                </div>

            </div>{{-- /grid --}}
        </div>
    </div>
</x-app-layout>
