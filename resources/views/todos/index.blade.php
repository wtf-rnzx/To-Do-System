<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center"
             x-data="{ openFilters: false }"
             @keydown.escape.window="openFilters = false">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('My Todos') }}
            </h2>

            <div class="relative flex items-center gap-2">
                <button
                    type="button"
                    class="relative inline-flex items-center justify-center rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 p-2.5 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    @click="openFilters = !openFilters"
                    :aria-expanded="openFilters.toString()"
                    aria-controls="todos-filter-panel"
                    aria-label="Open filters"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4.5h18M6.75 12h10.5M10.5 19.5h3" />
                    </svg>

                    @if ($hasActiveFilters ?? false)
                        <span class="absolute -top-0.5 -right-0.5 h-2.5 w-2.5 rounded-full bg-indigo-500 ring-2 ring-white dark:ring-gray-800" aria-hidden="true"></span>
                    @endif
                </button>

                <a href="{{ route('todos.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Add New Todo
                </a>

                <div
                    id="todos-filter-panel"
                    x-cloak
                    x-show="openFilters"
                    x-transition.origin.top.right
                    @click.outside="openFilters = false"
                    class="absolute right-0 top-12 z-40 w-[22rem] max-w-[90vw] rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-xl"
                    role="dialog"
                    aria-label="Todos filters"
                >
                    <form method="GET" action="{{ route('todos.index') }}" class="p-4 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Filters</h3>
                            <a href="{{ route('todos.index') }}"
                               class="text-xs font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                Clear
                            </a>
                        </div>

                        <fieldset>
                            <legend class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Smart View</legend>
                            <div class="mt-2 grid grid-cols-2 gap-2 text-sm">
                                @php $selectedSmartViews = $smartViews ?? ['all']; @endphp
                                @foreach (['all' => 'All', 'today' => 'Today', 'upcoming' => 'Upcoming', 'completed' => 'Completed'] as $value => $label)
                                    <label class="inline-flex items-center gap-2 rounded-md px-2 py-1.5 hover:bg-gray-50 dark:hover:bg-gray-700/60 cursor-pointer">
                                        <input type="checkbox"
                                               name="smart_views[]"
                                               value="{{ $value }}"
                                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700"
                                               {{ in_array($value, $selectedSmartViews, true) ? 'checked' : '' }}>
                                        <span class="text-gray-700 dark:text-gray-200">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>

                        <fieldset>
                            <legend class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</legend>
                            <div class="mt-2 grid grid-cols-2 gap-2 text-sm">
                                @php $selectedStatuses = $statuses ?? ['all']; @endphp
                                @foreach (['all' => 'All', 'pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed'] as $value => $label)
                                    <label class="inline-flex items-center gap-2 rounded-md px-2 py-1.5 hover:bg-gray-50 dark:hover:bg-gray-700/60 cursor-pointer">
                                        <input type="checkbox"
                                               name="statuses[]"
                                               value="{{ $value }}"
                                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700"
                                               {{ in_array($value, $selectedStatuses, true) ? 'checked' : '' }}>
                                        <span class="text-gray-700 dark:text-gray-200">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>

                        <fieldset>
                            <legend class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Priority</legend>
                            <div class="mt-2 grid grid-cols-2 gap-2 text-sm">
                                @php $selectedPriorities = $priorities ?? ['all']; @endphp
                                @foreach (['all' => 'All', 'low' => 'Low', 'medium' => 'Medium', 'high' => 'High'] as $value => $label)
                                    <label class="inline-flex items-center gap-2 rounded-md px-2 py-1.5 hover:bg-gray-50 dark:hover:bg-gray-700/60 cursor-pointer">
                                        <input type="checkbox"
                                               name="priorities[]"
                                               value="{{ $value }}"
                                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700"
                                               {{ in_array($value, $selectedPriorities, true) ? 'checked' : '' }}>
                                        <span class="text-gray-700 dark:text-gray-200">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>

                        <fieldset>
                            <legend class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Date Range</legend>
                            <div class="mt-2 grid grid-cols-2 gap-2">
                                <div>
                                    <label for="filter-from" class="sr-only">From</label>
                                    <input
                                        type="datetime-local"
                                        id="filter-from"
                                        name="from"
                                        value="{{ $from ?? '' }}"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"
                                    />
                                </div>
                                <div>
                                    <label for="filter-to" class="sr-only">To</label>
                                    <input
                                        type="datetime-local"
                                        id="filter-to"
                                        name="to"
                                        value="{{ $to ?? '' }}"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"
                                    />
                                </div>
                            </div>
                        </fieldset>

                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                            <a href="{{ route('todos.index') }}"
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
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success Message -->
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if($todos->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full w-full divide-y divide-gray-200 dark:divide-gray-700" style="table-layout: fixed;">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="w-20 px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th scope="col" class="w-4/12 px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Title
                                        </th>
                                        <th scope="col" class="w-2/12 px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Priority
                                        </th>
                                        <th scope="col" class="w-2/12 px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Due Date
                                        </th>
                                        <th scope="col" class="w-2/12 px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Created
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($todos as $todo)
                                        <tr
                                            class="hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer"
                                            data-row-url="{{ route('todos.show', $todo) }}"
                                            tabindex="0"
                                            role="link"
                                            aria-label="Open details for {{ $todo->title }}"
                                        >
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <!-- Toggle Complete -->
                                                <form action="{{ route('todos.toggle', $todo) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="focus:outline-none">
                                                        @if($todo->completed)
                                                            <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                            </svg>
                                                        @else
                                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <circle cx="12" cy="12" r="10" stroke-width="2"></circle>
                                                            </svg>
                                                        @endif
                                                    </button>
                                                </form>
                                            </td>
                                            <td class="px-6 py-4 align-top text-center">
                                                <!-- Todo Title -->
                                                <div
                                                    class="w-full text-sm whitespace-normal overflow-hidden [overflow-wrap:anywhere]"
                                                    style="word-break: break-word;"
                                                    title="{{ $todo->title }}"
                                                >
                                                    {{ $todo->title }}
                                                </div>

                                                <a href="{{ route('todos.show', $todo) }}" class="sr-only focus:not-sr-only focus:underline text-xs text-blue-600 dark:text-blue-400">
                                                    Open details for {{ $todo->title }}
                                                </a>

                                                @if(($todo->subtasks_count ?? 0) > 0)
                                                    <div class="text-xs mt-1 text-gray-500 dark:text-gray-400">
                                                        {{ $todo->completed_subtasks_count ?? 0 }}/{{ $todo->subtasks_count }} subtasks done
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                                @php
                                                    $priorityClass = match($todo->priority ?? 'medium') {
                                                        'high' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                                                        'low' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
                                                        default => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                                                    };
                                                @endphp
                                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold {{ $priorityClass }}">
                                                    {{ ucfirst($todo->priority ?? 'medium') }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                                                {{ $todo->due_date ? $todo->due_date->format('M d, Y') : 'No due date' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                                                {{ $todo->created_at->format('M d, Y h:i A') }}
                                            </td>
                                        </tr>

                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-6">
                            {{ $todos->links() }}
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No todos yet. Create your first one!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
document.addEventListener('click', function (e) {
    const row = e.target.closest('tr[data-row-url]');
    if (!row) return;

    // Prevent row navigation when clicking interactive elements
    if (e.target.closest('a, button, input, select, textarea, form, [data-no-row-click]')) {
        return;
    }

    window.location.href = row.dataset.rowUrl;
});

document.addEventListener('keydown', function (e) {
    const row = e.target.closest('tr[data-row-url]');
    if (!row) return;

    if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        window.location.href = row.dataset.rowUrl;
    }
});
</script>

<style>
[x-cloak] {
    display: none !important;
}
</style>
