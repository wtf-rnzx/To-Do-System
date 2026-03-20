<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'profile_image' => $user->profile_image,
                'profile_image_url' => $user->profile_image_url,
            ],
        ]);
    }

    public function updateImage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'image' => ['required', 'file', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
        ]);

        $user = $request->user();
        $disk = config('profile.image_disk', 'public');
        $oldPath = $user->profile_image;

        $newPath = $validated['image']->store("profile-images/{$user->id}", $disk);

        $user->forceFill([
            'profile_image' => $newPath,
        ])->save();

        if (! empty($oldPath) && $oldPath !== $newPath && Storage::disk($disk)->exists($oldPath)) {
            Storage::disk($disk)->delete($oldPath);
        }

        return response()->json([
            'message' => 'Profile image updated successfully.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'profile_image' => $user->profile_image,
                'profile_image_url' => $user->profile_image_url,
            ],
        ]);
    }

    public function removeImage(Request $request): JsonResponse
    {
        $user = $request->user();
        $disk = config('profile.image_disk', 'public');
        $oldPath = $user->profile_image;

        if (! empty($oldPath) && Storage::disk($disk)->exists($oldPath)) {
            Storage::disk($disk)->delete($oldPath);
        }

        $user->forceFill([
            'profile_image' => null,
        ])->save();

        return response()->json([
            'message' => 'Profile image removed successfully.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'profile_image' => $user->profile_image,
                'profile_image_url' => $user->profile_image_url,
            ],
        ]);
    }
}
