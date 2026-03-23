<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Models\TodoSnoozeHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Exception;


class TodoController extends Controller
{
    // Display all todos
    public function index(Request $request)
    {
        Log::info('Fetching todos with advanced filters', [
            'smart_views' => $request->query('smart_views', []),
            'statuses' => $request->query('statuses', []),
            'priorities' => $request->query('priorities', []),
            'from' => $request->query('from'),
            'to' => $request->query('to'),
        ]);

        try{
            $toArray = static fn ($value): array => is_array($value)
                ? $value
                : (is_null($value) || $value === '' ? [] : [$value]);

            $normalize = static function (array $values, array $allowed): array {
                $normalized = collect($values)
                    ->filter(fn ($value) => is_string($value) || is_numeric($value))
                    ->map(fn ($value) => strtolower(trim((string) $value)))
                    ->filter(fn ($value) => in_array($value, $allowed, true))
                    ->values()
                    ->all();

                if (in_array('all', $normalized, true)) {
                    return ['all'];
                }

                return array_values(array_unique($normalized));
            };

            $smartViews = $normalize($toArray($request->query('smart_views', [])), ['all', 'today', 'upcoming', 'overdue', 'completed']);
            $statuses = $normalize($toArray($request->query('statuses', [])), ['all', 'pending', 'in_progress', 'completed']);
            $priorities = $normalize($toArray($request->query('priorities', [])), ['all', 'low', 'medium', 'high']);

            // Backward compatibility for existing query links.
            $legacyFilter = strtolower((string) $request->query('filter', ''));
            if ($legacyFilter !== '' && $legacyFilter !== 'all') {
                [$type, $value] = array_pad(explode(':', $legacyFilter, 2), 2, null);

                if ($type === 'smart' && in_array($value, ['today', 'upcoming', 'overdue', 'completed'], true)) {
                    $smartViews = [$value];
                } elseif ($type === 'status' && in_array($value, ['completed', 'ongoing', 'pending', 'in_progress'], true)) {
                    $statuses = [$value === 'ongoing' ? 'in_progress' : $value];
                } elseif ($type === 'priority' && in_array($value, ['low', 'medium', 'high'], true)) {
                    $priorities = [$value];
                }
            }

            $legacySmartView = strtolower((string) $request->query('smart_view', ''));
            if ($smartViews === [] && in_array($legacySmartView, ['today', 'upcoming', 'overdue', 'completed'], true)) {
                $smartViews = [$legacySmartView];
            }

            $legacyStatus = strtolower((string) $request->query('status', ''));
            if ($statuses === [] && in_array($legacyStatus, ['completed', 'ongoing'], true)) {
                $statuses = [$legacyStatus === 'ongoing' ? 'in_progress' : 'completed'];
            }

            $legacyPriority = strtolower((string) $request->query('priority', ''));
            if ($priorities === [] && in_array($legacyPriority, ['low', 'medium', 'high'], true)) {
                $priorities = [$legacyPriority];
            }

            $smartViews = $smartViews === [] ? ['all'] : $smartViews;
            $statuses = $statuses === [] ? ['all'] : $statuses;
            $priorities = $priorities === [] ? ['all'] : $priorities;

            $hasSmartViewFilters = ! in_array('all', $smartViews, true);
            $hasStatusFilters = ! in_array('all', $statuses, true);
            $hasPriorityFilters = ! in_array('all', $priorities, true);

            $from = $request->query('from');
            $to   = $request->query('to');

            $query = Todo::query()
                ->where('user_id', auth()->id())
                ->withCount([
                    'subtasks',
                    'subtasks as completed_subtasks_count' => fn ($q) => $q->where('completed', true),
                ])
                ->latest();

            if ($hasStatusFilters) {
                $query->where(function ($statusQuery) use ($statuses) {
                    foreach ($statuses as $index => $status) {
                        $method = $index === 0 ? 'where' : 'orWhere';

                        if ($status === 'completed') {
                            $statusQuery->{$method}('completed', true);
                            continue;
                        }

                        if ($status === 'in_progress') {
                            $statusQuery->{$method}('completed', false);
                            continue;
                        }

                        if ($status === 'pending') {
                            $statusQuery->{$method}(function ($pendingQuery) {
                                $pendingQuery->where('completed', false)
                                    ->where(function ($dueQuery) {
                                        $dueQuery->whereNull('due_date')
                                            ->orWhereDate('due_date', '>=', today());
                                    });
                            });
                        }
                    }
                });
            }

            if ($hasPriorityFilters) {
                $query->whereIn('priority', $priorities);
            }

            if ($hasSmartViewFilters) {
                $query->where(function ($smartViewQuery) use ($smartViews) {
                    foreach ($smartViews as $index => $smartView) {
                        $method = $index === 0 ? 'where' : 'orWhere';

                        if ($smartView === 'today') {
                            $smartViewQuery->{$method}(function ($todayQuery) {
                                $todayQuery->whereDate('due_date', today());
                            });
                            continue;
                        }

                        if ($smartView === 'upcoming') {
                            $smartViewQuery->{$method}(function ($upcomingQuery) {
                                $upcomingQuery->whereDate('due_date', '>', today())
                                    ->where('completed', false);
                            });
                            continue;
                        }

                        if ($smartView === 'overdue') {
                            $smartViewQuery->{$method}(function ($overdueQuery) {
                                $overdueQuery->whereDate('due_date', '<', today())
                                    ->where('completed', false);
                            });
                            continue;
                        }

                        if ($smartView === 'completed') {
                            $smartViewQuery->{$method}(function ($completedQuery) {
                                $completedQuery->where('completed', true);
                            });
                        }
                    }
                });
            }

            // Apply date range filter
            if ($from) {
                $query->where('created_at', '>=', \Carbon\Carbon::parse($from));
            }
            if ($to) {
                $query->where('created_at', '<=', \Carbon\Carbon::parse($to));
            }

            $todos = $query->paginate(10)->appends(request()->query());

            $hasActiveFilters = $hasSmartViewFilters
                || $hasStatusFilters
                || $hasPriorityFilters
                || ! empty($from)
                || ! empty($to);

            return view('todos.index', compact(
                'todos',
                'from',
                'to',
                'smartViews',
                'statuses',
                'priorities',
                'hasActiveFilters'
            ));
            }
        catch(Exception $e){
            Log::error('Error fetching todos: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while fetching todos.');
        }

    }

    // Show create form
    public function create()
    {
        return view('todos.create');
    }

    // Store new todo
    public function store(Request $request)
    {
        Log::info('Creating todo', ['user_id' => auth()->id()]);

        try {
            $validated = $request->validate([
                'title' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('todos', 'title')->where(fn ($q) => $q->where('user_id', auth()->id())),
                ],
                'description' => ['nullable', 'string', 'max:2000'],
                'due_date' => ['nullable', 'date'],
                'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
                'recurrence_type' => ['nullable', Rule::in(['daily', 'weekly', 'monthly'])],
            ], [
                'title.unique' => 'A task with this title already exists.',
            ]);
    
            Todo::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'due_date' => $validated['due_date'] ?? null,
                'priority' => $validated['priority'] ?? 'medium',
                'recurrence_type' => $validated['recurrence_type'] ?? null,
                'user_id' => auth()->id(),
            ]);
    
            return redirect()->route('todos.index')->with('success', 'Todo created successfully!');
        } catch (\Throwable $th) {
            Log::error('Error creating todo: ' . $th->getMessage());
            return redirect()->back()->withInput()->withErrors(['error' => 'An error occurred while creating the todo.']);
        }
      
    }

    // Show edit form
    public function edit(Todo $todo)
    {
        abort_unless($todo->user_id === auth()->id(), 403);

        try{
            return view('todos.edit', compact('todo'));
        } catch (Exception $e) {
            Log::error('Error loading todo for edit: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while loading the todo for editing.');
        }
        return view('todos.edit', compact('todo'));
    }

    // Update todo
    public function update(Request $request, Todo $todo)
    {
        abort_unless($todo->user_id === auth()->id(), 403);

        try{
            $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('todos', 'title')
                    ->ignore($todo->id)
                    ->where(fn ($q) => $q->where('user_id', auth()->id())),
            ],
                'description' => ['nullable', 'string', 'max:2000'],
                'due_date' => ['nullable', 'date'],
                'completed' => ['nullable'],
                'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
                'recurrence_type' => ['nullable', Rule::in(['daily', 'weekly', 'monthly'])],
            ], [
                'title.unique' => 'A task with this title already exists.',
            ]);

            $todo->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'due_date' => $validated['due_date'] ?? null,
                'completed' => $request->boolean('completed'),
                'priority' => $validated['priority'],
                'recurrence_type' => $validated['recurrence_type'] ?? null,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Todo updated successfully!',
                    'todo' => [
                        'title' => $todo->title,
                        'description' => $todo->description,
                        'priority' => $todo->priority,
                        'recurrence_type' => $todo->recurrence_type,
                        'due_date' => optional($todo->due_date)->format('Y-m-d'),
                    ],
                ]);
            }

            return redirect()->route('todos.index')->with('success', 'Todo updated successfully!');
        }
        catch(Exception $e){
            Log::error('Error updating todo: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while updating the todo.');
        }
        
    }

    // Delete todo
    public function destroy(Todo $todo)
    {
        abort_unless($todo->user_id === auth()->id(), 403);

        Log::info('Deleting todo with ID: ' . $todo->id);
        try{
            $todo->delete(); // soft deletes
            return redirect()->route('todos.index')->with('success', 'Todo deleted successfully!');
        } catch (Exception $e) {
            Log::error('Error deleting todo: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while deleting the todo.');
        }
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'todo_ids' => ['nullable', 'array'],
            'todo_ids.*' => ['integer', 'distinct'],
            'delete_all_visible' => ['nullable', 'boolean'],
            'visible_ids' => ['nullable', 'array'],
            'visible_ids.*' => ['integer', 'distinct'],
        ]);

        $selectedIds = collect($validated['todo_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();

        $deleteAllVisible = (bool) ($validated['delete_all_visible'] ?? false);

        $visibleIds = collect($validated['visible_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();

        $targetIds = $selectedIds;

        if ($deleteAllVisible) {
            $targetIds = $visibleIds;
        }

        if ($targetIds->isEmpty()) {
            return response()->json([
                'message' => 'No todos were selected for deletion.',
                'deleted_count' => 0,
                'deleted_ids' => [],
            ], 422);
        }

        $ownedTodos = Todo::query()
            ->where('user_id', auth()->id())
            ->whereIn('id', $targetIds)
            ->pluck('id');

        if ($ownedTodos->isEmpty()) {
            return response()->json([
                'message' => 'No valid todos found to delete.',
                'deleted_count' => 0,
                'deleted_ids' => [],
            ], 422);
        }

        Todo::query()
            ->where('user_id', auth()->id())
            ->whereIn('id', $ownedTodos)
            ->delete();

        return response()->json([
            'message' => $ownedTodos->count() === 1
                ? '1 todo deleted successfully.'
                : $ownedTodos->count() . ' todos deleted successfully.',
            'deleted_count' => $ownedTodos->count(),
            'deleted_ids' => $ownedTodos->values()->all(),
        ]);
    }

    public function show(Todo $todo)
    {
        abort_unless($todo->user_id === auth()->id(), 403);

        Log::info('Showing details for todo with ID: ' . $todo->id);
        try{
            $todo->load(['subtasks', 'snoozeHistory.user']);

            return view('todos.show', compact('todo'));
        } catch (Exception $e) {
            Log::error('Error showing todo: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while fetching the todo details.');
        }
    }

    // Toggle completion status
    public function toggle(Request $request, Todo $todo)
    {
        abort_unless($todo->user_id === auth()->id(), 403);

        Log::info('Toggling completion status for todo with ID: ' . $todo->id);

        try{
            $user = User::query()->find(auth()->id());
            $previousExp = (int) ($user?->total_exp ?? 0);
            $previousRank = (string) ($user?->current_rank ?? '');

             $todo->update([
                'completed' => !$todo->completed,
            ]);

            $todo->refresh();

            if ($user) {
                $user = User::query()->find($user->id);
            }

            $currentExp = (int) ($user?->total_exp ?? $previousExp);
            $currentRank = (string) ($user?->current_rank ?? $previousRank);
            $expGained = max(0, $currentExp - $previousExp);
            $leveledUp = $expGained > 0 && $previousRank !== $currentRank;

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $todo->completed
                        ? 'Todo marked as completed.'
                        : 'Todo marked as pending.',
                    'todo' => [
                        'id' => $todo->id,
                        'completed' => (bool) $todo->completed,
                    ],
                    'experience' => [
                        'exp_gained' => $expGained,
                        'total_exp' => $currentExp,
                        'leveled_up' => $leveledUp,
                        'previous_rank' => $previousRank,
                        'current_rank' => $currentRank,
                    ],
                ]);
            }

            return redirect()->route('todos.index');
        } catch (Exception $e) {
            Log::error('Error toggling todo status: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'An error occurred while toggling the todo status.',
                ], 500);
            }
            return redirect()->back()->with('error', 'An error occurred while toggling the todo status.');
        }
    }

    public function snooze(Request $request, Todo $todo)
    {
        abort_unless($todo->user_id === auth()->id(), 403);

        $validated = $request->validate([
            'days' => ['nullable', 'integer', 'min:1', 'max:30'],
            'due_date' => ['nullable', 'date'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $oldDueDate = $todo->due_date;

        $newDueDate = isset($validated['due_date'])
            ? \Carbon\Carbon::parse($validated['due_date'])->toDateString()
            : ($oldDueDate?->copy()->addDays((int) ($validated['days'] ?? 1))->toDateString()
                ?? today()->addDays((int) ($validated['days'] ?? 1))->toDateString());

        $todo->update([
            'due_date' => $newDueDate,
        ]);

        TodoSnoozeHistory::create([
            'todo_id' => $todo->id,
            'user_id' => auth()->id(),
            'old_due_date' => $oldDueDate?->toDateString(),
            'new_due_date' => $newDueDate,
            'reason' => $validated['reason'] ?? null,
            'snoozed_at' => now(),
        ]);

        return back()->with('success', 'Task snoozed successfully.');
    }
}