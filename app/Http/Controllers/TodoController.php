<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TodoController extends Controller
{
    // Display all todos
    public function index()
    {
        $todos = Todo::latest()->paginate(6); 
        return view('todos.index', compact('todos'));
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
            'title' => ['required', 'string', 'max:255'],
            'due_date' => ['nullable', 'date'],
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
            'title' => ['required', 'string', 'max:255'],
            'due_date' => ['nullable', 'date'],
            'completed' => ['nullable'],
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
