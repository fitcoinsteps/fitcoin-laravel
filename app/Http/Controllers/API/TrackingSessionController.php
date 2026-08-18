<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TrackingSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TrackingSessionController extends Controller
{
    /**
     * Store a newly created tracking session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'started_at' => 'required|date',
            'ended_at' => 'nullable|date',
            'start_lat' => 'required|numeric',
            'start_lng' => 'required|numeric',
            'end_lat' => 'nullable|numeric',
            'end_lng' => 'nullable|numeric',
            'distance_meters' => 'nullable|numeric|min:0',
            'duration_seconds' => 'nullable|numeric|min:0',
        ]);

        $session = TrackingSession::create([
            'user_id' => Auth::id(),
            'started_at' => Carbon::parse($request->started_at),
            'ended_at' => $request->ended_at ? Carbon::parse($request->ended_at) : null,
            'start_lat' => $request->start_lat,
            'start_lng' => $request->start_lng,
            'end_lat' => $request->end_lat,
            'end_lng' => $request->end_lng,
            'distance_meters' => $request->distance_meters ?? 0,
            'duration_seconds' => $request->duration_seconds ?? 0,
            'is_active' => false,
        ]);

        return response()->json($session, 201);
    }

    /**
     * Get list of the user's tracking sessions.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $sessions = TrackingSession::where('user_id', Auth::id())
                    ->orderBy('started_at', 'desc')
                    ->get();

        return response()->json($sessions);
    }
}