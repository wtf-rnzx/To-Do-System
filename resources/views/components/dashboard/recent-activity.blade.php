@props([
    'activities' => collect(),
    'title'      => 'Recent Activity',
])

<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700
            shadow-sm p-4 flex flex-col h-full">
    <span class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-3 shrink-0">
        {{ $title }}
    </span>

    <div class="flex-1 overflow-y-auto scroll-smooth activity-feed">
        @forelse ($activities->take(10) as $log)
            @php
                $dotBg = match ($log->action) {
                    'login'           => 'bg-emerald-500',
                    'logout'          => 'bg-slate-400',
                    'created'         => 'bg-blue-500',
                    'updated'         => 'bg-amber-500',
                    'deleted'         => 'bg-red-500',
                    'toggled'         => 'bg-violet-500',
                    'role_updated'    => 'bg-orange-500',
                    'profile_updated' => 'bg-teal-500',
                    'account_deleted' => 'bg-rose-600',
                    default           => 'bg-gray-400',
                };
            @endphp

            <div class="flex items-start gap-3 py-2
                        {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-700/50' : '' }}">
                {{-- Dot --}}
                <div class="shrink-0 mt-1.5">
                    <div class="h-2 w-2 rounded-full {{ $dotBg }} ring-2 ring-white dark:ring-gray-800"></div>
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-snug truncate">
                        <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $log->user_name }}</span>
                        &nbsp;{{ Str::limit($log->description, 48) }}
                    </p>
                    <span class="text-[11px] text-gray-400 dark:text-gray-500">
                        {{ $log->created_at->diffForHumans() }}
                    </span>
                </div>

                {{-- Badge --}}
                <span class="shrink-0 self-start text-[10px] font-semibold px-1.5 py-0.5 rounded
                             {{ $log->actionBadgeClass() }}">
                    {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                </span>
            </div>
        @empty
            <div class="flex-1 flex items-center justify-center">
                <p class="text-sm text-gray-400 dark:text-gray-500">No recent activity.</p>
            </div>
        @endforelse
    </div>
</div>

@pushOnce('scripts', 'activity-feed-scrollbar')
<style>
.activity-feed::-webkit-scrollbar          { width: 4px; }
.activity-feed::-webkit-scrollbar-track    { background: transparent; }
.activity-feed::-webkit-scrollbar-thumb    { background-color: rgba(156,163,175,0.45); border-radius: 9999px; }
.dark .activity-feed::-webkit-scrollbar-thumb { background-color: rgba(75,85,99,0.55); }
</style>
@endPushOnce
