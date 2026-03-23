<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\UserAchievement;
use App\Services\ActivityLogger;
use App\Services\ExperienceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        private readonly ExperienceService $experienceService,
    ) {}

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        $visibleAchievements = UserAchievement::query()
            ->where('user_id', $user->id)
            ->whereNotNull('unlocked_at')
            ->where('is_visible', true)
            ->with('achievement')
            ->orderByDesc('unlocked_at')
            ->get();

        $experience = $this->experienceService->buildProgressForUser($user);
        $user->forceFill([
            'current_rank' => $experience['current_rank']['key'],
            'rank_progress_pct' => $experience['progress_pct'],
        ]);

        if ($user->isDirty(['current_rank', 'rank_progress_pct'])) {
            $user->save();
        }

        return view('profile.edit', [
            'user' => $user,
            'visibleAchievements' => $visibleAchievements,
            'experience' => $experience,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        ActivityLogger::log(
            user:        $request->user(),
            action:      'profile_updated',
            module:      'profile',
            description: "User '{$request->user()->name}' updated their profile.",
            request:     $request,
        );

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        ActivityLogger::log(
            user:        $user,
            action:      'account_deleted',
            module:      'profile',
            description: "User '{$user->name}' deleted their own account.",
            request:     $request,
        );

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
