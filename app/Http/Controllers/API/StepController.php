<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Step;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Http\Controllers\Api\FitcoinController;

class StepController extends Controller
{
    /**
     * Get today's steps for the authenticated user.
     */
    public function today()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        $step = Step::where('user_id', $user->id)
                    ->where('date', $today)
                    ->first();

        if (!$step) {
            return response()->json([
                'steps' => 0,
                'goal' => 10000,
                'date' => $today,
            ]);
        }

        return response()->json([
            'steps' => $step->steps,
            'goal' => $step->goal,
            'date' => $step->date->toDateString(),
        ]);
    }

    /**
     * Store or update step data for a given date, then auto-convert.
     */
    public function store(Request $request)
    {
        $request->validate([
            'steps' => 'required|integer|min:0',
            'goal' => 'nullable|integer|min:0',
            'date' => 'required|date',
        ]);

        $user = Auth::user();
        $date = Carbon::parse($request->date)->toDateString();

        Step::updateOrCreate(
            [
                'user_id' => $user->id,
                'date' => $date,
            ],
            [
                'steps' => $request->steps,
                'goal' => $request->goal ?? 10000,
            ]
        );

        // 🔁 Auto-convert steps to fitcoins
        FitcoinController::autoConvert($user);

        // Return the fresh step record after conversion
        $freshStep = Step::where('user_id', $user->id)
                        ->where('date', $date)
                        ->first();

        return response()->json($freshStep, 200);
    }

    /**
     * Get step history for the last 30 days.
     */
    public function history()
    {
        $user = Auth::user();
        $history = Step::where('user_id', $user->id)
                    ->orderBy('date', 'desc')
                    ->limit(30)
                    ->get();

        return response()->json($history);
    }
}