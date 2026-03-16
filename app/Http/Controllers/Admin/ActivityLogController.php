<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::latest('created_at');

        // Search across user_name, description, ip_address
        if ($search = $request->query('search')) {
            $query->search($search);
        }

        // Date range
        if ($from = $request->query('from')) {
            $query->dateFrom($from);
        }
        if ($to = $request->query('to')) {
            $query->dateTo($to);
        }

        // Filter by specific user
        if ($userId = $request->query('user_id')) {
            $query->forUser((int) $userId);
        }

        // Filter by action type
        if ($action = $request->query('action')) {
            if (in_array($action, ActivityLog::availableActions(), true)) {
                $query->forAction($action);
            }
        }

        // Filter by module
        if ($module = $request->query('module')) {
            if (in_array($module, ActivityLog::availableModules(), true)) {
                $query->forModule($module);
            }
        }

        // Sort (default: newest first)
        $sort = $request->query('sort', 'newest');
        match ($sort) {
            'oldest' => $query->oldest('created_at'),
            'user'   => $query->orderBy('user_name'),
            default  => $query->latest('created_at'),
        };

        $logs    = $query->paginate(20)->appends($request->query());
        $users   = User::orderBy('name')->get(['id', 'name']);
        $actions = ActivityLog::availableActions();
        $modules = ActivityLog::availableModules();

        return view('admin.activity-logs.index', compact(
            'logs', 'users', 'actions', 'modules'
        ));
    }
}
