<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Dashboard
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    Welcome back, {{ Auth::user()->name }}
                </p>
            </div>
            <a href="{{ route('todos.create') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 hover:bg-indigo-700
                      px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                New Task
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

                {{-- ── Row 1: Stat Cards ───────────────────────────────────── --}}

                <x-dashboard.stat-card
                    label="Total Tasks"
                    :value="$totalTodos"
                    icon-bg="bg-indigo-50 dark:bg-indigo-900/20">
                    <x-slot:icon>
                        <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none"
                             stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </x-slot:icon>
                </x-dashboard.stat-card>

                <x-dashboard.stat-card
                    label="Completed"
                    :value="$completedTodos"
                    icon-bg="bg-emerald-50 dark:bg-emerald-900/20"
                    :sublabel="$completionPct . '% of total'">
                    <x-slot:icon>
                        <svg class="h-5 w-5 text-emerald-600 dark:text-emerald-400" fill="none"
                             stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </x-slot:icon>
                </x-dashboard.stat-card>

                <x-dashboard.stat-card
                    label="Pending"
                    :value="$pendingTodos"
                    icon-bg="bg-amber-50 dark:bg-amber-900/20">
                    <x-slot:icon>
                        <svg class="h-5 w-5 text-amber-600 dark:text-amber-400" fill="none"
                             stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </x-slot:icon>
                </x-dashboard.stat-card>

                <x-dashboard.stat-card
                    label="Overdue"
                    :value="$overdueTodos"
                    icon-bg="bg-red-50 dark:bg-red-900/20"
                    :sublabel="$overdueTodos > 0 ? 'Needs attention' : 'All clear'">
                    <x-slot:icon>
                        <svg class="h-5 w-5 text-red-600 dark:text-red-400" fill="none"
                             stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118zm-9 3.75h.008v.008H12v-.008z" />
                        </svg>
                    </x-slot:icon>
                </x-dashboard.stat-card>

                {{-- ── Row 2: Trend Chart + Completion Ring ─────────────────── --}}

                <div class="col-span-2 md:col-span-3 h-52">
                    <x-dashboard.trend-chart
                        :data="$trendData"
                        title="Task Activity — Last 7 Days"
                        color="blue" />
                </div>

                <div class="col-span-2 md:col-span-1 h-52">
                    <x-dashboard.completion-ring
                        :percentage="$completionPct"
                        label="My Progress"
                        sublabel="of tasks completed"
                        color="indigo" />
                </div>

                {{-- ── Row 3: Recent Todos + Quick Actions ──────────────────── --}}

                <div class="col-span-2 md:col-span-3 h-56">
                    <x-dashboard.recent-todos
                        :todos="$recentTodos"
                        title="Recent Tasks" />
                </div>

                {{-- Quick Actions --}}
                <div class="col-span-2 md:col-span-1 h-56">
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700
                                shadow-sm p-4 flex flex-col h-full gap-2">
                        <span class="text-xs font-semibold uppercase tracking-widest
                                     text-gray-500 dark:text-gray-400 mb-1 shrink-0">
                            Quick Actions
                        </span>

                        <a href="{{ route('todos.create') }}"
                           class="flex items-center gap-3 rounded-lg border border-gray-200 dark:border-gray-700
                                  px-3 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300
                                  hover:bg-indigo-50 hover:border-indigo-300 hover:text-indigo-700
                                  dark:hover:bg-indigo-900/20 dark:hover:border-indigo-700 dark:hover:text-indigo-300
                                  transition-colors group">
                            <div class="h-7 w-7 rounded-lg bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center shrink-0
                                        group-hover:bg-indigo-100 dark:group-hover:bg-indigo-900/40 transition-colors">
                                <svg class="h-4 w-4 text-indigo-600 dark:text-indigo-400" fill="none"
                                     stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </div>
                            New Task
                        </a>

                        <a href="{{ route('todos.index') }}"
                           class="flex items-center gap-3 rounded-lg border border-gray-200 dark:border-gray-700
                                  px-3 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300
                                  hover:bg-gray-50 dark:hover:bg-gray-700/40
                                  transition-colors group">
                            <div class="h-7 w-7 rounded-lg bg-gray-100 dark:bg-gray-700/60 flex items-center justify-center shrink-0">
                                <svg class="h-4 w-4 text-gray-500 dark:text-gray-400" fill="none"
                                     stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                </svg>
                            </div>
                            All Tasks
                        </a>

                        <a href="{{ route('profile.edit') }}"
                           class="flex items-center gap-3 rounded-lg border border-gray-200 dark:border-gray-700
                                  px-3 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300
                                  hover:bg-gray-50 dark:hover:bg-gray-700/40
                                  transition-colors group">
                            <div class="h-7 w-7 rounded-lg bg-gray-100 dark:bg-gray-700/60 flex items-center justify-center shrink-0">
                                <svg class="h-4 w-4 text-gray-500 dark:text-gray-400" fill="none"
                                     stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            My Profile
                        </a>
                    </div>
                </div>

            </div>{{-- /grid --}}
        </div>
    </div>
</x-app-layout>
