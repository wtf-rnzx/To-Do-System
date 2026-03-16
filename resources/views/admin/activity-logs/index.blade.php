<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Activity Logs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- ── Filter Card ───────────────────────────────────────────────── --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-5">
                    <form method="GET" action="{{ route('admin.logs.index') }}"
                          class="flex flex-wrap items-end gap-3">

                        {{-- Search --}}
                        <div class="flex flex-col gap-1 flex-1 min-w-[180px]">
                            <label class="text-xs font-medium text-gray-600 dark:text-gray-300">Search</label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="User, description, IP…"
                                   class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm" />
                        </div>

                        {{-- Date From --}}
                        <div class="flex flex-col gap-1">
                            <label class="text-xs font-medium text-gray-600 dark:text-gray-300">From</label>
                            <input type="date" name="from" value="{{ request('from') }}"
                                   class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm" />
                        </div>

                        {{-- Date To --}}
                        <div class="flex flex-col gap-1">
                            <label class="text-xs font-medium text-gray-600 dark:text-gray-300">To</label>
                            <input type="date" name="to" value="{{ request('to') }}"
                                   class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm" />
                        </div>

                        {{-- User Filter --}}
                        <div class="flex flex-col gap-1">
                            <label class="text-xs font-medium text-gray-600 dark:text-gray-300">User</label>
                            <select name="user_id"
                                    class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                                <option value="">All Users</option>
                                @foreach ($users as $u)
                                    <option value="{{ $u->id }}"
                                            {{ request('user_id') == $u->id ? 'selected' : '' }}>
                                        {{ $u->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Action Filter --}}
                        <div class="flex flex-col gap-1">
                            <label class="text-xs font-medium text-gray-600 dark:text-gray-300">Action</label>
                            <select name="action"
                                    class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                                <option value="">All Actions</option>
                                @foreach ($actions as $act)
                                    <option value="{{ $act }}"
                                            {{ request('action') === $act ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $act)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Module Filter --}}
                        <div class="flex flex-col gap-1">
                            <label class="text-xs font-medium text-gray-600 dark:text-gray-300">Module</label>
                            <select name="module"
                                    class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                                <option value="">All Modules</option>
                                @foreach ($modules as $mod)
                                    <option value="{{ $mod }}"
                                            {{ request('module') === $mod ? 'selected' : '' }}>
                                        {{ ucfirst($mod) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Sort --}}
                        <div class="flex flex-col gap-1">
                            <label class="text-xs font-medium text-gray-600 dark:text-gray-300">Sort</label>
                            <select name="sort"
                                    class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                                <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>Newest First</option>
                                <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                                <option value="user"   {{ request('sort') === 'user'   ? 'selected' : '' }}>By User</option>
                            </select>
                        </div>

                        {{-- Buttons --}}
                        <div class="flex gap-2">
                            <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium py-2 px-4 rounded-md">
                                Apply
                            </button>
                            <a href="{{ route('admin.logs.index') }}"
                               class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 text-sm font-medium py-2 px-4 rounded-md">
                                Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ── Log Table ─────────────────────────────────────────────────── --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-lg font-medium">Activity Log</h3>
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ number_format($logs->total()) }} entr{{ $logs->total() === 1 ? 'y' : 'ies' }} found
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-10">#</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">User</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Action</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Module</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Description</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">IP Address</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Timestamp</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse ($logs as $log)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">

                                        {{-- Row number --}}
                                        <td class="px-4 py-3 text-gray-400 dark:text-gray-500 text-xs">
                                            {{ $logs->firstItem() + $loop->index }}
                                        </td>

                                        {{-- User --}}
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <div class="h-7 w-7 rounded-full bg-indigo-500 flex items-center justify-center text-white text-xs font-semibold uppercase shrink-0">
                                                    {{ substr($log->user_name, 0, 1) }}
                                                </div>
                                                <div class="leading-tight">
                                                    <p class="font-medium text-gray-900 dark:text-gray-100 text-xs">{{ $log->user_name }}</p>
                                                    @if ($log->user_id)
                                                        <p class="text-gray-400 dark:text-gray-500 text-[10px]">#{{ $log->user_id }}</p>
                                                    @else
                                                        <p class="text-gray-400 dark:text-gray-500 text-[10px] italic">deleted</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Action badge --}}
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $log->actionBadgeClass() }}">
                                                {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                            </span>
                                        </td>

                                        {{-- Module badge --}}
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                                {{ ucfirst($log->module) }}
                                            </span>
                                        </td>

                                        {{-- Description --}}
                                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300 max-w-xs">
                                            <span class="block truncate" title="{{ $log->description }}">
                                                {{ $log->description }}
                                            </span>
                                        </td>

                                        {{-- IP --}}
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-500 dark:text-gray-400 font-mono text-xs">
                                            {{ $log->ip_address ?? '—' }}
                                        </td>

                                        {{-- Timestamp --}}
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-500 dark:text-gray-400 text-xs">
                                            <span title="{{ $log->created_at->toDateTimeString() }}">
                                                {{ $log->created_at->format('M d, Y H:i') }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-16 text-center">
                                            <div class="flex flex-col items-center gap-2 text-gray-400 dark:text-gray-500">
                                                <svg class="w-10 h-10 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                <p class="text-sm">No activity logs found.</p>
                                                @if (request()->hasAny(['search','from','to','user_id','action','module']))
                                                    <a href="{{ route('admin.logs.index') }}"
                                                       class="text-xs text-blue-500 hover:underline">Clear filters</a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if ($logs->hasPages())
                        <div class="mt-5 border-t border-gray-100 dark:border-gray-700 pt-4">
                            {{ $logs->links() }}
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
