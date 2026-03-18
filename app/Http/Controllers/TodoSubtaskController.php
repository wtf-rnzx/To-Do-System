<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Models\TodoSubtask;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TodoSubtaskController extends Controller
{
    public function store(Request $request, Todo $todo): RedirectResponse
    {
        abort_unless($todo->user_id === auth()->id(), 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $position = ((int) $todo->subtasks()->max('position')) + 1;

        $todo->subtasks()->create([
            'title' => $validated['title'],
            'position' => $position,
        ]);

        $this->syncParentCompletion($todo->fresh());

        return back()->with('success', 'Subtask added.');
    }

    public function toggle(Todo $todo, TodoSubtask $subtask): RedirectResponse
    {
        abort_unless($todo->user_id === auth()->id(), 403);
        abort_unless($subtask->todo_id === $todo->id, 404);

        $subtask->update([
            'completed' => ! $subtask->completed,
        ]);

        $this->syncParentCompletion($todo->fresh());

        return back()->with('success', 'Subtask updated.');
    }

    public function update(Request $request, Todo $todo, TodoSubtask $subtask): RedirectResponse
    {
        abort_unless($todo->user_id === auth()->id(), 403);
        abort_unless($subtask->todo_id === $todo->id, 404);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $subtask->update([
            'title' => $validated['title'],
        ]);

        return back()->with('success', 'Subtask renamed.');
    }

    public function destroy(Todo $todo, TodoSubtask $subtask): RedirectResponse
    {
        abort_unless($todo->user_id === auth()->id(), 403);
        abort_unless($subtask->todo_id === $todo->id, 404);

        $subtask->delete();

        $this->syncParentCompletion($todo->fresh());

        return back()->with('success', 'Subtask removed.');
    }

    private function syncParentCompletion(Todo $todo): void
    {
        if (! $todo->subtasks()->exists()) {
            return;
        }

        $allCompleted = ! $todo->subtasks()->where('completed', false)->exists();

        if ($todo->completed !== $allCompleted) {
            $todo->update([
                'completed' => $allCompleted,
            ]);
        }
    }
}
