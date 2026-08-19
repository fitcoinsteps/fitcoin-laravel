<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CryptoWithdrawal;
use App\Services\CryptoService;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    protected $cryptoService;

    public function __construct(CryptoService $cryptoService)
    {
        $this->cryptoService = $cryptoService;
        // ✅ Remove middleware from constructor - handled in routes
    }

    // ==================== WITHDRAWALS MANAGEMENT ====================

    /**
     * Display a listing of crypto withdrawals.
     */
    public function index(Request $request)
    {
        $query = CryptoWithdrawal::with('user');

        // Filter by status
        if ($request->status && in_array($request->status, ['pending', 'processing', 'completed', 'failed'])) {
            $query->where('status', $request->status);
        }

        // Filter by currency
        if ($request->currency) {
            $query->where('crypto_currency', $request->currency);
        }

        // Filter by date range
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $withdrawals = $query->orderBy('created_at', 'desc')->paginate(20);
        $currencies = CryptoWithdrawal::distinct()->pluck('crypto_currency');

        return view('admin.withdrawals.index', compact('withdrawals', 'currencies'));
    }

    /**
     * Display a specific withdrawal.
     */
    public function show($id)
    {
        $withdrawal = CryptoWithdrawal::with('user')->findOrFail($id);
        return view('admin.withdrawals.show', compact('withdrawal'));
    }

    /**
     * Process a withdrawal (mark as processing).
     */
    public function process(Request $request, $id)
    {
        try {
            $request->validate([
                'transaction_hash' => 'nullable|string|max:255',
                'admin_notes' => 'nullable|string|max:1000',
            ]);

            $withdrawal = $this->cryptoService->processWithdrawal(
                $id,
                $request->transaction_hash
            );

            return redirect()->back()
                ->with('success', 'Withdrawal is now being processed!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to process withdrawal: ' . $e->getMessage());
        }
    }

    /**
     * Complete a withdrawal.
     */
    public function complete(Request $request, $id)
    {
        try {
            $request->validate([
                'transaction_hash' => 'required|string|max:255',
                'admin_notes' => 'nullable|string|max:1000',
            ]);

            $withdrawal = CryptoWithdrawal::findOrFail($id);

            if ($withdrawal->status !== 'processing') {
                return redirect()->back()
                    ->with('error', 'Only processing withdrawals can be completed.');
            }

            $withdrawal->markAsCompleted($request->transaction_hash);

            return redirect()->back()
                ->with('success', 'Withdrawal completed successfully! Transaction hash: ' . $request->transaction_hash);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to complete withdrawal: ' . $e->getMessage());
        }
    }

    /**
     * Fail a withdrawal and refund user.
     */
    public function fail(Request $request, $id)
    {
        try {
            $request->validate([
                'reason' => 'required|string|max:500',
                'admin_notes' => 'nullable|string|max:1000',
            ]);

            $withdrawal = $this->cryptoService->failWithdrawal(
                $id,
                $request->reason
            );

            return redirect()->back()
                ->with('success', 'Withdrawal failed and ' . $withdrawal->fitcoins_spent . ' FIT coins refunded to user!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to fail withdrawal: ' . $e->getMessage());
        }
    }

    /**
     * Export withdrawals as CSV.
     */
    public function export(Request $request)
    {
        $query = CryptoWithdrawal::with('user');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $withdrawals = $query->get();

        $csv = "ID,User,Currency,Amount,Network,Wallet Address,Status,FIT Spent,Date\n";
        foreach ($withdrawals as $w) {
            $csv .= "{$w->id},{$w->user->email},{$w->crypto_currency},{$w->crypto_amount},";
            $csv .= "{$w->network},{$w->wallet_address},{$w->status},{$w->fitcoins_spent},{$w->created_at}\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="withdrawals-' . date('Y-m-d') . '.csv"');
    }

    /**
     * Get statistics for withdrawals.
     */
    public function statistics()
    {
        $stats = [
            'total_withdrawals' => CryptoWithdrawal::count(),
            'pending_withdrawals' => CryptoWithdrawal::where('status', 'pending')->count(),
            'processing_withdrawals' => CryptoWithdrawal::where('status', 'processing')->count(),
            'completed_withdrawals' => CryptoWithdrawal::where('status', 'completed')->count(),
            'failed_withdrawals' => CryptoWithdrawal::where('status', 'failed')->count(),
            'total_crypto_amount' => CryptoWithdrawal::where('status', 'completed')->sum('crypto_amount'),
            'total_fitcoins_spent' => CryptoWithdrawal::where('status', 'completed')->sum('fitcoins_spent'),
            'total_admin_fees' => CryptoWithdrawal::where('status', 'completed')->sum('admin_fee'),
        ];

        // Group by currency
        $byCurrency = CryptoWithdrawal::where('status', 'completed')
            ->selectRaw('crypto_currency, SUM(crypto_amount) as total, COUNT(*) as count')
            ->groupBy('crypto_currency')
            ->get();

        $stats['by_currency'] = $byCurrency;

        return response()->json($stats);
    }

    /**
     * Get recent withdrawals for dashboard.
     */
    public function recent($limit = 10)
    {
        $withdrawals = CryptoWithdrawal::with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json($withdrawals);
    }
}