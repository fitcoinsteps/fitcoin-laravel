<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\JsonResponse;
use App\Models\User;

class DeviceController extends Controller
{
    /**
     * List all devices for the authenticated user.
     */
    public function index(): JsonResponse
    {
        /** @var User|null $user */
        $user = auth('api')->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $devices = Device::where('user_id', $user->id)
            ->orderBy('last_used_at', 'desc')
            ->get();

        return response()->json($devices);
    }

    /**
     * Revoke a specific device.
     */
    public function revoke(int $deviceId): JsonResponse
    {
        /** @var User|null $user */
        $user = auth('api')->user();

        $device = Device::where('user_id', $user->id)
            ->where('id', $deviceId)
            ->first();

        if (!$device) {
            return response()->json(['error' => 'Device not found'], 404);
        }

        $device->update(['revoked_at' => now()]);

        return response()->json(['message' => 'Device revoked successfully']);
    }

    /**
     * Trust a specific device.
     */
    public function trust(int $deviceId): JsonResponse
    {
        /** @var User|null $user */
        $user = auth('api')->user();

        $device = Device::where('user_id', $user->id)
            ->where('id', $deviceId)
            ->first();

        if (!$device) {
            return response()->json(['error' => 'Device not found'], 404);
        }

        $device->update(['is_trusted' => true]);

        return response()->json(['message' => 'Device trusted successfully']);
    }
}