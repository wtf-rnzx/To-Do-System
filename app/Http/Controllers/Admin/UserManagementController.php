<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
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

        $users = $query->paginate(10)->appends(request()->query());

        return view('admin.users.index', compact('users', 'role', 'date'));
    }

    public function toggleRole(User $user)
    {
        $user->usertype = $user->usertype === 'admin' ? 'user' : 'admin';
        $user->save();

        return back()->with('success', "Role updated for {$user->name}.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return back()->with('success', "User {$user->name} has been deleted.");
    }
}
