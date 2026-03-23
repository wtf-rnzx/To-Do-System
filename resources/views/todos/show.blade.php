<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Todo Details') }}
            </h2>

            <div class="flex items-center gap-2">
                <a href="{{ route('todos.index') }}"
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back
                </a>

                <form
                    action="{{ route('todos.destroy', $todo) }}"
                    method="POST"
                    onsubmit="return confirm('Delete this todo? This action cannot be undone.');"
                >
                    @csrf
                    @method('DELETE')
                    <button
                        type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                    >
                        Delete
                    </button>
                </form>

                <!-- <a href="{{ route('todos.edit', $todo) }}"
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Edit
                </a> -->
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div
                    class="p-6 text-gray-900 dark:text-gray-100 space-y-4"
                    x-data="todoDetailsEditor({
                        url: '{{ route('todos.update', $todo) }}',
                        completed: {{ $todo->completed ? 'true' : 'false' }},
                        title: @js($todo->title),
                        description: @js($todo->description),
                        priority: @js($todo->priority ?? 'medium'),
                        recurrence_type: @js($todo->recurrence_type),
                        due_date: @js(optional($todo->due_date)->format('Y-m-d')),
                    })"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <div class="text-xs uppercase text-gray-500 dark:text-gray-400">Title</div>
                            <template x-if="!editMode">
                                <div class="text-lg font-semibold break-words" x-text="form.title"></div>
                            </template>
                            <template x-if="editMode">
                                <input
                                    type="text"
                                    x-model="form.title"
                                    class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"
                                >
                            </template>

                            <div class="mt-3">
                                <div class="text-xs uppercase text-gray-500 dark:text-gray-400">Description</div>
                                <template x-if="!editMode">
                                    <div class="text-sm whitespace-pre-line" x-text="form.description || 'No description'"></div>
                                </template>
                                <template x-if="editMode">
                                    <textarea
                                        rows="4"
                                        x-model="form.description"
                                        class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"
                                    ></textarea>
                                </template>
                            </div>
                        </div>

                        <button
                            type="button"
                            @click="startEdit"
                            x-show="!editMode"
                            class="h-8 w-8 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-300 flex items-center justify-center hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors"
                            aria-label="Edit todo details"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.1 2.1 0 112.97 2.97L7.5 18.79l-4.5 1.5 1.5-4.5L16.862 3.487z" />
                            </svg>
                        </button>
                    </div>

                    <div x-show="saveMessage" x-transition class="rounded-md border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs text-emerald-700 dark:border-emerald-900/40 dark:bg-emerald-900/20 dark:text-emerald-300" x-text="saveMessage"></div>

                    <template x-if="Object.keys(errors).length > 0">
                        <div class="rounded-md border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-700 dark:border-red-900/40 dark:bg-red-900/20 dark:text-red-300">
                            <template x-for="(errorMessages, field) in errors" :key="field">
                                <p x-text="errorMessages[0]"></p>
                            </template>
                        </div>
                    </template>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs uppercase text-gray-500 dark:text-gray-400">Status</div>
                            <div class="text-sm" x-text="isCompleted ? 'Completed' : 'Ongoing'"></div>
                        </div>

                        <div>
                            <div class="text-xs uppercase text-gray-500 dark:text-gray-400">Priority</div>
                            <template x-if="!editMode">
                                <div class="text-sm font-medium" x-text="(form.priority || 'medium').charAt(0).toUpperCase() + (form.priority || 'medium').slice(1)"></div>
                            </template>
                            <template x-if="editMode">
                                <select x-model="form.priority" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </template>
                        </div>

                        <div>
                            <div class="text-xs uppercase text-gray-500 dark:text-gray-400">Recurrence</div>
                            <template x-if="!editMode">
                                <div class="text-sm" x-text="form.recurrence_type ? form.recurrence_type.charAt(0).toUpperCase() + form.recurrence_type.slice(1) : 'No recurrence'"></div>
                            </template>
                            <template x-if="editMode">
                                <select x-model="form.recurrence_type" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                                    <option value="">Does not repeat</option>
                                    <option value="daily">Daily</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly">Monthly</option>
                                </select>
                            </template>
                        </div>

                        <div>
                            <div class="text-xs uppercase text-gray-500 dark:text-gray-400">Due Date</div>
                            <template x-if="!editMode">
                                <div class="text-sm" x-text="form.due_date || 'No due date'"></div>
                            </template>
                            <template x-if="editMode">
                                <input type="date" x-model="form.due_date" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                            </template>
                        </div>

                        <div>
                            <div class="text-xs uppercase text-gray-500 dark:text-gray-400">Created</div>
                            <div class="text-sm">{{ $todo->created_at->format('M d, Y') }}</div>
                        </div>

                        <div>
                            <div class="text-xs uppercase text-gray-500 dark:text-gray-400">Last Updated</div>
                            <div class="text-sm">{{ $todo->updated_at->format('M d, Y') }}</div>
                        </div>
                    </div>

                    <div x-show="editMode" x-transition class="flex items-center gap-2 pt-2">
                        <button
                            type="button"
                            @click="save"
                            :disabled="saving"
                            class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700 disabled:opacity-60"
                        >
                            <span x-text="saving ? 'Saving...' : 'Save'"></span>
                        </button>
                        <button
                            type="button"
                            @click="cancelEdit"
                            :disabled="saving"
                            class="inline-flex items-center rounded-md bg-gray-200 dark:bg-gray-700 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600"
                        >
                            Cancel
                        </button>
                    </div>

                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300">Checklist</h3>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $todo->subtasks->where('completed', true)->count() }}/{{ $todo->subtasks->count() }} done
                            </span>
                        </div>

                        <form action="{{ route('todos.subtasks.store', $todo) }}" method="POST" class="flex items-center gap-2 mb-3">
                            @csrf
                            <input
                                type="text"
                                name="title"
                                class="w-full px-3 py-2 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"
                                placeholder="Add a subtask..."
                                required
                            >
                            <button type="submit" class="px-3 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white text-sm">Add</button>
                        </form>

                        <div class="space-y-2">
                            @forelse($todo->subtasks as $subtask)
                                <div class="flex items-center gap-2 rounded-md border border-gray-200 dark:border-gray-700 px-3 py-2">
                                    <form action="{{ route('todos.subtasks.toggle', [$todo, $subtask]) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-sm {{ $subtask->completed ? 'text-green-600' : 'text-gray-400' }}">
                                            {{ $subtask->completed ? '✓' : '○' }}
                                        </button>
                                    </form>

                                    <form action="{{ route('todos.subtasks.update', [$todo, $subtask]) }}" method="POST" class="flex-1">
                                        @csrf
                                        @method('PATCH')
                                        <input type="text" name="title" value="{{ $subtask->title }}"
                                               class="w-full bg-transparent border-0 p-0 text-sm focus:ring-0 {{ $subtask->completed ? 'line-through text-gray-400' : '' }}" />
                                    </form>

                                    <form action="{{ route('todos.subtasks.destroy', [$todo, $subtask]) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-600 hover:text-red-800">Delete</button>
                                    </form>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 dark:text-gray-400">No subtasks yet.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-3">Snooze Task</h3>
                        <form action="{{ route('todos.snooze', $todo) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                            @csrf
                            @method('PATCH')
                            <input type="number" min="1" max="30" name="days" placeholder="Days (e.g. 2)"
                                   class="px-3 py-2 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm" />
                            <input type="date" name="due_date"
                                   class="px-3 py-2 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm" />
                            <button type="submit" class="px-3 py-2 rounded-md bg-purple-600 hover:bg-purple-700 text-white text-sm">Snooze</button>
                        </form>

                        @if($todo->snoozeHistory->count() > 0)
                            <div class="mt-3 space-y-1 text-xs text-gray-500 dark:text-gray-400">
                                @foreach($todo->snoozeHistory->take(5) as $entry)
                                    <p>
                                        {{ $entry->snoozed_at?->format('M d, Y h:i A') }} —
                                        {{ optional($entry->old_due_date)->format('M d, Y') ?? 'No due date' }} →
                                        {{ optional($entry->new_due_date)->format('M d, Y') }}
                                        by {{ $entry->user?->name ?? 'System' }}
                                    </p>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
function todoDetailsEditor(initialData) {
    return {
        editMode: false,
        saving: false,
        saveMessage: '',
        errors: {},
        isCompleted: !!initialData.completed,
        form: {
            title: initialData.title ?? '',
            description: initialData.description ?? '',
            priority: initialData.priority ?? 'medium',
            recurrence_type: initialData.recurrence_type ?? '',
            due_date: initialData.due_date ?? '',
        },
        snapshot: null,
        startEdit() {
            this.snapshot = JSON.parse(JSON.stringify(this.form));
            this.errors = {};
            this.saveMessage = '';
            this.editMode = true;
        },
        cancelEdit() {
            if (this.snapshot) {
                this.form = JSON.parse(JSON.stringify(this.snapshot));
            }
            this.errors = {};
            this.editMode = false;
        },
        async save() {
            this.saving = true;
            this.errors = {};
            this.saveMessage = '';

            try {
                const response = await fetch(initialData.url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({
                        title: this.form.title,
                        description: this.form.description,
                        priority: this.form.priority,
                        recurrence_type: this.form.recurrence_type || null,
                        due_date: this.form.due_date || null,
                        completed: this.isCompleted,
                    }),
                });

                const payload = await response.json();

                if (!response.ok) {
                    this.errors = payload.errors ?? { general: [payload.message ?? 'Unable to update todo.'] };
                    return;
                }

                this.form = {
                    title: payload.todo.title,
                    description: payload.todo.description ?? '',
                    priority: payload.todo.priority ?? 'medium',
                    recurrence_type: payload.todo.recurrence_type ?? '',
                    due_date: payload.todo.due_date ?? '',
                };

                this.snapshot = JSON.parse(JSON.stringify(this.form));
                this.editMode = false;
                this.saveMessage = payload.message ?? 'Updated successfully.';
            } catch (error) {
                this.errors = { general: ['Something went wrong while saving. Please try again.'] };
            } finally {
                this.saving = false;
            }
        },
    };
}
</script>