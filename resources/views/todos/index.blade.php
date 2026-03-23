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
                        <div id="todo-table-component" data-bulk-delete-url="{{ route('todos.bulk-destroy') }}">
                            <div class="flex items-center justify-between mb-3">
                                <p id="selection-status" class="text-sm text-gray-600 dark:text-gray-300">Selection mode is off</p>

                                <div class="relative" id="more-options-wrapper">
                                    <button
                                        id="more-options-button"
                                        type="button"
                                        class="inline-flex items-center justify-center rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 p-2 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                        aria-haspopup="true"
                                        aria-expanded="false"
                                        aria-label="More Options"
                                    >
                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path d="M10 3a1.75 1.75 0 1 1 0 3.5A1.75 1.75 0 0 1 10 3Zm0 5.25a1.75 1.75 0 1 1 0 3.5A1.75 1.75 0 0 1 10 8.25ZM11.75 15.25a1.75 1.75 0 1 0-3.5 0 1.75 1.75 0 0 0 3.5 0Z" />
                                        </svg>
                                    </button>

                                    <div
                                        id="more-options-menu"
                                        class="hidden absolute right-0 mt-2 w-52 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-lg z-30"
                                        role="menu"
                                        aria-labelledby="more-options-button"
                                    >
                                        <button type="button" id="action-select-mode" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem">
                                            Select Mode
                                        </button>
                                        <button type="button" id="action-select-all" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem">
                                            Select All
                                        </button>
                                        <button type="button" id="action-delete-all" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20" role="menuitem">
                                            Delete All
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div id="selection-action-bar" class="hidden mb-3 rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-3 dark:border-indigo-700 dark:bg-indigo-900/20">
                                <div class="flex items-center justify-between gap-3">
                                    <p id="selection-action-count" class="text-sm font-medium text-indigo-700 dark:text-indigo-300">0 selected</p>
                                    <button
                                        type="button"
                                        id="action-delete-selected"
                                        class="inline-flex items-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-50"
                                    >
                                        Delete Selected
                                    </button>
                                </div>
                            </div>

                            <div id="table-action-feedback" class="hidden rounded-md border px-4 py-3 text-sm mb-3"></div>

                            <div class="overflow-x-auto">
                            <table class="min-w-full w-full divide-y divide-gray-200 dark:divide-gray-700" style="table-layout: fixed;">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="selection-column hidden w-14 px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            <input
                                                id="header-row-selector"
                                                type="checkbox"
                                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700"
                                                data-no-row-click
                                                aria-label="Select all visible todos"
                                            >
                                        </th>
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
                                <tbody id="todo-table-body" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($todos as $todo)
                                        <tr
                                            class="js-todo-row hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer"
                                            data-row-url="{{ route('todos.show', $todo) }}"
                                            data-todo-id="{{ $todo->id }}"
                                            tabindex="0"
                                            role="link"
                                            aria-label="Open details for {{ $todo->title }}"
                                        >
                                            <td class="selection-column hidden px-3 py-4 whitespace-nowrap text-center" data-no-row-click>
                                                <input
                                                    type="checkbox"
                                                    class="row-selector rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700"
                                                    value="{{ $todo->id }}"
                                                    data-no-row-click
                                                    aria-label="Select {{ $todo->title }}"
                                                >
                                            </td>
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
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-6" id="todos-pagination-wrapper">
                            {{ $todos->links() }}
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No todos yet. Create your first one!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div id="delete-confirm-modal" class="hidden fixed inset-0 z-50">
        <div class="absolute inset-0 bg-black/40"></div>
        <div class="relative flex items-center justify-center min-h-screen px-4">
            <div class="w-full max-w-md rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-xl p-5">
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Confirm Deletion</h3>
                <p id="delete-confirm-message" class="mt-2 text-sm text-gray-600 dark:text-gray-300"></p>
                <div class="mt-4 flex items-center justify-end gap-2">
                    <button type="button" id="cancel-delete" class="rounded-md bg-gray-200 dark:bg-gray-700 px-3 py-2 text-sm text-gray-800 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600">Cancel</button>
                    <button type="button" id="confirm-delete" class="rounded-md bg-red-600 px-3 py-2 text-sm text-white hover:bg-red-700">Delete</button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tableComponent = document.getElementById('todo-table-component');
    if (!tableComponent) {
        return;
    }

    const tableBody = document.getElementById('todo-table-body');
    const selectionStatus = document.getElementById('selection-status');
    const feedback = document.getElementById('table-action-feedback');

    const menuWrapper = document.getElementById('more-options-wrapper');
    const menuButton = document.getElementById('more-options-button');
    const menu = document.getElementById('more-options-menu');

    const selectModeButton = document.getElementById('action-select-mode');
    const selectAllButton = document.getElementById('action-select-all');
    const deleteAllButton = document.getElementById('action-delete-all');
    const deleteSelectedButton = document.getElementById('action-delete-selected');
    const headerRowSelector = document.getElementById('header-row-selector');

    const selectionActionBar = document.getElementById('selection-action-bar');
    const selectionActionCount = document.getElementById('selection-action-count');

    const modal = document.getElementById('delete-confirm-modal');
    const modalMessage = document.getElementById('delete-confirm-message');
    const cancelDeleteButton = document.getElementById('cancel-delete');
    const confirmDeleteButton = document.getElementById('confirm-delete');

    const paginationWrapper = document.getElementById('todos-pagination-wrapper');

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const bulkDeleteUrl = tableComponent.dataset.bulkDeleteUrl;

    let selectMode = false;
    let selectedIds = new window.Set();
    let deleteAllVisibleFallback = false;

    const getRows = () => Array.from(tableBody.querySelectorAll('tr.js-todo-row[data-todo-id]'));

    const getVisibleIds = () => getRows()
        .map((row) => Number(row.dataset.todoId))
        .filter((id) => Number.isInteger(id) && id > 0);

    const closeMenu = () => {
        menu.classList.add('hidden');
        menuButton.setAttribute('aria-expanded', 'false');
    };

    const showFeedback = (type, message) => {
        const typeClasses = {
            success: 'border-green-300 bg-green-50 text-green-700 dark:border-green-700 dark:bg-green-900/20 dark:text-green-300',
            error: 'border-red-300 bg-red-50 text-red-700 dark:border-red-700 dark:bg-red-900/20 dark:text-red-300',
        };

        feedback.className = `rounded-md border px-4 py-3 text-sm mb-3 ${typeClasses[type] || typeClasses.error}`;
        feedback.textContent = message;
        feedback.classList.remove('hidden');
    };

    const updateSelectionStatus = () => {
        if (!selectMode) {
            selectionStatus.textContent = 'Selection mode is off';
            return;
        }

        selectionStatus.textContent = `${selectedIds.size} selected`;
    };

    const updateActionBar = () => {
        const shouldShow = selectMode && selectedIds.size > 0;

        selectionActionBar.classList.toggle('hidden', !shouldShow);
        selectionActionCount.textContent = `${selectedIds.size} selected`;
        deleteSelectedButton.disabled = selectedIds.size === 0;
    };

    const updateSelectModeLabel = () => {
        selectModeButton.textContent = selectMode ? 'Exit Select Mode' : 'Select Mode';
    };

    const updateSelectAllLabel = () => {
        const visibleIds = getVisibleIds();
        const allVisibleSelected = visibleIds.length > 0 && visibleIds.every((id) => selectedIds.has(id));

        selectAllButton.textContent = allVisibleSelected ? 'Unselect All' : 'Select All';
    };

    const updateHeaderCheckboxState = () => {
        const visibleIds = getVisibleIds();
        const visibleCount = visibleIds.length;
        const selectedVisibleCount = visibleIds.filter((id) => selectedIds.has(id)).length;

        headerRowSelector.disabled = !selectMode || visibleCount === 0;

        if (!selectMode || visibleCount === 0) {
            headerRowSelector.checked = false;
            headerRowSelector.indeterminate = false;
            return;
        }

        headerRowSelector.checked = selectedVisibleCount === visibleCount;
        headerRowSelector.indeterminate = selectedVisibleCount > 0 && selectedVisibleCount < visibleCount;
    };

    const updateMoreOptionsAvailability = () => {
        const hasRows = getRows().length > 0;

        selectModeButton.disabled = !hasRows;
        selectAllButton.disabled = !hasRows;
        deleteAllButton.disabled = !hasRows;

        if (!hasRows) {
            selectMode = false;
            tableComponent.dataset.selectMode = '0';
            selectedIds.clear();
            syncCheckboxVisibility();
            syncRowSelectionUi();
        }
    };

    const updateBulkUiState = () => {
        updateSelectionStatus();
        updateActionBar();
        updateSelectModeLabel();
        updateSelectAllLabel();
        updateHeaderCheckboxState();
        updateMoreOptionsAvailability();
    };

    const syncCheckboxVisibility = () => {
        document.querySelectorAll('.selection-column').forEach((cell) => {
            cell.classList.toggle('hidden', !selectMode);
        });
    };

    const syncRowSelectionUi = () => {
        getRows().forEach((row) => {
            const id = Number(row.dataset.todoId);
            const isSelected = selectMode && selectedIds.has(id);
            const checkbox = row.querySelector('.row-selector');

            if (checkbox) {
                checkbox.checked = isSelected;
            }

            row.classList.toggle('bg-indigo-50', isSelected);
            row.classList.toggle('dark:bg-indigo-900/20', isSelected);
        });
    };

    const renderEmptyStateIfNeeded = () => {
        if (getRows().length > 0) {
            return;
        }

        tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">No todos left in this view.</td>
            </tr>
        `;

        if (paginationWrapper) {
            paginationWrapper.classList.add('hidden');
        }

        selectedIds.clear();
        updateBulkUiState();
    };

    const setSelectMode = (enabled) => {
        selectMode = enabled;
        tableComponent.dataset.selectMode = enabled ? '1' : '0';

        if (!enabled) {
            selectedIds.clear();
        }

        syncCheckboxVisibility();
        syncRowSelectionUi();
        updateBulkUiState();
    };

    const openDeleteModal = (message, deleteAllVisible) => {
        deleteAllVisibleFallback = deleteAllVisible;
        modalMessage.textContent = message;
        modal.classList.remove('hidden');
    };

    const closeDeleteModal = () => {
        modal.classList.add('hidden');
        deleteAllVisibleFallback = false;
    };

    tableBody.addEventListener('change', function (event) {
        const checkbox = event.target.closest('.row-selector');
        if (!checkbox) {
            return;
        }

        const id = Number(checkbox.value);
        if (!Number.isInteger(id) || id <= 0) {
            return;
        }

        if (checkbox.checked) {
            selectedIds.add(id);
        } else {
            selectedIds.delete(id);
        }

        syncRowSelectionUi();
        updateBulkUiState();
    });

    headerRowSelector.addEventListener('change', function () {
        if (!selectMode) {
            return;
        }

        const visibleIds = getVisibleIds();
        if (headerRowSelector.checked) {
            visibleIds.forEach((id) => selectedIds.add(id));
        } else {
            visibleIds.forEach((id) => selectedIds.delete(id));
        }

        syncRowSelectionUi();
        updateBulkUiState();
    });

    selectModeButton.addEventListener('click', function () {
        setSelectMode(!selectMode);
        closeMenu();
    });

    selectAllButton.addEventListener('click', function () {
        if (!selectMode) {
            setSelectMode(true);
        }

        const visibleIds = getVisibleIds();
        const allVisibleSelected = visibleIds.length > 0 && visibleIds.every((id) => selectedIds.has(id));

        if (allVisibleSelected) {
            visibleIds.forEach((id) => selectedIds.delete(id));
        } else {
            visibleIds.forEach((id) => selectedIds.add(id));
        }

        syncRowSelectionUi();
        updateBulkUiState();
        closeMenu();
    });

    deleteSelectedButton.addEventListener('click', function () {
        const selectedCount = selectedIds.size;
        if (selectedCount === 0) {
            return;
        }

        openDeleteModal(`Delete ${selectedCount} selected todo${selectedCount > 1 ? 's' : ''}? This action cannot be undone.`, false);
    });

    deleteAllButton.addEventListener('click', function () {
        const selectedCount = selectedIds.size;
        const visibleCount = getVisibleIds().length;

        if (visibleCount === 0) {
            closeMenu();
            return;
        }

        if (selectedCount > 0) {
            openDeleteModal(`Delete ${selectedCount} selected todo${selectedCount > 1 ? 's' : ''}? This action cannot be undone.`, false);
        } else {
            openDeleteModal(`No rows are selected. Delete all ${visibleCount} visible todo${visibleCount > 1 ? 's' : ''}? This action cannot be undone.`, true);
        }

        closeMenu();
    });

    confirmDeleteButton.addEventListener('click', async function () {
        const selectedCount = selectedIds.size;
        const visibleIds = getVisibleIds();

        const payload = deleteAllVisibleFallback
            ? { delete_all_visible: true, visible_ids: visibleIds }
            : { todo_ids: Array.from(selectedIds) };

        try {
            const response = await fetch(bulkDeleteUrl, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                },
                body: JSON.stringify(payload),
            });

            const result = await response.json();

            if (!response.ok) {
                showFeedback('error', result.message || 'Unable to delete todos.');
                closeDeleteModal();
                return;
            }

            const deletedIds = new window.Set((result.deleted_ids || []).map((id) => Number(id)));

            getRows().forEach((row) => {
                const id = Number(row.dataset.todoId);
                if (deletedIds.has(id)) {
                    row.remove();
                }
            });

            deletedIds.forEach((id) => selectedIds.delete(id));

            if (!deleteAllVisibleFallback && selectedCount > 0 && selectMode) {
                syncRowSelectionUi();
            }

            updateBulkUiState();
            renderEmptyStateIfNeeded();
            showFeedback('success', result.message || 'Todos deleted successfully.');
        } catch (error) {
            showFeedback('error', 'Something went wrong while deleting todos. Please try again.');
        } finally {
            closeDeleteModal();
        }
    });

    cancelDeleteButton.addEventListener('click', closeDeleteModal);

    modal.addEventListener('click', function (event) {
        if (event.target === modal || event.target.classList.contains('bg-black/40')) {
            closeDeleteModal();
        }
    });

    menuButton.addEventListener('click', function () {
        const isOpen = !menu.classList.contains('hidden');

        if (isOpen) {
            closeMenu();
            return;
        }

        menu.classList.remove('hidden');
        menuButton.setAttribute('aria-expanded', 'true');
    });

    document.addEventListener('click', function (event) {
        if (!menuWrapper.contains(event.target)) {
            closeMenu();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeMenu();
            closeDeleteModal();
        }
    });

    setSelectMode(false);
    updateBulkUiState();
});

document.addEventListener('click', function (e) {
    const row = e.target.closest('tr[data-row-url]');
    if (!row) return;

    // Prevent row navigation when clicking interactive elements
    if (e.target.closest('a, button, input, select, textarea, form, [data-no-row-click]')) {
        return;
    }

    const tableComponent = row.closest('#todo-table-component');
    if (tableComponent && tableComponent.dataset.selectMode === '1') {
        return;
    }

    window.location.href = row.dataset.rowUrl;
});

document.addEventListener('keydown', function (e) {
    const row = e.target.closest('tr[data-row-url]');
    if (!row) return;

    if (e.target.closest('a, button, input, select, textarea, form, [data-no-row-click]')) {
        return;
    }

    const tableComponent = row.closest('#todo-table-component');
    if (tableComponent && tableComponent.dataset.selectMode === '1') {
        return;
    }

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
