@props([
    'todos'    => collect(),
    'title'    => 'Recent Todos',
    'showUser' => false,
])

<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700
            shadow-sm p-4 flex flex-col h-full">
    <span class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-3 shrink-0">
        {{ $title }}
    </span>

    <div class="flex-1 flex flex-col overflow-hidden">
        @forelse ($todos as $todo)
            @php
                $isOverdue = !$todo->completed && $todo->due_date && $todo->due_date->isPast();
                $dotClass  = $todo->completed ? 'bg-emerald-500'
                           : ($isOverdue       ? 'bg-red-500'
                                              : 'bg-amber-400');
            @endphp

            @php
                $priorityClass = match($todo->priority ?? 'medium') {
                    'high' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                    'low' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
                    default => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                };
            @endphp

            <div class="flex items-start gap-3 py-2
                        {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-700/50' : '' }}">
                {{-- Status dot --}}
                <div class="shrink-0 mt-1.5">
                    <div class="h-2 w-2 rounded-full {{ $dotClass }} ring-2 ring-white dark:ring-gray-800"></div>
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate
                               {{ $todo->completed ? 'line-through text-gray-400 dark:text-gray-500' : '' }}">
                        {{ $todo->title }}
                    </p>
                    <div class="flex items-center gap-2 mt-0.5">
                        @if ($showUser && $todo->user)
                            <span class="text-[11px] text-gray-400 dark:text-gray-500">
                                {{ $todo->user->name }}
                            </span>
                            <span class="text-gray-300 dark:text-gray-600">·</span>
                        @endif
                        @if ($todo->due_date)
                            <span class="text-[11px] {{ $isOverdue ? 'text-red-500' : 'text-gray-400 dark:text-gray-500' }}">
                                {{ $todo->due_date->format('M d') }}
                            </span>
                        @else
                            <span class="text-[11px] text-gray-400 dark:text-gray-500">
                                {{ $todo->created_at->diffForHumans() }}
                            </span>
                        @endif

                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $priorityClass }}">
                            {{ strtoupper(substr($todo->priority ?? 'medium', 0, 1)) }}
                        </span>

                        @if(($todo->subtasks_count ?? 0) > 0)
                            <span class="text-[11px] text-gray-400 dark:text-gray-500">
                                {{ $todo->completed_subtasks_count ?? 0 }}/{{ $todo->subtasks_count }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Status tag --}}
                @if ($todo->completed)
                    <span class="shrink-0 self-start text-[10px] font-semibold px-1.5 py-0.5 rounded
                                 bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">
                        Done
                    </span>
                @elseif ($isOverdue)
                    <span class="shrink-0 self-start text-[10px] font-semibold px-1.5 py-0.5 rounded
                                 bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300">
                        Overdue
                    </span>
                @else
                    <span class="shrink-0 self-start text-[10px] font-semibold px-1.5 py-0.5 rounded
                                 bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">
                        Pending
                    </span>
                @endif
            </div>
        @empty
            <div class="flex-1 flex items-center justify-center">
                <p class="text-sm text-gray-400 dark:text-gray-500">No todos yet.</p>
            </div>
        @endforelse
    </div>
</div>
