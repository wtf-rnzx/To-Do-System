<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Admin Dashboard
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    System overview — {{ now()->format('F j, Y') }}
                </p>
            </div>
            <a href="{{ route('admin.users.index') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 hover:bg-indigo-700
                      px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
                Manage Users
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

                {{-- ── Row 1: Stat Cards ───────────────────────────────────── --}}

                <x-dashboard.stat-card
                    label="Total Users"
                    :value="$totalUsers"
                    icon-bg="bg-indigo-50 dark:bg-indigo-900/20">
                    <x-slot:icon>
                        <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none"
                             stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                    </x-slot:icon>
                </x-dashboard.stat-card>

                <x-dashboard.stat-card
                    label="Total Tasks"
                    :value="$totalTodos"
                    icon-bg="bg-blue-50 dark:bg-blue-900/20">
                    <x-slot:icon>
                        <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none"
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
                    :sublabel="$systemCompletionPct . '% completion rate'">
                    <x-slot:icon>
                        <svg class="h-5 w-5 text-emerald-600 dark:text-emerald-400" fill="none"
                             stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </x-slot:icon>
                </x-dashboard.stat-card>

                <x-dashboard.stat-card
                    label="Activity Logs"
                    :value="number_format($totalLogs)"
                    icon-bg="bg-purple-50 dark:bg-purple-900/20"
                    sublabel="Total events recorded">
                    <x-slot:icon>
                        <svg class="h-5 w-5 text-purple-600 dark:text-purple-400" fill="none"
                             stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </x-slot:icon>
                </x-dashboard.stat-card>

                {{-- ── Row 2: Trend Chart + Completion Ring ─────────────────── --}}

                <div class="col-span-2 md:col-span-3 h-52">
                    <x-dashboard.trend-chart
                        :data="$trendData"
                        title="System Task Activity — Last 7 Days"
                        color="blue" />
                </div>

                <div class="col-span-2 md:col-span-1 h-52">
                    <x-dashboard.completion-ring
                        :percentage="$systemCompletionPct"
                        label="System Completion"
                        sublabel="of all tasks done"
                        color="green" />
                </div>

                {{-- ── Row 3: Top Users + Recent Activity ───────────────────── --}}

                <div class="col-span-2 h-64">
                    <x-dashboard.top-users-chart
                        :users="$topUsers"
                        title="Top Users by Tasks Completed" />
                </div>

                <div class="col-span-2 h-64">
                    <x-dashboard.recent-activity
                        :activities="$recentActivity"
                        title="Recent Activity" />
                </div>

                {{-- ── Row 4: Quick Actions ──────────────────────────────────── --}}

                <!-- <div class="col-span-2 md:col-span-4 grid grid-cols-2 md:grid-cols-4 gap-3">

                    <a href="{{ route('admin.users.index') }}"
                       class="flex items-center gap-3 rounded-xl border border-gray-200 dark:border-gray-700
                              bg-white dark:bg-gray-800 shadow-sm px-4 py-3
                              hover:bg-indigo-50 hover:border-indigo-200
                              dark:hover:bg-indigo-900/20 dark:hover:border-indigo-700
                              transition-colors group">
                        <div class="h-9 w-9 rounded-lg bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center shrink-0
                                    group-hover:bg-indigo-100 dark:group-hover:bg-indigo-900/40 transition-colors">
                            <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none"
                                 stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200
                                      group-hover:text-indigo-700 dark:group-hover:text-indigo-300 transition-colors">
                                Manage Users
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">Roles &amp; accounts</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.logs.index') }}"
                       class="flex items-center gap-3 rounded-xl border border-gray-200 dark:border-gray-700
                              bg-white dark:bg-gray-800 shadow-sm px-4 py-3
                              hover:bg-purple-50 hover:border-purple-200
                              dark:hover:bg-purple-900/20 dark:hover:border-purple-700
                              transition-colors group">
                        <div class="h-9 w-9 rounded-lg bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center shrink-0
                                    group-hover:bg-purple-100 dark:group-hover:bg-purple-900/40 transition-colors">
                            <svg class="h-5 w-5 text-purple-600 dark:text-purple-400" fill="none"
                                 stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200
                                      group-hover:text-purple-700 dark:group-hover:text-purple-300 transition-colors">
                                Activity Logs
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">Audit trail</p>
                        </div>
                    </a>

                    <a href="{{ route('todos.index') }}"
                       class="flex items-center gap-3 rounded-xl border border-gray-200 dark:border-gray-700
                              bg-white dark:bg-gray-800 shadow-sm px-4 py-3
                              hover:bg-blue-50 hover:border-blue-200
                              dark:hover:bg-blue-900/20 dark:hover:border-blue-700
                              transition-colors group">
                        <div class="h-9 w-9 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center shrink-0
                                    group-hover:bg-blue-100 dark:group-hover:bg-blue-900/40 transition-colors">
                            <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none"
                                 stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200
                                      group-hover:text-blue-700 dark:group-hover:text-blue-300 transition-colors">
                                All Tasks
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">Browse &amp; manage</p>
                        </div>
                    </a>

                    <a href="{{ route('profile.edit') }}"
                       class="flex items-center gap-3 rounded-xl border border-gray-200 dark:border-gray-700
                              bg-white dark:bg-gray-800 shadow-sm px-4 py-3
                              hover:bg-gray-50 dark:hover:bg-gray-700/40
                              transition-colors group">
                        <div class="h-9 w-9 rounded-lg bg-gray-100 dark:bg-gray-700/60 flex items-center justify-center shrink-0">
                            <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none"
                                 stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 transition-colors">
                                My Profile
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">Account settings</p>
                        </div>
                    </a>

                </div>{{-- /quick actions --}} -->

            </div>{{-- /grid --}}
        </div>
    </div>
</x-app-layout>
