<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('User Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-medium">All Users</h3>
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            Total: {{ $users->total() }} user(s)
                        </span>
                    </div>

                    <div class="mb-4">
                        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-end gap-3 justify-between">
                            {{-- Role Filter --}}
                            <div class="flex flex-col gap-1">
                                <label for="role" class="text-xs font-medium text-gray-600 dark:text-gray-300">Role</label>
                                <select
                                    id="role"
                                    name="role"
                                    class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"
                                >
                                    <option value="all"   {{ ($role ?? 'all') === 'all'   ? 'selected' : '' }}>All</option>
                                    <option value="admin" {{ ($role ?? 'all') === 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="user"  {{ ($role ?? 'all') === 'user'  ? 'selected' : '' }}>User</option>
                                </select>
                            </div>

                            <div class="flex flex-wrap items-end gap-3">
                                {{-- Date --}}
                                <div class="flex flex-col gap-1">
                                    <label for="date" class="text-xs font-medium text-gray-600 dark:text-gray-300">Date</label>
                                    <input
                                        type="date"
                                        id="date"
                                        name="date"
                                        value="{{ $date ?? '' }}"
                                        class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"
                                    />
                                </div>

                                {{-- Apply & Clear buttons --}}
                                <div class="flex gap-2">
                                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium py-2 px-4 rounded-md">
                                        Apply
                                    </button>
                                    <a href="{{ route('admin.users.index') }}" class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 text-sm font-medium py-2 px-4 rounded-md">
                                        Clear
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        #
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Name
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Email
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Role
                                    </th>
                                    <!-- <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Email Verified
                                    </th> -->
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Registered
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($users as $user)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $user->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <div class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center text-white text-sm font-semibold uppercase">
                                                    {{ substr($user->name, 0, 1) }}
                                                </div>
                                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $user->name }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                            {{ $user->email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($user->usertype === 'admin')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                    Admin
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                    User
                                                </span>
                                            @endif
                                        </td>
                                        <!-- <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if ($user->email_verified_at)
                                                <span class="inline-flex items-center gap-1 text-green-600 dark:text-green-400">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Verified
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 text-yellow-600 dark:text-yellow-400">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Unverified
                                                </span>
                                            @endif
                                        </td> -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $user->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <div class="flex items-center justify-center gap-2">
                                                {{-- Toggle Role --}}
                                                <form method="POST" action="{{ route('admin.users.toggleRole', $user) }}"
                                                      onsubmit="return confirm('Change role for {{ addslashes($user->name) }}?')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                        class="text-xs px-3 py-1 rounded {{ $user->usertype === 'admin' ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200 dark:bg-yellow-900 dark:text-yellow-300' : 'bg-indigo-100 text-indigo-700 hover:bg-indigo-200 dark:bg-indigo-900 dark:text-indigo-300' }}">
                                                        {{ $user->usertype === 'admin' ? 'Demote' : 'Make Admin' }}
                                                    </button>
                                                </form>

                                                {{-- Delete --}}
                                                @if ($user->id !== auth()->id())
                                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                                          onsubmit="return confirm('Delete user {{ addslashes($user->name) }}? This cannot be undone.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="text-xs px-3 py-1 rounded bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-900 dark:text-red-300">
                                                            Delete
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-xs text-gray-400 italic">You</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                            No users found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if ($users->hasPages())
                        <div class="mt-4">
                            {{ $users->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
