<!-- Delete Modal -->
<x-modal name="delete-todo-{{ $todo->id }}" maxWidth="sm">
    <div class="p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
            Delete Todo
        </h2>

        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
            Are you sure you want to delete "<strong>{{ $todo->title }}</strong>"? This action cannot be undone.
        </p>

        <div class="flex justify-end space-x-3">
            <button
                type="button"
                x-on:click="$dispatch('close-modal', 'delete-todo-{{ $todo->id }}')"
                class="px-4 py-2 pr-5 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600"
            >
                Cancel
            </button>
            <form action="{{ route('todos.destroy', $todo) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button
                    type="submit"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg"
                >
                    Delete
                </button>
            </form>
        </div>
    </div>
</x-modal>