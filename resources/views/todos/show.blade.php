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
                <div class="p-6 text-gray-900 dark:text-gray-100 space-y-4">
                    <div>
                        <div class="text-xs uppercase text-gray-500 dark:text-gray-400">Title</div>
                        <div class="text-lg font-semibold break-words">{{ $todo->title }}</div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs uppercase text-gray-500 dark:text-gray-400">Status</div>
                            <div class="text-sm">
                                {{ $todo->completed ? 'Completed' : 'Ongoing' }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs uppercase text-gray-500 dark:text-gray-400">Description</div>
                            <div class="text-sm whitespace-pre-line">
                                {{ $todo->description ?: 'No description' }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs uppercase text-gray-500 dark:text-gray-400">Due Date</div>
                            <div class="text-sm">
                                {{ $todo->due_date ? $todo->due_date->format('M d, Y') : 'No due date' }}
                            </div>
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
                    
                </div>
            </div>
        </div>
    </div>
</x-app-layout>