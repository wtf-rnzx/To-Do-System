<div>
    {{-- Main Container with gradient background --}}
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header Section with improved styling --}}
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-400 dark:to-purple-400">
                            My Todos
                        </h1>
                        <p class="mt-2 text-base text-gray-600 dark:text-gray-400 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            Manage your tasks efficiently
                        </p>
                    </div>
                    <button wire:click="openModal" 
                            class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add New Todo
                    </button>
                </div>
            </div>

            {{-- Enhanced Success Message with animation --}}
            @if (session()->has('message'))
                <div x-data="{ show: true }" 
                     x-show="show" 
                     x-init="setTimeout(() => show = false, 4000)"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform -translate-y-2 scale-95"
                     x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0 transform -translate-y-2"
                     class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border-l-4 border-green-500 p-4 rounded-r-xl shadow-lg backdrop-blur-sm">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-green-500 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <p class="ml-3 text-sm font-semibold text-green-800 dark:text-green-300">
                                {{ session('message') }}
                            </p>
                        </div>
                        <button @click="show = false" class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-200 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            {{-- Modern Filter Tabs with pill design --}}
            <div class="mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-2 inline-flex space-x-2">
                    <button wire:click="setFilter('all')" 
                            class="@if($filterStatus === 'all') bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-md @else text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 @endif px-6 py-2.5 rounded-lg font-semibold text-sm transition-all duration-200 transform hover:scale-105 focus:outline-none">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                            All Tasks
                        </span>
                    </button>
                    <button wire:click="setFilter('pending')" 
                            class="@if($filterStatus === 'pending') bg-gradient-to-r from-amber-500 to-orange-500 text-white shadow-md @else text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 @endif px-6 py-2.5 rounded-lg font-semibold text-sm transition-all duration-200 transform hover:scale-105 focus:outline-none">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Pending
                        </span>
                    </button>
                    <button wire:click="setFilter('completed')" 
                            class="@if($filterStatus === 'completed') bg-gradient-to-r from-green-600 to-emerald-600 text-white shadow-md @else text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 @endif px-6 py-2.5 rounded-lg font-semibold text-sm transition-all duration-200 transform hover:scale-105 focus:outline-none">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Completed
                        </span>
                    </button>
                </div>
            </div>

            {{-- Enhanced Todos Table --}}
            <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700">
                @if($todos->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Status
                                        </span>
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                            Task
                                        </span>
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Created
                                        </span>
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                        <span class="flex items-center justify-end">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                            </svg>
                                            Actions
                                        </span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($todos as $todo)
                                    <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 dark:hover:from-gray-700 dark:hover:to-gray-700 transition-all duration-200 group">
                                        {{-- Enhanced Status Badge --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <button wire:click="toggleComplete({{ $todo->id }})" 
                                                    class="focus:outline-none transform transition-all duration-300 hover:scale-125">
                                                @if($todo->completed)
                                                    <div class="relative">
                                                        <svg class="w-8 h-8 text-green-500 drop-shadow-lg" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                        </svg>
                                                        <div class="absolute inset-0 bg-green-400 rounded-full blur-sm opacity-50 animate-pulse"></div>
                                                    </div>
                                                @else
                                                    <div class="relative group">
                                                        <svg class="w-8 h-8 text-gray-300 hover:text-blue-500 dark:text-gray-600 dark:hover:text-blue-400 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </button>
                                        </td>

                                        {{-- Enhanced Todo Title with badge --}}
                                        <td class="px-6 py-4">
                                            <div class="flex items-center space-x-3">
                                                <div class="flex-1">
                                                    <div class="text-sm font-semibold @if($todo->completed) line-through text-gray-400 dark:text-gray-500 @else text-gray-900 dark:text-white @endif">
                                                        {{ $todo->title }}
                                                    </div>
                                                    @if($todo->completed)
                                                        <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                            </svg>
                                                            Completed
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-300">
                                                            <svg class="w-3 h-3 mr-1 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            </svg>
                                                            In Progress
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Enhanced Created Date --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                {{ $todo->created_at->diffForHumans() }}
                                            </div>
                                        </td>

                                        {{-- Enhanced Action Buttons --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="flex justify-end space-x-3">
                                                {{-- Edit Button with tooltip --}}
                                                <button wire:click="editTodo({{ $todo->id }})" 
                                                        class="group relative inline-flex items-center justify-center p-2 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 hover:bg-blue-200 dark:hover:bg-blue-900/50 rounded-lg transition-all duration-200 transform hover:scale-110 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                                        title="Edit">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                    {{-- Tooltip --}}
                                                    <span class="absolute bottom-full mb-2 hidden group-hover:block w-max px-2 py-1 text-xs font-medium text-white bg-gray-900 rounded-md shadow-sm">
                                                        Edit Todo
                                                    </span>
                                                </button>

                                                {{-- Delete Button with confirmation --}}
                                                <button x-data="{ confirmDelete: false }" 
                                                        @click="confirmDelete ? $wire.deleteTodo({{ $todo->id }}) : confirmDelete = true"
                                                        @click.away="confirmDelete = false"
                                                        class="group relative inline-flex items-center justify-center p-2 transition-all duration-200 transform hover:scale-110 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                                        :class="confirmDelete ? 'bg-red-600 text-white animate-pulse' : 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-200 dark:hover:bg-red-900/50'"
                                                        :title="confirmDelete ? 'Click to confirm deletion' : 'Delete'">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                    {{-- Tooltip --}}
                                                    <span class="absolute bottom-full mb-2 hidden group-hover:block w-max px-2 py-1 text-xs font-medium text-white bg-gray-900 rounded-md shadow-sm"
                                                          x-text="confirmDelete ? 'Click again to confirm' : 'Delete Todo'">
                                                    </span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Enhanced Pagination --}}
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $todos->links() }}
                </div>
            @else
                {{-- Enhanced Empty State --}}
                <div class="text-center py-16 px-4">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-br from-blue-100 to-purple-100 dark:from-blue-900/30 dark:to-purple-900/30 mb-6">
                        <svg class="w-10 h-10 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">No todos yet</h3>
                    <p class="text-base text-gray-600 dark:text-gray-400 mb-8 max-w-sm mx-auto">
                        Start organizing your life by creating your first todo task.
                    </p>
                    <button wire:click="openModal" 
                            class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-blue-300">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Create Your First Todo
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- Enhanced Modal for Create/Edit with Alpine.js --}}
    <div x-data="{ show: @entangle('showModal') }" 
         x-show="show" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true"
         @keydown.escape.window="$wire.closeModal()">
        
        {{-- Background Overlay with blur effect --}}
        <div x-show="show" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity"
             @click="$wire.closeModal()"></div>

        {{-- Modal Panel with enhanced styling --}}
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="show" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-8 scale-95"
                 class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-2xl transition-all w-full max-w-lg border border-gray-200 dark:border-gray-700">
                
                {{-- Modal Header with gradient --}}
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-2xl font-bold text-white flex items-center" id="modal-title">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            {{ $editingId ? 'Edit Todo' : 'Create New Todo' }}
                        </h3>
                        <button wire:click="closeModal" 
                                class="text-white/80 hover:text-white transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-white/50 rounded-lg p-1">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Form Section --}}
                <div class="px-6 py-6">
                    <div class="space-y-4">
                        <div>
                            <label for="title" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Todo Title
                                </span>
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       wire:model.live="title" 
                                       id="title"
                                       class="block w-full px-4 py-3 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-900 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 dark:focus:border-blue-400 transition-all duration-200 placeholder-gray-400"
                                       placeholder="Enter your todo task...">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </div>
                            </div>
                            
                            @error('title')
                                <div class="mt-2 flex items-center text-sm text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 px-3 py-2 rounded-lg">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Modal Footer with enhanced buttons --}}
                <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 flex flex-col-reverse sm:flex-row sm:justify-between gap-3 border-t border-gray-200 dark:border-gray-700">
                    <button wire:click="closeModal" 
                            type="button"
                            class="inline-flex items-center justify-center px-6 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl text-gray-700 dark:text-gray-300 font-semibold bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Cancel
                    </button>
                    
                    @if($editingId)
                        <button wire:click="updateTodo" 
                                type="button"
                                class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Update Todo
                        </button>
                    @else
                        <button wire:click="createTodo" 
                                type="button"
                                class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-green-300 dark:focus:ring-green-800">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Create Todo
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Enhanced custom styles for animations and x-cloak --}}
<style>
    [x-cloak] { 
        display: none !important; 
    }
    
    /* Custom scrollbar for better aesthetics */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, #3b82f6, #8b5cf6);
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(180deg, #2563eb, #7c3aed);
    }
    
    /* Dark mode scrollbar */
    .dark ::-webkit-scrollbar-track {
        background: #1f2937;
    }
    
    /* Smooth transitions for all elements */
    * {
        transition-property: background-color, border-color, color, fill, stroke;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }
</style>
