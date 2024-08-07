<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CreditTransaction;
use App\Models\User;

class AdminController extends Controller
{
    /**
     * Show all credit transactions.
     */
    public function showCreditTransactions()
    {
        // Fetch all credit transactions
        $transactions = CreditTransaction::with('user')->get();
        return view('admin.creditTransactions', compact('transactions'));
    }

    /**
     * Increment credits for a user.
     */
    public function incrementCredits(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|integer|min:1',
            'expiry_date' => 'required|date'
        ]);

        $user = User::findOrFail($request->user_id);
        $amount = $request->amount;
        $expiryDate = $request->expiry_date;

        $this->updateCredits($user, $amount, 'admin', $expiryDate);

        return redirect()->back()->with('success', 'Credits incremented successfully.');
    }

    /**
     * Decrement credits for a user.
     */
    public function decrementCredits(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|integer|min:1'
        ]);

        $user = User::findOrFail($request->user_id);
        $amount = $request->amount;

        $this->updateCredits($user, -$amount, 'admin');

        return redirect()->back()->with('success', 'Credits decremented successfully.');
    }

    /**
     * Update user credits and record the transaction.
     */
    private function updateCredits(User $user, int $amount, string $processedBy, $expiryDate = null)
    {
        $user->credits += $amount;
        $user->save();

        CreditTransaction::create([
            'user_id' => $user->id,
            'transaction_type' => $amount > 0 ? 'credit' : 'debit',
            'amount' => abs($amount),
            'processed_by' => $processedBy,
            'expiry_date' => $expiryDate,
        ]);
    }
}
