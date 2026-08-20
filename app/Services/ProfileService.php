<?php

namespace App\Services;

use App\Models\Step;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProfileService
{
    /**
     * Get user profile with fitcoin balance and today's steps.
     */
    public function getProfile(User $user): array
    {
        $today = now()->toDateString();
        $todayStep = Step::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        return [
            'id'               => $user->id,
            'uuid'             => $user->uuid,
            'username'         => $user->username,
            'first_name'       => $user->first_name,
            'last_name'        => $user->last_name,
            'display_name'     => $user->display_name,
            'email'            => $user->email,
            'phone'            => $user->phone,
            'role'             => $user->role,
            'avatar'           => $user->avatar ? Storage::url($user->avatar) : null,
            'fitcoin_balance'  => $user->fitcoin_balance ?? 0,
            'today_steps'      => $todayStep->steps ?? 0,
            'daily_goal'       => $todayStep->goal ?? 10000,
            'created_at'       => $user->created_at,
            'updated_at'       => $user->updated_at,
        ];
    }

    /**
     * Update user profile fields.
     */
    public function updateProfile(User $user, array $data): array
    {
        $user->update($data);
        $user->refresh();
        return $this->getProfile($user);
    }

    /**
     * Upload and set user avatar.
     */
    public function uploadAvatar(User $user, UploadedFile $file): array
    {
        // Delete old avatar if exists
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Store new avatar
        $path = $file->store('avatars', 'public');
        $user->update(['avatar' => $path]);
        $user->refresh();

        return [
            'avatar'  => Storage::url($path),
            'profile' => $this->getProfile($user),
        ];
    }
}