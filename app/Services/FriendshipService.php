<?php

namespace App\Services;

use App\Models\Friendship;
use App\Models\Step;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FriendshipService
{
    /**
     * Send a friend request.
     */
    public function sendRequest(User $sender, int $receiverId): array
    {
        if ($sender->id == $receiverId) {
            throw new \Exception('You cannot send a friend request to yourself.', 422);
        }

        $receiver = User::where('id', $receiverId)
                        ->where('role', User::ROLE_USER)
                        ->first();

        if (!$receiver) {
            throw new \Exception('User not found.', 404);
        }

        $existing = Friendship::where(function ($query) use ($sender, $receiverId) {
                $query->where('sender_id', $sender->id)
                      ->where('receiver_id', $receiverId);
            })
            ->orWhere(function ($query) use ($sender, $receiverId) {
                $query->where('sender_id', $receiverId)
                      ->where('receiver_id', $sender->id);
            })
            ->first();

        if ($existing) {
            if ($existing->status === 'accepted') {
                throw new \Exception('You are already friends.', 422);
            }
            if ($existing->status === 'pending') {
                throw new \Exception('Friend request already sent.', 422);
            }
            if ($existing->status === 'blocked') {
                throw new \Exception('Unable to send request.', 422);
            }
        }

        $friendship = Friendship::create([
            'sender_id'   => $sender->id,
            'receiver_id' => $receiverId,
            'status'      => 'pending',
        ]);

        return [
            'friendship' => $friendship,
            'message'    => 'Friend request sent.',
        ];
    }

    /**
     * Accept a friend request.
     */
    public function acceptRequest(User $user, int $friendshipId): array
    {
        $friendship = Friendship::where('id', $friendshipId)
            ->where('receiver_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if (!$friendship) {
            throw new \Exception('Friend request not found or already processed.', 404);
        }

        $friendship->update(['status' => 'accepted']);

        return [
            'friendship' => $friendship,
            'message'    => 'Friend request accepted.',
        ];
    }

    /**
     * Reject a friend request.
     */
    public function rejectRequest(User $user, int $friendshipId): array
    {
        $friendship = Friendship::where('id', $friendshipId)
            ->where('receiver_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if (!$friendship) {
            throw new \Exception('Friend request not found or already processed.', 404);
        }

        $friendship->update(['status' => 'rejected']);

        return [
            'message' => 'Friend request rejected.',
        ];
    }

    /**
     * List accepted friends with their today's step data and online status.
     */
    public function listFriends(User $user): array
    {
        $friendships = Friendship::where(function ($query) use ($user) {
                $query->where('sender_id', $user->id)
                      ->orWhere('receiver_id', $user->id);
            })
            ->where('status', 'accepted')
            ->get();

        $friendIds = [];
        foreach ($friendships as $friendship) {
            $friendId = $friendship->sender_id == $user->id
                ? $friendship->receiver_id
                : $friendship->sender_id;
            $friendIds[] = $friendId;
        }

        if (empty($friendIds)) {
            return [
                'total_users' => User::where('role', User::ROLE_USER)->count(),
                'friends' => [],
            ];
        }

        $today = Carbon::today()->toDateString();

        $friends = User::whereIn('id', $friendIds)
            ->where('role', User::ROLE_USER)
            ->get()
            ->map(function ($friend) use ($today) {
                $step = Step::where('user_id', $friend->id)
                    ->where('date', $today)
                    ->first();

                $todaySteps = $step ? $step->steps : 0;
                $goal = $step ? $step->goal : 10000;
                $progress = $goal > 0 ? round(($todaySteps / $goal) * 100, 2) : 0;
                $isCompleted = $progress >= 100;
                $isOnline = $friend->last_activity_at
                    ? $friend->last_activity_at->diffInMinutes(now()) <= 5
                    : false;

                return [
                    'id'               => $friend->id,
                    'name'             => trim($friend->first_name . ' ' . $friend->last_name),
                    'username'         => $friend->username,
                    'avatar_url'       => $friend->avatar
                        ? Storage::url($friend->avatar)
                        : null,
                    'today_steps'      => $todaySteps,
                    'goal'             => $goal,
                    'progress_percent' => $progress,
                    'is_completed'     => $isCompleted,
                    'is_online'        => $isOnline,
                    'role'             => $friend->role,
                ];
            });

        return [
            'total_users' => User::where('role', User::ROLE_USER)->count(),
            'friends'     => $friends,
        ];
    }

    /**
     * List pending incoming and outgoing friend requests.
     */
    public function pendingRequests(User $user): array
    {
        $incoming = Friendship::where('receiver_id', $user->id)
            ->where('status', 'pending')
            ->with('sender:id,username,first_name,last_name,avatar')
            ->get()
            ->map(function ($friendship) {
                return [
                    'friendship_id' => $friendship->id,
                    'sender' => [
                        'id'         => $friendship->sender->id,
                        'name'       => trim($friendship->sender->first_name . ' ' . $friendship->sender->last_name),
                        'username'   => $friendship->sender->username,
                        'avatar_url' => $friendship->sender->avatar
                            ? Storage::url($friendship->sender->avatar)
                            : null,
                    ],
                    'created_at' => $friendship->created_at,
                ];
            });

        $outgoing = Friendship::where('sender_id', $user->id)
            ->where('status', 'pending')
            ->with('receiver:id,username,first_name,last_name,avatar')
            ->get()
            ->map(function ($friendship) {
                return [
                    'friendship_id' => $friendship->id,
                    'receiver' => [
                        'id'         => $friendship->receiver->id,
                        'name'       => trim($friendship->receiver->first_name . ' ' . $friendship->receiver->last_name),
                        'username'   => $friendship->receiver->username,
                        'avatar_url' => $friendship->receiver->avatar
                            ? Storage::url($friendship->receiver->avatar)
                            : null,
                    ],
                    'created_at' => $friendship->created_at,
                ];
            });

        return [
            'incoming' => $incoming,
            'outgoing' => $outgoing,
        ];
    }

    /**
     * Remove / unfriend.
     */
    public function removeFriend(User $user, int $friendId): array
    {
        $friendship = Friendship::where(function ($query) use ($user, $friendId) {
                $query->where('sender_id', $user->id)
                      ->where('receiver_id', $friendId);
            })
            ->orWhere(function ($query) use ($user, $friendId) {
                $query->where('sender_id', $friendId)
                      ->where('receiver_id', $user->id);
            })
            ->where('status', 'accepted')
            ->first();

        if (!$friendship) {
            throw new \Exception('Friendship not found.', 404);
        }

        $friendship->delete();

        return ['message' => 'Friend removed.'];
    }

    /**
     * Search for users who are not the current user and not already friends/pending.
     */
    public function searchUsers(User $user, ?string $query = null): array
    {
        $queryBuilder = User::where('role', User::ROLE_USER)
            ->where('id', '!=', $user->id);

        // Exclude users who are already friends or have pending requests
        $friendIds = Friendship::where(function ($q) use ($user) {
                $q->where('sender_id', $user->id)
                  ->orWhere('receiver_id', $user->id);
            })
            ->pluck('sender_id', 'receiver_id')
            ->toArray();

        $excludeIds = [];
        foreach ($friendIds as $senderId => $receiverId) {
            $excludeIds[] = $senderId;
            $excludeIds[] = $receiverId;
        }
        $excludeIds = array_unique($excludeIds);
        $excludeIds[] = $user->id;

        $queryBuilder->whereNotIn('id', $excludeIds);

        if ($query && trim($query) !== '') {
            $queryBuilder->where(function ($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%")
                  ->orWhere('username', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            });
        }

        $users = $queryBuilder->limit(20)->get()->map(function ($u) {
            return [
                'id'         => $u->id,
                'name'       => trim($u->first_name . ' ' . $u->last_name),
                'username'   => $u->username,
                'avatar_url' => $u->avatar ? Storage::url($u->avatar) : null,
                'last_activity_at' => $u->last_activity_at,
                'is_online'  => $u->last_activity_at && $u->last_activity_at->diffInMinutes(now()) <= 5,
            ];
        });

        return [
            'users' => $users,
        ];
    }
}