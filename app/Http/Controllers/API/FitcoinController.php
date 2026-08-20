<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FitcoinService;
use Illuminate\Http\Request;

class FitcoinController extends Controller
{
    protected FitcoinService $fitcoinService;

    public function __construct(FitcoinService $fitcoinService)
    {
        $this->fitcoinService = $fitcoinService;
    }

    public function balance()
    {
        /** @var User $user */
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json($this->fitcoinService->getBalance($user));
    }

    public function convert(Request $request)
    {
        $request->validate([
            'steps_to_convert' => 'required|integer|min:1',
        ]);

        /** @var User $user */
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $result = $this->fitcoinService->convert($user, $request->steps_to_convert);
            return response()->json($result);
        } catch (\Exception $e) {
            $status = $e->getCode() ?: 500;
            return response()->json(['error' => $e->getMessage()], $status);
        }
    }
}