<?php

namespace App\Services;

use App\Models\Step;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StepService
{
    /**
     * Get today's step record for the user.
     */
    public function getToday(User $user): array
    {
        $today = Carbon::today()->toDateString();
        $step = Step::firstOrCreate(
            ['user_id' => $user->id, 'date' => $today],
            ['steps' => 0, 'goal' => 10000]
        );

        return [
            'steps' => $step->steps,
            'goal'  => $step->goal,
            'date'  => $today,
        ];
    }

    /**
     * Sync (update) steps for today.
     */
    public function sync(User $user, int $steps, ?int $goal = null, ?string $date = null): array
    {
        $date = $date ?? Carbon::today()->toDateString();

        DB::beginTransaction();
        try {
            $step = Step::where('user_id', $user->id)
                        ->where('date', $date)
                        ->lockForUpdate()
                        ->first();

            if (!$step) {
                $step = Step::create([
                    'user_id' => $user->id,
                    'date'    => $date,
                    'steps'   => $steps,
                    'goal'    => $goal ?? 10000,
                ]);
            } else {
                $step->steps = $steps;
                if ($goal !== null) {
                    $step->goal = $goal;
                }
                $step->save();
            }

            DB::commit();

            return [
                'steps' => $step->steps,
                'goal'  => $step->goal,
                'date'  => $step->date->toDateString(),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update the user's daily goal.
     */
    public function updateGoal(User $user, int $goal): array
    {
        $today = Carbon::today()->toDateString();
        $step = Step::where('user_id', $user->id)
                    ->where('date', $today)
                    ->first();

        if ($step) {
            $step->goal = $goal;
            $step->save();
        } else {
            Step::create([
                'user_id' => $user->id,
                'date'    => $today,
                'steps'   => 0,
                'goal'    => $goal,
            ]);
        }

        return ['goal' => $goal];
    }
}