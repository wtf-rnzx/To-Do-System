<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->query('role', 'all'); // all|admin|user

        if (!in_array($role, ['all', 'admin', 'user'], true)) {
            $role = 'all';
        }

        $date = $request->query('date');

        $query = User::latest();

        if ($role === 'admin') {
            $query->where('usertype', 'admin');
        } elseif ($role === 'user') {
            $query->where('usertype', 'user');
        }

        // Apply date filter
        if ($date) {
            $query->whereDate('created_at', $date);
        }

        $users = $query->paginate(6)->appends(request()->query());

        return view('admin.users.index', compact('users', 'role', 'date'));
    }

    public function toggleRole(User $user)
    {
        $oldRole = $user->usertype;
        $user->usertype = $user->usertype === 'admin' ? 'user' : 'admin';
        $user->save();

        ActivityLogger::log(
            user:        auth()->user(),
            action:      'role_updated',
            module:      'users',
            description: "Admin changed role of '{$user->name}' from '{$oldRole}' to '{$user->usertype}'.",
            properties:  ['target_user_id' => $user->id, 'old_role' => $oldRole, 'new_role' => $user->usertype],
        );

        return back()->with('success', "Role updated for {$user->name}.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $targetName = $user->name;
        $targetId   = $user->id;
        $user->delete();

        ActivityLogger::log(
            user:        auth()->user(),
            action:      'deleted',
            module:      'users',
            description: "Admin deleted user account '{$targetName}'.",
            properties:  ['deleted_user_id' => $targetId, 'deleted_user_name' => $targetName],
        );

        return back()->with('success', "User {$targetName} has been deleted.");
    }
}
