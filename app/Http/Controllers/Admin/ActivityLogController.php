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

        $selectedUserId = $request->query('user_id');
        $selectedSort = (string) $request->query('sort', 'newest');
        $selectedActions = $request->query('actions', []);
        $selectedModules = $request->query('modules', []);
        $from = (string) $request->query('from', '');
        $to = (string) $request->query('to', '');

        $selectedActions = is_array($selectedActions) ? $selectedActions : [$selectedActions];
        $selectedModules = is_array($selectedModules) ? $selectedModules : [$selectedModules];

        $selectedActions = collect($selectedActions)
            ->map(fn ($action) => strtolower(trim((string) $action)))
            ->filter(fn ($action) => in_array($action, ActivityLog::availableActions(), true))
            ->unique()
            ->values()
            ->all();

        $selectedModules = collect($selectedModules)
            ->map(fn ($module) => strtolower(trim((string) $module)))
            ->filter(fn ($module) => in_array($module, ActivityLog::availableModules(), true))
            ->unique()
            ->values()
            ->all();

        // Backward compatibility for old single-select params.
        $legacyAction = strtolower((string) $request->query('action', ''));
        if ($selectedActions === [] && in_array($legacyAction, ActivityLog::availableActions(), true)) {
            $selectedActions = [$legacyAction];
        }

        $legacyModule = strtolower((string) $request->query('module', ''));
        if ($selectedModules === [] && in_array($legacyModule, ActivityLog::availableModules(), true)) {
            $selectedModules = [$legacyModule];
        }

        // Date range
        if ($from !== '') {
            $query->dateFrom($from);
        }
        if ($to !== '') {
            $query->dateTo($to);
        }

        // Filter by specific user
        if (! empty($selectedUserId)) {
            $query->forUser((int) $selectedUserId);
        }

        if ($selectedActions !== []) {
            $query->whereIn('action', $selectedActions);
        }

        if ($selectedModules !== []) {
            $query->whereIn('module', $selectedModules);
        }

        // Sort (default: newest first)
        if (! in_array($selectedSort, ['newest', 'oldest'], true)) {
            $selectedSort = 'newest';
        }

        match ($selectedSort) {
            'oldest' => $query->oldest('created_at'),
            default  => $query->latest('created_at'),
        };

        $logs    = $query->paginate(20)->appends($request->query());
        $users   = User::orderBy('name')->get(['id', 'name']);
        $actions = ActivityLog::availableActions();
        $modules = ActivityLog::availableModules();

        $activeFilterCount = 0;
        if (! empty($selectedUserId)) {
            $activeFilterCount++;
        }
        $activeFilterCount += count($selectedActions);
        $activeFilterCount += count($selectedModules);
        if ($selectedSort !== 'newest') {
            $activeFilterCount++;
        }
        if ($from !== '') {
            $activeFilterCount++;
        }
        if ($to !== '') {
            $activeFilterCount++;
        }

        $hasActiveFilters = $activeFilterCount > 0;

        return view('admin.activity-logs.index', compact(
            'logs',
            'users',
            'actions',
            'modules',
            'selectedUserId',
            'selectedActions',
            'selectedModules',
            'selectedSort',
            'from',
            'to',
            'activeFilterCount',
            'hasActiveFilters'
        ));
    }
}
