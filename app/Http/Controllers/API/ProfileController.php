<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ProfileService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    protected ProfileService $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    /**
     * GET /api/profile
     */
    public function show()
    {
        /** @var User $user */
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json($this->profileService->getProfile($user));
    }

    /**
     * POST /api/profile/update
     */
    public function update(Request $request)
    {
        $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name'  => 'sometimes|string|max:255',
            'username'   => 'sometimes|string|max:255|unique:users,username,' . auth('api')->id(),
            'phone'      => 'sometimes|nullable|string|max:20',
        ]);

        /** @var User $user */
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = $request->only(['first_name', 'last_name', 'username', 'phone']);
        $profile = $this->profileService->updateProfile($user, $data);

        return response()->json([
            'message' => 'Profile updated successfully',
            'profile' => $profile,
        ]);
    }

    /**
     * POST /api/profile/avatar
     */
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        /** @var User $user */
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $result = $this->profileService->uploadAvatar($user, $request->file('avatar'));

        return response()->json([
            'message' => 'Avatar uploaded successfully',
            'avatar'  => $result['avatar'],
            'profile' => $result['profile'],
        ]);
    }
}