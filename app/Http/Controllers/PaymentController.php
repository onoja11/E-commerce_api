<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Transaction as ModelsTransaction;
use App\Models\User;
use App\Models\Wallet;
use Binkode\Paystack\Support\Transaction;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    // Initialize Paystack payment
    public function initialize(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100',
            'email'  => 'required|email',
        ]);

        $user = User::where('email', $request->email)->firstOrFail();

        // Create wallet if user does not have one
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0]
        );

        $payment = Transaction::initialize([
            'amount'       => $request->amount * 100, // Paystack uses kobo
            'email'        => $request->email,
            'callback_url' => env("APP_URL") . '/api/pay/callback',
        ]);

        if (!$payment['status']) {
            return response()->json(['message' => 'Payment initialization failed'], 500);
        }

        // Save pending transaction
        ModelsTransaction::create([
            'user_id'     => $user->id,
            'wallet_id'   => $wallet->id,
            'amount'      => $request->amount,
            'description' => 'pending',
            'order_id'    => null,
            'reference'   => $payment['data']['reference'],
        ]);

        return response()->json([
            'authorization_url' => $payment['data']['authorization_url'],
            'reference'         => $payment['data']['reference'],
        ]);
    }

    // Callback after payment
    public function callback(Request $request)
    {
        $reference = $request->query('reference');
        $verification = Transaction::verify($reference);

        if ($verification['status'] && $verification['data']['status'] === 'success') {
            $amount = $verification['data']['amount'] / 100;

            // Find pending transaction
            $transaction = ModelsTransaction::where('reference', $reference)->first();
            if ($transaction && $transaction->description !== 'income') {
                $wallet = $transaction->wallet;

                // Prevent double credit
                if ($wallet) {
                    $wallet->balance += $amount;
                    $wallet->save();
                }

                $transaction->description = 'income';
                $transaction->save();
            }

            return redirect(env("FRONTEND_URL") . "/wallet?status=success&reference={$reference}");
        }

        return redirect(env("FRONTEND_URL") . "/wallet?status=failed&reference={$reference}");
    }
}
