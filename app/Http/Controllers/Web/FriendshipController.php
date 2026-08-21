<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FriendshipService;
use Illuminate\Http\Request;

class FriendshipController extends Controller
{
    protected FriendshipService $friendshipService;

    public function __construct(FriendshipService $friendshipService)
    {
        $this->friendshipService = $friendshipService;
    }

    public function index()
    {
        /** @var User $user */
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return response()->json($this->friendshipService->listFriends($user));
    }

    public function sendRequest(Request $request)
    {
        $request->validate(['receiver_id' => 'required|integer|exists:users,id']);
        $user = auth('api')->user();
        try {
            $result = $this->friendshipService->sendRequest($user, $request->receiver_id);
            return response()->json($result, 201);
        } catch (\Exception $e) {
            $status = $e->getCode() ?: 500;
            return response()->json(['error' => $e->getMessage()], $status);
        }
    }

    public function accept(Request $request)
    {
        $request->validate(['friendship_id' => 'required|integer|exists:friendships,id']);
        $user = auth('api')->user();
        try {
            $result = $this->friendshipService->acceptRequest($user, $request->friendship_id);
            return response()->json($result);
        } catch (\Exception $e) {
            $status = $e->getCode() ?: 500;
            return response()->json(['error' => $e->getMessage()], $status);
        }
    }

    public function reject(Request $request)
    {
        $request->validate(['friendship_id' => 'required|integer|exists:friendships,id']);
        $user = auth('api')->user();
        try {
            $result = $this->friendshipService->rejectRequest($user, $request->friendship_id);
            return response()->json($result);
        } catch (\Exception $e) {
            $status = $e->getCode() ?: 500;
            return response()->json(['error' => $e->getMessage()], $status);
        }
    }

    public function pending()
    {
        $user = auth('api')->user();
        return response()->json($this->friendshipService->pendingRequests($user));
    }

    public function remove(int $friendId)
    {
        $user = auth('api')->user();
        try {
            $result = $this->friendshipService->removeFriend($user, $friendId);
            return response()->json($result);
        } catch (\Exception $e) {
            $status = $e->getCode() ?: 500;
            return response()->json(['error' => $e->getMessage()], $status);
        }
    }

    public function searchUsers(Request $request)
    {
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $query = $request->get('query');
        $result = $this->friendshipService->searchUsers($user, $query);
        return response()->json($result);
    }
}