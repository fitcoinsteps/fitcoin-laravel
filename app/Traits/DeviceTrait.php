<?php

namespace App\Traits;

use App\Models\Device;

trait DeviceTrait
{
    /**
     * Check if a device is available for a user.
     * Returns true if no other user is actively using the same device.
     */
    protected function isDeviceAvailable(string $ip, string $userAgent, int $userId): bool
    {
        $globalFingerprint = hash('sha256', $ip . $userAgent);
        $activeDevice = Device::where('global_fingerprint', $globalFingerprint)
                              ->whereNull('revoked_at')
                              ->first();

        // Allow if no active device exists OR the active device belongs to this user
        return !$activeDevice || $activeDevice->user_id === $userId;
    }

    /**
     * Check or create device for the user.
     * Returns the Device model.
     */
    protected function checkDevice($user, string $ip, string $userAgent, string $deviceName, bool $remember): Device
    {
        $fingerprint = hash('sha256', $ip . $userAgent . $user->id);
        $globalFingerprint = hash('sha256', $ip . $userAgent);

        // Find existing device for this user (including revoked)
        $device = Device::where('user_id', $user->id)
                        ->where('fingerprint', $fingerprint)
                        ->first();

        if ($device) {
            // Reactivate and update
            $device->update([
                'revoked_at'         => null,
                'last_used_at'       => now(),
                'is_trusted'         => $remember,
                'device_name'        => $deviceName,
                'global_fingerprint' => $globalFingerprint,
            ]);
        } else {
            // Create new device
            $device = Device::create([
                'user_id'            => $user->id,
                'device_name'        => $deviceName,
                'fingerprint'        => $fingerprint,
                'global_fingerprint' => $globalFingerprint,
                'last_used_at'       => now(),
                'is_trusted'         => $remember,
            ]);
        }

        return $device;
    }
}