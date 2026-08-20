<?php

namespace App\Services;

use App\Models\FitcoinTransaction;
use App\Models\Step;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FitcoinService
{
    public function getBalance(User $user): array
    {
        $today = Carbon::today()->toDateString();
        $step = Step::where('user_id', $user->id)
                    ->where('date', $today)
                    ->first();

        $availableSteps = $step ? $step->steps : 0;

        return [
            'fitcoin_balance'       => $user->fitcoin_balance,
            'today_available_steps' => $availableSteps,
            'conversion_rate'       => config('fitcoin.conversion_rate'),
        ];
    }

    public function convert(User $user, int $stepsToConvert): array
    {
        $conversionRate = config('fitcoin.conversion_rate');
        $today = Carbon::today()->toDateString();

        if ($stepsToConvert < 1) {
            throw new \Exception('Steps to convert must be at least 1.', 422);
        }

        DB::beginTransaction();

        try {
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();
            $step = Step::where('user_id', $lockedUser->id)
                        ->where('date', $today)
                        ->lockForUpdate()
                        ->first();

            $availableSteps = $step ? $step->steps : 0;

            if ($stepsToConvert > $availableSteps) {
                DB::rollBack();
                throw new \Exception('Not enough steps available for today.', 422);
            }

            $fitcoinsEarned = intdiv($stepsToConvert, $conversionRate);

            if ($fitcoinsEarned < 1) {
                DB::rollBack();
                throw new \Exception("Minimum steps required: {$conversionRate} steps = 1 Fitcoin.", 422);
            }

            if ($step) {
                $step->steps -= $stepsToConvert;
                $step->save();
            }

            FitcoinTransaction::create([
                'user_id'          => $lockedUser->id,
                'steps_converted'  => $stepsToConvert,
                'fitcoins_earned'  => $fitcoinsEarned,
                'conversion_date'  => $today,
            ]);

            $lockedUser->increment('fitcoin_balance', $fitcoinsEarned);
            DB::commit();

            $lockedUser->refresh();
            $remainingSteps = max(0, $availableSteps - $stepsToConvert);

            return [
                'fitcoins_earned'   => $fitcoinsEarned,
                'fitcoin_balance'   => $lockedUser->fitcoin_balance,
                'remaining_steps'   => $remainingSteps,
                'conversion_rate'   => $conversionRate,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function autoConvert(User $user): void
    {
        $conversionRate = config('fitcoin.conversion_rate');
        $today = Carbon::today()->toDateString();

        DB::transaction(function () use ($user, $conversionRate, $today) {
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();

            $step = Step::where('user_id', $lockedUser->id)
                        ->where('date', $today)
                        ->lockForUpdate()
                        ->first();

            if (!$step || $step->steps < $conversionRate) {
                return;
            }

            $stepsToConvert = intdiv($step->steps, $conversionRate) * $conversionRate;
            $fitcoinsEarned = intdiv($stepsToConvert, $conversionRate);

            $step->steps -= $stepsToConvert;
            $step->save();

            FitcoinTransaction::create([
                'user_id'          => $lockedUser->id,
                'steps_converted'  => $stepsToConvert,
                'fitcoins_earned'  => $fitcoinsEarned,
                'conversion_date'  => $today,
            ]);

            $lockedUser->increment('fitcoin_balance', $fitcoinsEarned);
        });
    }
}