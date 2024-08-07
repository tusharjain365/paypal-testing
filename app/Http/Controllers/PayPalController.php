<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CreditTransaction;
use App\Models\User;
use App\Services\PayPalService;
use Illuminate\Support\Facades\Log;

class PayPalController extends Controller
{
    private $paypal;

    public function __construct(PayPalService $paypal)
    {
        $this->paypal = $paypal;
    }

    public function createPayment(Request $request)
    {
        $amount = (float) $request->amount;
        $redirectUrl = $this->paypal->createOrder($amount);

        return redirect($redirectUrl);
    }

    public function executePayment(Request $request)
    {
        $orderId = $request->query('token');
        try {
            $result = $this->paypal->captureOrder($orderId);

            Log::info($result);

            $user = Auth::user();
            $amount = $result['purchase_units'][0]['payments']['captures'][0]['amount']['value'];

            $credits = ($amount == '49.00') ? 125 : (($amount == '456.00') ? 125 * 12 : 0);
            $expiryDate = ($amount == '49.00') ? now()->addMonth() : (($amount == '456.00') ? now()->addYear() : null);
            
            if ($credits > 0) {
                $this->incrementCredits($user, $credits, 'admin', $expiryDate);
                return redirect('/dashboard')->with('success', 'Payment successful and credits added.');
            } else {
                return redirect('/')->with('error', 'Invalid payment amount.');
            }
        } catch (\Exception $ex) {
            Log::info($ex->getmessage());
            return redirect('/')->with('error', 'Payment failed.');
        }
    }

    private function incrementCredits(User $user, int $amount, string $processedBy, $expiryDate)
    {
        $user->credits += $amount;
        $user->save();

        CreditTransaction::create([
            'user_id' => $user->id,
            'transaction_type' => 'credit',
            'amount' => $amount,
            'processed_by' => $processedBy,
            'expiry_date' => $expiryDate,
        ]);
    }
}
