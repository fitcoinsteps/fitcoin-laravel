<?php

namespace App\Services;

use App\Models\Device;
use App\Models\JwtToken;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AccountService
{
    /**
     * Hard delete the user account and revoke all sessions.
     */
    public function deactivateAccount(User $user): void
    {
        DB::transaction(function () use ($user) {
            // Revoke devices and tokens
            Device::where('user_id', $user->id)->update(['revoked_at' => now()]);
            JwtToken::where('user_id', $user->id)->update(['revoked' => true]);

            // Hard delete user (cascade removes related records)
            $user->forceDelete();
        });
    }
}