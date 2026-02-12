<!-- Edit Modal -->
<x-modal name="edit-todo-{{ $todo->id }}" maxWidth="md" focusable>
    <div class="p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
            Edit Todo
        </h2>

        <form action="{{ route('todos.update', $todo) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="title-{{ $todo->id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Todo Title
                </label>
                <input
                    type="text"
                    name="title"
                    id="title-{{ $todo->id }}"
                    value="{{ old('title', $todo->title) }}"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                    required
                >
            </div>

            <label class="flex items-center space-x-2 text-sm text-gray-700 dark:text-gray-300 pt-2 mt-4">
                <input
                    type="checkbox"
                    name="completed"
                    {{ $todo->completed ? 'checked' : '' }}
                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                >
                <span>Mark as completed</span>
            </label>

            <div class="flex justify-end space-x-3 mt-6">
                <button
                    type="button"
                    x-on:click="$dispatch('close-modal', 'edit-todo-{{ $todo->id }}')"
                    class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg"
                >
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</x-modal>