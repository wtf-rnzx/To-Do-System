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
        $todos = Todo::latest()->get();
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
        $request->validate([
            'title' => 'required|max:255',
        ]);

        Todo::create([
            'title' => $request->title,
            'completed' => false,
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
        $request->validate([
            'title' => 'required|max:255',
        ]);

        $todo->update([
            'title' => $request->title,
            'completed' => $request->has('completed'),
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
