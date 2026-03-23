<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    StagStack Dashboard
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    Welcome back, {{ auth()->user()->name }}
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
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4">

                @if (session('success'))
                    <div class="col-span-2 md:col-span-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900/40 dark:bg-emerald-900/20 dark:text-emerald-300">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- ── Row 1: Stat Cards ───────────────────────────────────── --}}

                <div class="col-span-2 md:col-span-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-4">
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">Task Overview</p>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Core Metrics</h3>
                        </div>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $completionPct }}% completed</span>
                    </div>

                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                        <div class="rounded-lg border border-indigo-100 dark:border-indigo-900/30 bg-indigo-50/70 dark:bg-indigo-900/20 p-3">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-indigo-700 dark:text-indigo-300">Total Tasks</p>
                            <p class="mt-1 text-2xl font-bold text-indigo-900 dark:text-indigo-100 tabular-nums">{{ $totalTodos }}</p>
                        </div>

                        <div class="rounded-lg border border-emerald-100 dark:border-emerald-900/30 bg-emerald-50/70 dark:bg-emerald-900/20 p-3">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-300">Completed</p>
                            <p class="mt-1 text-2xl font-bold text-emerald-900 dark:text-emerald-100 tabular-nums">{{ $completedTodos }}</p>
                        </div>

                        <div class="rounded-lg border border-amber-100 dark:border-amber-900/30 bg-amber-50/70 dark:bg-amber-900/20 p-3">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-amber-700 dark:text-amber-300">Pending</p>
                            <p class="mt-1 text-2xl font-bold text-amber-900 dark:text-amber-100 tabular-nums">{{ $pendingTodos }}</p>
                        </div>

                        <div class="rounded-lg border border-red-100 dark:border-red-900/30 bg-red-50/70 dark:bg-red-900/20 p-3">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-red-700 dark:text-red-300">Overdue</p>
                            <p class="mt-1 text-2xl font-bold text-red-900 dark:text-red-100 tabular-nums">{{ $overdueTodos }}</p>
                            <p class="text-[11px] mt-1 text-red-700/80 dark:text-red-300/80">{{ $overdueTodos > 0 ? 'Needs attention' : 'All clear' }}</p>
                        </div>
                    </div>
                </div>

                <x-dashboard.stat-card
                    label="Current Streak"
                    :value="$dailyStreak"
                    icon-bg="bg-orange-50 dark:bg-orange-900/20"
                    :sublabel="$dailyStreak . ' day(s) in a row'">
                    <x-slot:icon>
                        <svg class="h-5 w-5 text-orange-600 dark:text-orange-400" fill="none"
                             stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15.362 5.214A8.252 8.252 0 0112 5.25c-2.29 0-4.34.932-5.82 2.436m0 0A8.25 8.25 0 104.5 12c0-1.03.19-2.015.537-2.923m0 0A8.223 8.223 0 016.18 7.686m0 0L3.75 6m2.43 1.686L6.75 3.75" />
                        </svg>
                    </x-slot:icon>
                </x-dashboard.stat-card>

                <div
                    class="col-span-2 md:col-span-1 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-4"
                    x-data="{
                        editMode: false,
                        loading: false,
                        error: '',
                        weeklyGoal: {{ (int) $weeklyGoal }},
                        currentGoal: {{ (int) $weeklyGoal }},
                        async saveGoal() {
                            this.error = '';
                            this.loading = true;
                            try {
                                const response = await fetch('{{ route('home.weekly-goal') }}', {
                                    method: 'PATCH',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content'),
                                    },
                                    body: JSON.stringify({ weekly_goal: this.weeklyGoal }),
                                });

                                const payload = await response.json();
                                if (!response.ok) {
                                    this.error = payload.message ?? 'Unable to update weekly goal.';
                                    return;
                                }

                                this.currentGoal = payload.weekly_goal;
                                this.weeklyGoal = payload.weekly_goal;
                                this.editMode = false;
                                window.location.reload();
                            } catch (e) {
                                this.error = 'Something went wrong while updating the goal.';
                            } finally {
                                this.loading = false;
                            }
                        }
                    }"
                >
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">Weekly Goal</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white tabular-nums leading-none mt-1">{{ $weeklyCompleted }}/<span x-text="currentGoal"></span></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $weeklyGoalPct }}% complete</p>
                        </div>

                        <button
                            type="button"
                            @click="editMode = !editMode; error = ''; weeklyGoal = currentGoal"
                            class="h-8 w-8 rounded-lg bg-violet-50 dark:bg-violet-900/20 text-violet-600 dark:text-violet-300 flex items-center justify-center hover:bg-violet-100 dark:hover:bg-violet-900/40 transition-colors"
                            aria-label="Edit weekly goal"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.1 2.1 0 112.97 2.97L7.5 18.79l-4.5 1.5 1.5-4.5L16.862 3.487z" />
                            </svg>
                        </button>
                    </div>

                    <div x-show="editMode" x-transition class="mt-3 border-t border-gray-100 dark:border-gray-700 pt-3 space-y-2">
                        <label class="text-xs font-medium text-gray-600 dark:text-gray-300" for="weekly_goal">Set weekly target</label>
                        <input
                            id="weekly_goal"
                            type="number"
                            min="1"
                            max="999"
                            x-model.number="weeklyGoal"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"
                        >
                        <p x-show="error" x-text="error" class="text-xs text-red-600 dark:text-red-400"></p>
                        <div class="flex gap-2">
                            <button
                                type="button"
                                @click="saveGoal"
                                :disabled="loading"
                                class="inline-flex items-center rounded-md bg-violet-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-violet-700 disabled:opacity-60"
                            >
                                <span x-text="loading ? 'Saving...' : 'Save'">Save</span>
                            </button>
                            <button
                                type="button"
                                @click="editMode = false; error = ''; weeklyGoal = currentGoal"
                                class="inline-flex items-center rounded-md bg-gray-200 dark:bg-gray-700 px-3 py-1.5 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600"
                            >
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-span-2 md:col-span-3">
                    <x-experience.progress-card
                        :experience="$experience"
                        title="Experience & Rank"
                    />
                </div>

                {{-- ── Row 2: Trend Chart + Completion Ring ─────────────────── --}}

                <div class="col-span-2 md:col-span-4 h-52">
                    <x-dashboard.trend-chart
                        :data="$trendData"
                        title="Task Activity — Last 7 Days"
                        color="blue" />
                </div>

                <div class="col-span-2 md:col-span-2 h-52">
                    <x-dashboard.completion-ring
                        :percentage="$completionPct"
                        label="My Progress"
                        sublabel="of tasks completed"
                        color="indigo" />
                </div>

                {{-- ── Row 3: Recent Todos + Quick Actions ──────────────────── --}}

                <div class="col-span-2 md:col-span-4 h-56">
                    <x-dashboard.recent-todos
                        :todos="$recentTodos"
                        title="Recent Tasks" />
                </div>

                {{-- Quick Actions --}}
                <div class="col-span-2 md:col-span-2 h-56">
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
