<?php

namespace App\Services;

use App\Models\Challenge;
use App\Models\Step;
use App\Models\User;
use App\Models\UserChallenge;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ChallengeService
{
    /**
     * List active challenges (all users can see them).
     */
    public function listActiveChallenges(): array
    {
        $challenges = Challenge::where('is_active', true)
            ->get()
            ->map(function ($challenge) {
                return [
                    'id' => $challenge->id,
                    'uuid' => $challenge->uuid,
                    'title' => $challenge->title,
                    'description' => $challenge->description,
                    'goal_type' => $challenge->goal_type,
                    'goal_value' => $challenge->goal_value,
                    'time_limit_minutes' => $challenge->time_limit_minutes,
                    'reward_fitcoins' => $challenge->reward_fitcoins,
                    'created_by' => $challenge->created_by,
                ];
            });

        return ['challenges' => $challenges];
    }

    /**
     * Create a new challenge (by user).
     */
    public function createChallenge(User $user, array $data): Challenge
    {
        return Challenge::create([
            'uuid' => (string) Str::uuid(),
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'goal_type' => $data['goal_type'] ?? 'steps',
            'goal_value' => $data['goal_value'],
            'time_limit_minutes' => $data['time_limit_minutes'],
            'reward_fitcoins' => $data['reward_fitcoins'] ?? 0,
            'is_active' => $data['is_active'] ?? true,
            'created_by' => $user->id,
        ]);
    }

    /**
     * Update a challenge (only if user owns it).
     */
    public function updateChallenge(User $user, int $challengeId, array $data): Challenge
    {
        $challenge = Challenge::where('id', $challengeId)
            ->where('created_by', $user->id)
            ->first();

        if (!$challenge) {
            throw new \Exception('Challenge not found or you do not own it.', 404);
        }

        $challenge->update($data);
        return $challenge;
    }

    /**
     * Delete a challenge (only if user owns it).
     */
    public function deleteChallenge(User $user, int $challengeId): void
    {
        $challenge = Challenge::where('id', $challengeId)
            ->where('created_by', $user->id)
            ->first();

        if (!$challenge) {
            throw new \Exception('Challenge not found or you do not own it.', 404);
        }

        $challenge->delete();
    }

    /**
     * Activate a challenge for the user.
     */
    public function activateChallenge(User $user, int $challengeId): array
    {
        $challenge = Challenge::where('id', $challengeId)
            ->where('is_active', true)
            ->first();

        if (!$challenge) {
            throw new \Exception('Challenge not found or inactive.', 404);
        }

        // Check if already active for this user
        $existing = UserChallenge::where('user_id', $user->id)
            ->where('challenge_id', $challengeId)
            ->where('status', 'active')
            ->first();

        if ($existing) {
            throw new \Exception('You already activated this challenge.', 422);
        }

        // Limit active challenges to 3
        $activeCount = UserChallenge::where('user_id', $user->id)
            ->where('status', 'active')
            ->count();

        if ($activeCount >= 3) {
            throw new \Exception('You can have at most 3 active challenges.', 422);
        }

        $today = Carbon::today()->toDateString();
        $step = Step::where('user_id', $user->id)
            ->where('date', $today)
            ->first();
        $currentSteps = $step ? $step->steps : 0;

        $userChallenge = UserChallenge::create([
            'uuid'          => (string) Str::uuid(),
            'user_id'       => $user->id,
            'challenge_id'  => $challengeId,
            'status'        => 'active',
            'started_at'    => now(),
            'steps_at_start'=> $currentSteps,
        ]);

        return [
            'user_challenge' => $userChallenge,
            'message'        => 'Challenge activated.',
        ];
    }

    /**
     * Get active challenges for user with progress info.
     */
    public function getActiveChallenges(User $user): array
    {
        $activeChallenges = UserChallenge::with('challenge')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->get()
            ->map(function ($uc) use ($user) {
                // Get current steps today
                $today = Carbon::today()->toDateString();
                $step = Step::where('user_id', $user->id)
                    ->where('date', $today)
                    ->first();
                $currentSteps = $step ? $step->steps : 0;

                $stepsGained = max(0, $currentSteps - $uc->steps_at_start);
                $goal = $uc->challenge->goal_value;
                $progressPercent = $goal > 0 ? min(100, round(($stepsGained / $goal) * 100, 2)) : 0;

                return [
                    'id' => $uc->id,
                    'challenge' => [
                        'id' => $uc->challenge->id,
                        'title' => $uc->challenge->title,
                        'goal_value' => $uc->challenge->goal_value,
                        'time_limit_minutes' => $uc->challenge->time_limit_minutes,
                    ],
                    'started_at' => $uc->started_at,
                    'steps_at_start' => $uc->steps_at_start,
                    'steps_gained' => $stepsGained,
                    'progress_percent' => $progressPercent,
                    'time_remaining_minutes' => max(0, $uc->started_at->addMinutes($uc->challenge->time_limit_minutes)->diffInMinutes(now())),
                ];
            });

        return ['active_challenges' => $activeChallenges];
    }

    /**
     * Check progress of a user challenge.
     */
    public function checkProgress(User $user, int $userChallengeId): array
    {
        $userChallenge = UserChallenge::with('challenge')
            ->where('id', $userChallengeId)
            ->where('user_id', $user->id)
            ->first();

        if (!$userChallenge) {
            throw new \Exception('Challenge not found.', 404);
        }

        if ($userChallenge->status !== 'active') {
            throw new \Exception('Challenge is not active.', 422);
        }

        $today = Carbon::today()->toDateString();
        $step = Step::where('user_id', $user->id)
            ->where('date', $today)
            ->first();
        $currentSteps = $step ? $step->steps : 0;

        $stepsGained = max(0, $currentSteps - $userChallenge->steps_at_start);
        $goal = $userChallenge->challenge->goal_value;
        $progressPercent = $goal > 0 ? min(100, round(($stepsGained / $goal) * 100, 2)) : 0;
        $isCompleted = $stepsGained >= $goal;

        $timeLimit = $userChallenge->challenge->time_limit_minutes;
        $startedAt = $userChallenge->started_at;
        $timePassed = $startedAt->diffInMinutes(now());
        $timeRemaining = max(0, $timeLimit - $timePassed);
        $isExpired = $timeRemaining <= 0;

        if ($isCompleted) {
            $userChallenge->update([
                'status' => 'completed',
                'completed_at' => now(),
                'steps_at_completion' => $currentSteps,
                'reward_earned' => $userChallenge->challenge->reward_fitcoins,
            ]);

            $user->increment('fitcoin_balance', $userChallenge->challenge->reward_fitcoins);

            return [
                'status' => 'completed',
                'progress_percent' => 100,
                'steps_gained' => $stepsGained,
                'reward_earned' => $userChallenge->challenge->reward_fitcoins,
            ];
        }

        if ($isExpired) {
            $userChallenge->update(['status' => 'failed']);

            return [
                'status' => 'failed',
                'progress_percent' => $progressPercent,
                'steps_gained' => $stepsGained,
                'time_remaining_minutes' => 0,
            ];
        }

        return [
            'status' => 'active',
            'progress_percent' => $progressPercent,
            'steps_gained' => $stepsGained,
            'time_remaining_minutes' => $timeRemaining,
            'steps_needed' => max(0, $goal - $stepsGained),
        ];
    }

    /**
     * Get user challenge history.
     */
    public function getHistory(User $user): array
    {
        $history = UserChallenge::with('challenge')
            ->where('user_id', $user->id)
            ->whereIn('status', ['completed', 'failed', 'cancelled'])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($uc) {
                return [
                    'id' => $uc->id,
                    'challenge_title' => $uc->challenge->title,
                    'status' => $uc->status,
                    'steps_at_start' => $uc->steps_at_start,
                    'steps_at_completion' => $uc->steps_at_completion,
                    'reward_earned' => $uc->reward_earned,
                    'started_at' => $uc->started_at,
                    'completed_at' => $uc->completed_at,
                ];
            });

        return ['history' => $history];
    }
}