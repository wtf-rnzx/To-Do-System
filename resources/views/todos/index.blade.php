<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('My Todos') }}
            </h2>
            <a href="{{ route('todos.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Add New Todo
            </a>
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

            <div class="flex items-center justify-end mb-4">
                <form method="GET" action="{{ route('todos.index') }}" class="flex items-center gap-2">
                    <label for="status" class="text-sm text-gray-600 dark:text-gray-300">
                        Filter:
                    </label>

                    <select
                        id="status"
                        name="status"
                        onchange="this.form.submit()"
                        class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"
                    >
                        <option value="all" {{ ($status ?? request('status', 'all')) === 'all' ? 'selected' : '' }}>All</option>
                        <option value="ongoing" {{ ($status ?? request('status', 'all')) === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                        <option value="completed" {{ ($status ?? request('status', 'all')) === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>

                    @if(($status ?? request('status', 'all')) !== 'all')
                        <a
                            href="{{ route('todos.index') }}"
                            class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white underline"
                        >
                            Clear
                        </a>
                    @endif
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if($todos->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full w-full table-fixed divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="w-20 px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th scope="col" class="w-4/12 px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Title
                                        </th>
                                        <th scope="col" class="w-2/12 px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Due Date
                                        </th>
                                        <th scope="col" class="w-2/12 px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Created
                                        </th>
                                        <th scope="col" class="w-2/12 px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($todos as $todo)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
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
                                                <span class="text-sm break-words {{ $todo->completed ? 'line-through text-gray-500' : 'text-gray-900 dark:text-gray-100' }}">
                                                    {{ $todo->title }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                                                {{ $todo->due_date ? $todo->due_date->format('M d, Y') : 'No due date' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                                                {{ $todo->created_at->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium ">
                                                <!-- Action Buttons -->
                                                <a 
                                                    href="{{ route('todos.edit', $todo) }}" 
                                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                                >
                                                    Edit
                                                </a>
                                                <button 
                                                    type="button" 
                                                    x-data 
                                                    @click="$dispatch('open-modal', 'delete-todo-{{ $todo->id }}')" 
                                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 ml-2"
                                                >
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>

                                        @include('todos.partials.delete-modal', ['todo' => $todo])

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
