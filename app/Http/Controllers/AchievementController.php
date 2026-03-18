<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\UserAchievement;
use App\Services\AchievementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AchievementController extends Controller
{
    public function __construct(
        private readonly AchievementService $achievementService,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $achievements = $this->achievementService->getUserAchievementsForPage($user);

        return view('achievements.index', [
            'achievements' => $achievements,
            'unlockedCount' => collect($achievements)->where('unlocked', true)->count(),
            'totalCount' => count($achievements),
        ]);
    }

    public function toggleVisibility(Request $request, Achievement $achievement): RedirectResponse
    {
        $user = $request->user();

        $userAchievement = UserAchievement::query()
            ->where('user_id', $user->id)
            ->where('achievement_id', $achievement->id)
            ->first();

        if (! $userAchievement || is_null($userAchievement->unlocked_at)) {
            return back()->with('error', 'Only unlocked achievements can be shown on profile.');
        }

        $userAchievement->update([
            'is_visible' => ! $userAchievement->is_visible,
        ]);

        return back()->with('success', 'Badge visibility updated.');
    }
}
