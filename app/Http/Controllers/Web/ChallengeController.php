<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ChallengeService;
use Illuminate\Http\Request;

class ChallengeController extends Controller
{
    protected ChallengeService $challengeService;

    public function __construct(ChallengeService $challengeService)
    {
        $this->challengeService = $challengeService;
    }

    /**
     * GET /challenges
     */
    public function index()
    {
        /** @var User $user */
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json($this->challengeService->listActiveChallenges());
    }

    /**
     * POST /challenges
     */
    public function store(Request $request)
    {
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'goal_type' => 'sometimes|in:steps',
            'goal_value' => 'required|integer|min:1',
            'time_limit_minutes' => 'required|integer|min:1',
            'reward_fitcoins' => 'sometimes|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        try {
            $challenge = $this->challengeService->createChallenge($user, $data);
            return response()->json($challenge, 201);
        } catch (\Exception $e) {
            $status = $e->getCode() ?: 500;
            return response()->json(['error' => $e->getMessage()], $status);
        }
    }

    /**
     * PUT /challenges/{id}
     */
    public function update(Request $request, int $id)
    {
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'goal_type' => 'sometimes|in:steps',
            'goal_value' => 'sometimes|integer|min:1',
            'time_limit_minutes' => 'sometimes|integer|min:1',
            'reward_fitcoins' => 'sometimes|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        try {
            $challenge = $this->challengeService->updateChallenge($user, $id, $data);
            return response()->json($challenge);
        } catch (\Exception $e) {
            $status = $e->getCode() ?: 500;
            return response()->json(['error' => $e->getMessage()], $status);
        }
    }

    /**
     * DELETE /challenges/{id}
     */
    public function destroy(int $id)
    {
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $this->challengeService->deleteChallenge($user, $id);
            return response()->json(['message' => 'Challenge deleted.']);
        } catch (\Exception $e) {
            $status = $e->getCode() ?: 500;
            return response()->json(['error' => $e->getMessage()], $status);
        }
    }

    /**
     * POST /challenges/{id}/activate
     */
    public function activate(int $challengeId)
    {
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $result = $this->challengeService->activateChallenge($user, $challengeId);
            return response()->json($result, 201);
        } catch (\Exception $e) {
            $status = $e->getCode() ?: 500;
            return response()->json(['error' => $e->getMessage()], $status);
        }
    }

    /**
     * GET /challenges/active
     */
    public function active()
    {
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json($this->challengeService->getActiveChallenges($user));
    }

    /**
     * GET /challenges/{id}/progress
     */
    public function checkProgress(int $userChallengeId)
    {
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $result = $this->challengeService->checkProgress($user, $userChallengeId);
            return response()->json($result);
        } catch (\Exception $e) {
            $status = $e->getCode() ?: 500;
            return response()->json(['error' => $e->getMessage()], $status);
        }
    }

    /**
     * GET /challenges/history
     */
    public function history()
    {
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json($this->challengeService->getHistory($user));
    }
}