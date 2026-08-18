<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FitcoinTransaction;
use App\Models\Step;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FitcoinController extends Controller
{
    /**
     * Get current fitcoin balance and today's available steps.
     */
    public function balance()
    {
        /** @var User $user */
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        $step = Step::where('user_id', $user->id)
                    ->where('date', $today)
                    ->first();

        $availableSteps = $step ? $step->steps : 0;

        return response()->json([
            'fitcoin_balance' => $user->fitcoin_balance,
            'today_available_steps' => $availableSteps,
            'conversion_rate' => config('fitcoin.conversion_rate'),
        ]);
    }

    /**
     * Manual conversion endpoint (optional, for user override).
     */
    public function convert(Request $request)
    {
        $request->validate([
            'steps_to_convert' => 'required|integer|min:1',
        ]);

        /** @var User $user */
        $user = Auth::user();
        $stepsToConvert = $request->steps_to_convert;
        $conversionRate = config('fitcoin.conversion_rate');
        $today = Carbon::today()->toDateString();

        DB::beginTransaction();

        try {
            $user = User::where('id', $user->id)->lockForUpdate()->first();

            $step = Step::where('user_id', $user->id)
                        ->where('date', $today)
                        ->lockForUpdate()
                        ->first();

            $availableSteps = $step ? $step->steps : 0;

            if ($stepsToConvert > $availableSteps) {
                DB::rollBack();
                return response()->json([
                    'error' => 'Not enough steps available for today.'
                ], 422);
            }

            $fitcoinsEarned = intdiv($stepsToConvert, $conversionRate);

            if ($fitcoinsEarned < 1) {
                DB::rollBack();
                return response()->json([
                    'error' => "Minimum steps required: {$conversionRate} steps = 1 Fitcoin."
                ], 422);
            }

            if ($step) {
                $step->steps -= $stepsToConvert;
                $step->save();
            }

            FitcoinTransaction::create([
                'user_id' => $user->id,
                'steps_converted' => $stepsToConvert,
                'fitcoins_earned' => $fitcoinsEarned,
                'conversion_date' => $today,
            ]);

            $user->increment('fitcoin_balance', $fitcoinsEarned);

            DB::commit();

            $user->refresh();
            $remainingSteps = max(0, $availableSteps - $stepsToConvert);

            return response()->json([
                'fitcoins_earned' => $fitcoinsEarned,
                'fitcoin_balance' => $user->fitcoin_balance,
                'remaining_steps' => $remainingSteps,
                'conversion_rate' => $conversionRate,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Something went wrong. Please try again.'
            ], 500);
        }
    }

    /**
     * Automatically convert any full conversion‑rate chunks.
     *
     * @param  User  $user
     * @return void
     */
    public static function autoConvert(User $user): void
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
                'user_id' => $lockedUser->id,
                'steps_converted' => $stepsToConvert,
                'fitcoins_earned' => $fitcoinsEarned,
                'conversion_date' => $today,
            ]);

            $lockedUser->increment('fitcoin_balance', $fitcoinsEarned);
        });
    }
}