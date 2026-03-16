<?php

namespace App\Http\Controllers;

use App\Models\Todo;
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

            if (!in_array($status, ['all', 'completed', 'ongoing'], true)) {
                $status = 'all';
            }

            $from = $request->query('from');
            $to   = $request->query('to');

            $query = Todo::query()
                ->where('user_id', auth()->id())
                ->latest();

            if ($status === 'completed') {
                $query->where('completed', true);
            } elseif ($status === 'ongoing') {
                $query->where('completed', false);
            }

            // Apply date range filter
            if ($from) {
                $query->where('created_at', '>=', \Carbon\Carbon::parse($from));
            }
            if ($to) {
                $query->where('created_at', '<=', \Carbon\Carbon::parse($to));
            }

            $todos = $query->paginate(6)->appends(request()->query());

            return view('todos.index', compact('todos', 'status', 'from', 'to'));
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
        Log::info('Creating new todo with title: ' . $request);

        try {
            $validated = $request->validate([
                'title' => ['required', 'string', 'max:255', 'unique:todos,title'],
                'description' => ['nullable', 'string', 'max:2000'],
                'due_date' => ['nullable', 'date'],
            ], [
                'title.unique' => 'A task with this title already exists.',
            ]);
    
            Todo::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'due_date' => $validated['due_date'] ?? null,
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
                Rule::unique('todos', 'title')->ignore($todo->id),
            ],
                'description' => ['nullable', 'string', 'max:2000'],
                'due_date' => ['nullable', 'date'],
                'completed' => ['nullable'],
            ], [
                'title.unique' => 'A task with this title already exists.',
            ]);

            $todo->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'due_date' => $validated['due_date'] ?? null,
                'completed' => $request->boolean('completed'),
            ]);

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
}