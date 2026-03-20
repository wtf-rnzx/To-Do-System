<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Activity Logs') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ openFilters: false }" @keydown.escape.window="openFilters = false">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- ── Activity Feed ────────────────────────────────────────────────── --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 pt-5 pb-1 flex items-center justify-between border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100">Activity Log</h3>
                    <div class="relative flex items-center gap-3">
                        <span class="text-sm text-gray-400 dark:text-gray-500">
                            {{ number_format($logs->total()) }} {{ \Illuminate\Support\Str::plural('entry', $logs->total()) }}
                        </span>

                        <button type="button"
                                class="relative inline-flex items-center justify-center rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 p-2 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                @click="openFilters = !openFilters"
                                :aria-expanded="openFilters.toString()"
                                aria-controls="activity-filter-panel"
                                aria-label="Open activity log filters">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4.5h18M6.75 12h10.5M10.5 19.5h3" />
                            </svg>

                            @if ($hasActiveFilters ?? false)
                                <span class="absolute -top-1 -right-1 min-w-[1rem] h-4 px-1 rounded-full bg-indigo-600 text-white text-[10px] leading-4 text-center font-semibold ring-2 ring-white dark:ring-gray-800">
                                    {{ $activeFilterCount }}
                                </span>
                            @endif
                        </button>

                        <div id="activity-filter-panel"
                             x-cloak
                             x-show="openFilters"
                             x-transition.origin.top.right
                             @click.outside="openFilters = false"
                             class="absolute right-0 top-10 z-40 w-[24rem] max-w-[92vw] rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-xl"
                             role="dialog"
                             aria-label="Activity log filters">
                            <form method="GET" action="{{ route('admin.logs.index') }}" class="p-4 space-y-4">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Filters</h4>
                                    <a href="{{ route('admin.logs.index') }}" class="text-xs font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                        Clear
                                    </a>
                                </div>

                                <div>
                                    <label for="activity-user" class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">User</label>
                                    <select id="activity-user"
                                            name="user_id"
                                            class="mt-2 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                                        <option value="">All Users</option>
                                        @foreach ($users as $u)
                                            <option value="{{ $u->id }}" {{ (string)($selectedUserId ?? '') === (string)$u->id ? 'selected' : '' }}>
                                                {{ $u->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <fieldset>
                                    <legend class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Action</legend>
                                    <div class="mt-2 grid grid-cols-2 gap-2 text-sm max-h-32 overflow-y-auto pr-1">
                                        @foreach ($actions as $act)
                                            <label class="inline-flex items-center gap-2 rounded-md px-2 py-1.5 hover:bg-gray-50 dark:hover:bg-gray-700/60 cursor-pointer">
                                                <input type="checkbox"
                                                       name="actions[]"
                                                       value="{{ $act }}"
                                                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700"
                                                       {{ in_array($act, $selectedActions ?? [], true) ? 'checked' : '' }}>
                                                <span class="text-gray-700 dark:text-gray-200">{{ ucfirst(str_replace('_', ' ', $act)) }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </fieldset>

                                <fieldset>
                                    <legend class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Module</legend>
                                    <div class="mt-2 grid grid-cols-2 gap-2 text-sm">
                                        @foreach ($modules as $mod)
                                            <label class="inline-flex items-center gap-2 rounded-md px-2 py-1.5 hover:bg-gray-50 dark:hover:bg-gray-700/60 cursor-pointer">
                                                <input type="checkbox"
                                                       name="modules[]"
                                                       value="{{ $mod }}"
                                                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700"
                                                       {{ in_array($mod, $selectedModules ?? [], true) ? 'checked' : '' }}>
                                                <span class="text-gray-700 dark:text-gray-200">{{ ucfirst($mod) }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </fieldset>

                                <div>
                                    <label for="activity-sort" class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Sort</label>
                                    <select id="activity-sort"
                                            name="sort"
                                            class="mt-2 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                                        <option value="newest" {{ ($selectedSort ?? 'newest') === 'newest' ? 'selected' : '' }}>Newest First</option>
                                        <option value="oldest" {{ ($selectedSort ?? 'newest') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                                    </select>
                                </div>

                                <fieldset>
                                    <legend class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Date Range</legend>
                                    <div class="mt-2 grid grid-cols-2 gap-2">
                                        <div>
                                            <label for="activity-from" class="sr-only">From date</label>
                                            <input id="activity-from" type="date" name="from" value="{{ $from ?? '' }}"
                                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm" />
                                        </div>
                                        <div>
                                            <label for="activity-to" class="sr-only">To date</label>
                                            <input id="activity-to" type="date" name="to" value="{{ $to ?? '' }}"
                                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm" />
                                        </div>
                                    </div>
                                </fieldset>

                                <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                                    <a href="{{ route('admin.logs.index') }}"
                                       class="inline-flex items-center rounded-md bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 text-sm font-medium py-2 px-3">
                                        Clear
                                    </a>
                                    <button type="submit"
                                            class="inline-flex items-center rounded-md bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium py-2 px-3">
                                        Apply
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                @php $prevDate = null; @endphp

                @forelse ($logs as $log)
                    @php
                        $dateKey = $log->created_at->toDateString();

                        // ── Dot + icon config per action ──────────────────────
                        [$dotClass, $iconPath] = match ($log->action) {
                            'login' => [
                                'bg-emerald-500',
                                'M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75',
                            ],
                            'logout' => [
                                'bg-slate-400',
                                'M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m-6-5.25h13.5m0 0l-3-3m3 3l-3 3',
                            ],
                            'created' => [
                                'bg-blue-500',
                                'M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z',
                            ],
                            'updated' => [
                                'bg-amber-500',
                                'M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10',
                            ],
                            'deleted' => [
                                'bg-red-500',
                                'M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0',
                            ],
                            'toggled' => [
                                'bg-violet-500',
                                'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                            ],
                            'role_updated' => [
                                'bg-orange-500',
                                'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z',
                            ],
                            'profile_updated' => [
                                'bg-teal-500',
                                'M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z',
                            ],
                            'account_deleted' => [
                                'bg-rose-600',
                                'M22 10.5h-6m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z',
                            ],
                            default => [
                                'bg-gray-400',
                                'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z',
                            ],
                        };

                        // ── Avatar colour, stable by first letter ─────────────
                        $fl = strtolower(substr($log->user_name ?? 'x', 0, 1));
                        $avatarBg = match (true) {
                            in_array($fl, ['a','b','c'])         => 'bg-sky-500',
                            in_array($fl, ['d','e','f'])         => 'bg-violet-500',
                            in_array($fl, ['g','h','i'])         => 'bg-emerald-500',
                            in_array($fl, ['j','k','l'])         => 'bg-amber-500',
                            in_array($fl, ['m','n','o'])         => 'bg-pink-500',
                            in_array($fl, ['p','q','r'])         => 'bg-indigo-500',
                            in_array($fl, ['s','t','u'])         => 'bg-orange-500',
                            default                              => 'bg-teal-500',
                        };
                    @endphp

                    {{-- ── Date Separator ──────────────────────────────────────── --}}
                    @if ($dateKey !== $prevDate)
                        @php $prevDate = $dateKey; @endphp
                        <div class="flex items-center gap-3 px-6 py-2 bg-gray-50 dark:bg-gray-700/40
                                    {{ $loop->first ? 'border-b' : 'border-y' }}
                                    border-gray-100 dark:border-gray-700">
                            <span class="text-[11px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 shrink-0">
                                @if ($log->created_at->isToday())
                                    Today
                                @elseif ($log->created_at->isYesterday())
                                    Yesterday
                                @else
                                    {{ $log->created_at->format('l, F j Y') }}
                                @endif
                            </span>
                            <div class="flex-1 h-px bg-gray-200 dark:bg-gray-600"></div>
                        </div>
                    @endif

                    {{-- ── Feed Row ─────────────────────────────────────────────── --}}
                    <div class="relative flex gap-4 px-6 py-4
                                hover:bg-gray-50 dark:hover:bg-gray-700/20 transition-colors
                                border-b border-gray-100 dark:border-gray-700/60">

                        {{-- Timeline spine --}}
                        @if (!$loop->last)
                            <div class="absolute left-[2.35rem] top-[3.5rem] bottom-0 w-px bg-gray-200 dark:bg-gray-700 pointer-events-none"></div>
                        @endif

                        {{-- Action dot with icon --}}
                        <div class="shrink-0 z-10">
                            <div class="h-9 w-9 rounded-full {{ $dotClass }} flex items-center justify-center
                                        shadow-sm ring-2 ring-white dark:ring-gray-800">
                                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor"
                                     stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconPath }}" />
                                </svg>
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0 pt-1">

                            {{-- Header: user + badges + timestamp --}}
                            <div class="flex items-start justify-between gap-3 flex-wrap">
                                <div class="flex items-center gap-2 flex-wrap">

                                    {{-- Mini avatar + name --}}
                                    <div class="flex items-center gap-1.5">
                                        <div class="h-5 w-5 rounded-full {{ $avatarBg }} flex items-center justify-center
                                                    text-white text-[9px] font-bold uppercase select-none shrink-0">
                                            {{ strtoupper(substr($log->user_name ?? '?', 0, 1)) }}
                                        </div>
                                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                            {{ $log->user_name }}
                                        </span>
                                        @unless ($log->user_id)
                                            <span class="text-[10px] text-gray-400 dark:text-gray-500 italic">(deleted)</span>
                                        @endunless
                                    </div>

                                    {{-- Action badge --}}
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold
                                                 {{ $log->actionBadgeClass() }}">
                                        {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                    </span>

                                    {{-- Module badge --}}
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium
                                                 bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">
                                        {{ ucfirst($log->module) }}
                                    </span>
                                </div>

                                {{-- Timestamp --}}
                                <time class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap shrink-0 tabular-nums"
                                      datetime="{{ $log->created_at->toIso8601String() }}">
                                    {{ $log->created_at->format('M d, Y · h:i A') }}
                                </time>
                            </div>

                            {{-- Description --}}
                            <p class="mt-1.5 text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                {{ $log->description }}
                            </p>

                            {{-- Metadata row --}}
                            @if ($log->ip_address)
                                <div class="mt-2 flex items-center gap-1.5 text-[11px] text-gray-400 dark:text-gray-500">
                                    <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor"
                                         stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" />
                                    </svg>
                                    <span class="font-mono">{{ $log->ip_address }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                @empty
                    {{-- Empty state --}}
                    <div class="px-6 py-16 text-center">
                        <div class="mx-auto mb-4 h-14 w-14 rounded-2xl bg-gray-100 dark:bg-gray-700
                                    flex items-center justify-center">
                            <svg class="h-7 w-7 text-gray-400 dark:text-gray-500" fill="none"
                                 stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">No activity logs found.</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Try adjusting your filters.</p>
                        @if (request()->hasAny(['from','to','user_id','actions','modules','sort']))
                            <a href="{{ route('admin.logs.index') }}"
                               class="mt-3 inline-flex items-center gap-1 text-xs text-blue-500 hover:text-blue-600 font-medium">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Clear all filters
                            </a>
                        @endif
                    </div>
                @endforelse

                {{-- Pagination --}}
                @if ($logs->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                        {{ $logs->links() }}
                    </div>
                @endif

            </div>{{-- /feed card --}}

        </div>
    </div>
</x-app-layout>

<style>
[x-cloak] {
    display: none !important;
}
</style>
