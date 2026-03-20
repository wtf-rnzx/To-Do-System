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
        $availableRoles = User::query()
            ->select('usertype')
            ->whereNotNull('usertype')
            ->distinct()
            ->pluck('usertype')
            ->map(fn ($role) => strtolower((string) $role))
            ->filter()
            ->sort()
            ->values()
            ->all();

        if ($availableRoles === []) {
            $availableRoles = ['admin', 'user'];
        }

        $selectedRoles = $request->query('roles', []);
        $selectedRoles = is_array($selectedRoles) ? $selectedRoles : [$selectedRoles];
        $selectedRoles = collect($selectedRoles)
            ->map(fn ($role) => strtolower(trim((string) $role)))
            ->filter(fn ($role) => $role === 'all' || in_array($role, $availableRoles, true))
            ->values()
            ->all();

        $legacyRole = strtolower((string) $request->query('role', ''));
        if ($selectedRoles === [] && in_array($legacyRole, $availableRoles, true)) {
            $selectedRoles = [$legacyRole];
        }

        if (in_array('all', $selectedRoles, true) || $selectedRoles === []) {
            $selectedRoles = ['all'];
        }

        $from = (string) $request->query('from', '');
        $to = (string) $request->query('to', '');

        $query = User::latest();

        if (! in_array('all', $selectedRoles, true)) {
            $query->whereIn('usertype', $selectedRoles);
        }

        if ($from !== '') {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to !== '') {
            $query->whereDate('created_at', '<=', $to);
        }

        $users = $query->paginate(6)->appends(request()->query());

        $activeFilterCount = 0;
        if (! in_array('all', $selectedRoles, true)) {
            $activeFilterCount += count($selectedRoles);
        }
        if ($from !== '') {
            $activeFilterCount++;
        }
        if ($to !== '') {
            $activeFilterCount++;
        }

        $hasActiveFilters = $activeFilterCount > 0;

        return view('admin.users.index', compact(
            'users',
            'availableRoles',
            'selectedRoles',
            'from',
            'to',
            'activeFilterCount',
            'hasActiveFilters'
        ));
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
