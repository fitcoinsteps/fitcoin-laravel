<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\StepService;
use Illuminate\Http\Request;

class StepController extends Controller
{
    protected StepService $stepService;

    public function __construct(StepService $stepService)
    {
        $this->stepService = $stepService;
    }

    public function today()
    {
        /** @var User $user */
        $user = auth('api')->user();
        if (!$user) return response()->json(['error' => 'Unauthorized'], 401);
        return response()->json($this->stepService->getToday($user));
    }

    public function sync(Request $request)
    {
        $request->validate([
            'steps' => 'required|integer|min:0',
            'goal'  => 'sometimes|integer|min:1',
            'date'  => 'sometimes|date_format:Y-m-d',
        ]);

        $user = auth('api')->user();
        if (!$user) return response()->json(['error' => 'Unauthorized'], 401);

        try {
            $result = $this->stepService->sync($user, $request->steps, $request->goal, $request->date);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateGoal(Request $request)
    {
        $request->validate(['goal' => 'required|integer|min:1']);
        $user = auth('api')->user();
        if (!$user) return response()->json(['error' => 'Unauthorized'], 401);
        return response()->json($this->stepService->updateGoal($user, $request->goal));
    }
}