<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GiftCard;
use App\Models\GiftCardRedemption;
use App\Services\GiftCardService;
use App\Http\Requests\Admin\CreateGiftCardRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GiftCardController extends Controller
{
    protected $giftCardService;

    public function __construct(GiftCardService $giftCardService)
    {
        $this->giftCardService = $giftCardService;
        // ✅ Remove middleware from constructor - handled in routes
    }

    // ==================== GIFT CARDS MANAGEMENT ====================

    /**
     * Display a listing of gift cards.
     */
    public function index(Request $request)
    {
        $query = GiftCard::query();

        // Filter by provider
        if ($request->provider) {
            $query->where('provider', $request->provider);
        }

        // Filter by status
        if ($request->status === 'used') {
            $query->where('is_used', true);
        } elseif ($request->status === 'available') {
            $query->where('is_used', false);
        }

        $giftCards = $query->orderBy('created_at', 'desc')->paginate(20);
        $providers = GiftCard::distinct()->pluck('provider');

        return view('admin.gift-cards.index', compact('giftCards', 'providers'));
    }

    /**
     * Show the form for creating a new gift card.
     */
    public function create()
    {
        $providers = ['amazon', 'google_play', 'steam', 'apple'];
        return view('admin.gift-cards.create', compact('providers'));
    }

    /**
     * Store a newly created gift card.
     */
    public function store(CreateGiftCardRequest $request)
    {
        try {
            $giftCard = $this->giftCardService->createGiftCard($request->validated());

            return redirect()->route('admin.gift-cards.index')
                ->with('success', 'Gift card added successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to add gift card: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing a gift card.
     */
    public function edit(GiftCard $giftCard)
    {
        if ($giftCard->is_used) {
            return redirect()->route('admin.gift-cards.index')
                ->with('error', 'Cannot edit a used gift card.');
        }

        $providers = ['amazon', 'google_play', 'steam', 'apple'];
        return view('admin.gift-cards.edit', compact('giftCard', 'providers'));
    }

    /**
     * Update a gift card.
     */
    public function update(Request $request, GiftCard $giftCard)
    {
        if ($giftCard->is_used) {
            return redirect()->route('admin.gift-cards.index')
                ->with('error', 'Cannot update a used gift card.');
        }

        $validator = Validator::make($request->all(), [
            'value' => 'required|numeric|min:0.01',
            'currency' => 'required|string|size:3',
            'fitcoin_cost' => 'required|integer|min:1',
            'expires_at' => 'nullable|date|after:today',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $giftCard->update($request->only(['value', 'currency', 'fitcoin_cost', 'expires_at']));

        return redirect()->route('admin.gift-cards.index')
            ->with('success', 'Gift card updated successfully!');
    }

    /**
     * Delete a gift card.
     */
    public function destroy(GiftCard $giftCard)
    {
        if ($giftCard->is_used) {
            return redirect()->route('admin.gift-cards.index')
                ->with('error', 'Cannot delete a used gift card.');
        }

        $giftCard->delete();

        return redirect()->route('admin.gift-cards.index')
            ->with('success', 'Gift card deleted successfully!');
    }

    /**
     * Show bulk upload form.
     */
    public function bulkUpload()
    {
        $providers = ['amazon', 'google_play', 'steam', 'apple'];
        return view('admin.gift-cards.bulk-upload', compact('providers'));
    }

    /**
     * Store bulk uploaded gift cards.
     */
    public function storeBulk(Request $request)
    {
        $request->validate([
            'gift_cards' => 'required|string',
            'provider' => 'required|in:amazon,google_play,steam,apple',
            'value' => 'required|numeric|min:0.01',
            'fitcoin_cost' => 'required|integer|min:1',
            'currency' => 'required|string|size:3',
        ]);

        $lines = explode("\n", trim($request->gift_cards));
        $count = 0;
        $errors = [];

        foreach ($lines as $line) {
            $code = trim($line);
            if (empty($code)) continue;

            try {
                $this->giftCardService->createGiftCard([
                    'provider' => $request->provider,
                    'code' => $code,
                    'value' => $request->value,
                    'currency' => $request->currency,
                    'fitcoin_cost' => $request->fitcoin_cost,
                    'purchased_at' => now(),
                ]);
                $count++;
            } catch (\Exception $e) {
                $errors[] = "Failed to add code: $code - " . $e->getMessage();
            }
        }

        $message = "{$count} gift cards added successfully!";
        if (!empty($errors)) {
            $message .= " Errors: " . implode(', ', $errors);
            return redirect()->route('admin.gift-cards.index')
                ->with('warning', $message);
        }

        return redirect()->route('admin.gift-cards.index')
            ->with('success', $message);
    }

    /**
     * Export gift cards as CSV.
     */
    public function export(Request $request)
    {
        $query = GiftCard::query();

        if ($request->status === 'used') {
            $query->where('is_used', true);
        } elseif ($request->status === 'available') {
            $query->where('is_used', false);
        }

        $giftCards = $query->get();

        $csv = "ID,Provider,Code,Value,Currency,FIT Cost,Status,Used At,Created At\n";
        foreach ($giftCards as $card) {
            $csv .= "{$card->id},{$card->provider},{$card->code},{$card->value},{$card->currency},{$card->fitcoin_cost},";
            $csv .= $card->is_used ? "Used" : "Available";
            $csv .= ",{$card->used_at},{$card->created_at}\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="gift-cards-' . date('Y-m-d') . '.csv"');
    }

    // ==================== REDEMPTIONS MANAGEMENT ====================

    /**
     * Display a listing of redemptions.
     */
    public function redemptions(Request $request)
    {
        $query = GiftCardRedemption::with(['user', 'giftCard']);

        // Filter by status
        if ($request->status && in_array($request->status, ['pending', 'processing', 'completed', 'failed'])) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $redemptions = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.redemptions.index', compact('redemptions'));
    }

    /**
     * Display a specific redemption.
     */
    public function showRedemption($id)
    {
        $redemption = GiftCardRedemption::with(['user', 'giftCard'])->findOrFail($id);
        return view('admin.redemptions.show', compact('redemption'));
    }

    /**
     * Complete a redemption.
     */
    public function completeRedemption($id)
    {
        try {
            $redemption = GiftCardRedemption::findOrFail($id);
            $redemption->markAsCompleted();

            return redirect()->back()
                ->with('success', 'Redemption marked as completed!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to complete redemption: ' . $e->getMessage());
        }
    }

    /**
     * Cancel a redemption and refund user.
     */
    public function cancelRedemption($id)
    {
        try {
            $redemption = GiftCardRedemption::findOrFail($id);

            if ($redemption->status === 'completed') {
                return redirect()->back()
                    ->with('error', 'Cannot cancel a completed redemption.');
            }

            $redemption->markAsFailed('Cancelled by admin');

            // Refund user
            $user = $redemption->user;
            $user->increment('fitcoin_balance', $redemption->fitcoins_spent);

            return redirect()->back()
                ->with('success', 'Redemption cancelled and ' . $redemption->fitcoins_spent . ' FIT coins refunded!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to cancel redemption: ' . $e->getMessage());
        }
    }

    /**
     * Get statistics for gift cards.
     */
    public function statistics()
    {
        $stats = [
            'total_gift_cards' => GiftCard::count(),
            'available_gift_cards' => GiftCard::available()->count(),
            'used_gift_cards' => GiftCard::where('is_used', true)->count(),
            'expired_gift_cards' => GiftCard::where('expires_at', '<', now())->count(),
            'total_redemptions' => GiftCardRedemption::where('status', 'completed')->count(),
            'pending_redemptions' => GiftCardRedemption::where('status', 'pending')->count(),
            'total_value_issued' => GiftCardRedemption::where('status', 'completed')->sum('gift_card_value'),
            'total_fitcoins_spent' => GiftCardRedemption::where('status', 'completed')->sum('fitcoins_spent'),
        ];

        return response()->json($stats);
    }

    /**
     * Get recent redemptions for dashboard.
     */
    public function recent($limit = 10)
    {
        $redemptions = GiftCardRedemption::with(['user', 'giftCard'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json($redemptions);
    }
}