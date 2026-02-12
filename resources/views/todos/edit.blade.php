<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Todo') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('todos.update', $todo) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Title Input -->
                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium mb-2">
                                Todo Title
                            </label>
                            <input 
                                type="text" 
                                name="title" 
                                id="title" 
                                value="{{ old('title', $todo->title) }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                required
                            >
                            @error('title')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Completed Checkbox -->
                        <div class="mb-4 pt-2 mt-4">
                            <label class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    name="completed" 
                                    {{ $todo->completed ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                >
                                <span class="ml-2 text-sm">Mark as completed</span>
                            </label>
                        </div>

                        <!-- Buttons -->
                        <div class="flex space-x-3">
                            <button 
                                type="submit" 
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                            >
                                Update Todo
                            </button>
                            <a 
                                href="{{ route('todos.index') }}" 
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                            >
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
