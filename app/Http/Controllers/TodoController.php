<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;

class TodoController extends Controller
{
    // Display all todos
    public function index(Request $request)
    {
        $status = $request->query('status', 'all'); // all|completed|ongoing

        if (!in_array($status, ['all', 'completed', 'ongoing'], true)) {
            $status = 'all';
        }

        $query = Todo::query()->latest();

        if ($status === 'completed') {
            $query->where('completed', true);
        } elseif ($status === 'ongoing') {
            $query->where('completed', false);

        }

        $todos = $query->paginate(6)->appends(request()->query());

        return view('todos.index', compact('todos', 'status'));
    }

    // Show create form
    public function create()
    {
        return view('todos.create');
    }

    // Store new todo
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255', 'unique:todos,title'],
            'due_date' => ['nullable', 'date'],
        ], [
            'title.unique' => 'A task with this title already exists.',
        ]);

        Todo::create([
            'title' => $validated['title'],
            'due_date' => $validated['due_date'] ?? null,
        ]);

        return redirect()->route('todos.index')->with('success', 'Todo created successfully!');
    }

    // Show edit form
    public function edit(Todo $todo)
    {
        return view('todos.edit', compact('todo'));
    }

    // Update todo
    public function update(Request $request, Todo $todo)
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('todos', 'title')->ignore($todo->id),
            ],
            'due_date' => ['nullable', 'date'],
            'completed' => ['nullable'],
        ], [
            'title.unique' => 'A task with this title already exists.',
        ]);

        $todo->update([
            'title' => $validated['title'],
            'due_date' => $validated['due_date'] ?? null,
            'completed' => $request->boolean('completed'),
        ]);

        return redirect()->route('todos.index')->with('success', 'Todo updated successfully!');
    }

    // Delete todo
    public function destroy(Todo $todo)
    {
        $todo->delete();
        return redirect()->route('todos.index')->with('success', 'Todo deleted successfully!');
    }

    // Toggle completion status
    public function toggle(Todo $todo)
    {
        $todo->update([
            'completed' => !$todo->completed,
        ]);

        return redirect()->route('todos.index');
    }
}
