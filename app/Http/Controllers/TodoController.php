<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Models\TodoSnoozeHistory;
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
        Log::info('Fetching todos with status filter: ' . $request->query('status', 'all'));

        try{
            $status = $request->query('status', 'all'); // all|completed|ongoing
            $smartView = $request->query('smart_view', 'all'); // all|today|upcoming|overdue
            $priority = $request->query('priority', 'all'); // all|low|medium|high

            if (!in_array($status, ['all', 'completed', 'ongoing'], true)) {
                $status = 'all';
            }

            if (! in_array($smartView, ['all', 'today', 'upcoming', 'overdue'], true)) {
                $smartView = 'all';
            }

            if (! in_array($priority, ['all', 'low', 'medium', 'high'], true)) {
                $priority = 'all';
            }

            $from = $request->query('from');
            $to   = $request->query('to');

            $query = Todo::query()
                ->where('user_id', auth()->id())
                ->withCount([
                    'subtasks',
                    'subtasks as completed_subtasks_count' => fn ($q) => $q->where('completed', true),
                ])
                ->latest();

            if ($status === 'completed') {
                $query->where('completed', true);
            } elseif ($status === 'ongoing') {
                $query->where('completed', false);
            }

            if ($priority !== 'all') {
                $query->where('priority', $priority);
            }

            if ($smartView === 'today') {
                $query->whereDate('due_date', today());
            } elseif ($smartView === 'upcoming') {
                $query->whereDate('due_date', '>', today())
                    ->where('completed', false);
            } elseif ($smartView === 'overdue') {
                $query->whereDate('due_date', '<', today())
                    ->where('completed', false);
            }

            // Apply date range filter
            if ($from) {
                $query->where('created_at', '>=', \Carbon\Carbon::parse($from));
            }
            if ($to) {
                $query->where('created_at', '<=', \Carbon\Carbon::parse($to));
            }

            $todos = $query->paginate(10)->appends(request()->query());

            return view('todos.index', compact('todos', 'status', 'from', 'to', 'smartView', 'priority'));
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
    public function toggle(Todo $todo)
    {
        abort_unless($todo->user_id === auth()->id(), 403);

        Log::info('Toggling completion status for todo with ID: ' . $todo->id);

        try{
             $todo->update([
                'completed' => !$todo->completed,
            ]);

            return redirect()->route('todos.index');
        } catch (Exception $e) {
            Log::error('Error toggling todo status: ' . $e->getMessage());
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