<?php

namespace App\Http\Service;

use App\Http\Enum\TransactionTypeEnum;
use App\Models\TransactionHistory;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletFees;
use Illuminate\Support\Facades\DB;
use Stripe\Charge;
use Stripe\Stripe;

class WalletService implements WalletServiceInterface
{
    public function topUp(array $data): bool
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        try {
            Charge::create([
                'amount' => $data['amount'] * 100,
                'currency' => 'usd',
                'source' => $data['stripeToken'],
                'description' => 'Test Payment',
            ]);
            DB::transaction(function () use ($data) {
                $wallet = Wallet::where('user_id', auth()->id())->first();
                $wallet->update([
                    'balance' => $wallet->balance + $data['amount']
                ]);
                TransactionHistory::create([
                    'amount' => $data['amount'],
                    'type' => TransactionTypeEnum::TOPUP,
                    'user_id' => auth()->id()
                ]);
            });
            session()->flash('success', 'Payment successful!');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return false;
        }
        return true;
    }
    public function history()
    {
        return TransactionHistory::where('user_id', auth()->id())->latest()->paginate(10);
    }
    public function transfer(array $data): bool
    {
        $user = User::where('phone', $data['phone'])->where('id', '!=', auth()->id())->first();
        if (!$user) {
            session()->flash('error', 'user not found');
            return false;
        }
        $wallet = Wallet::where('user_id', auth()->id())->first();
        if ($wallet->balance < $data['amount']) {
            session()->flash('error', 'balance not enough');
            return false;
        }
        try {
            DB::transaction(function () use ($data) {
                $senderWallet = Wallet::where('user_id', auth()->id())->first();
                $recievingWallet = Wallet::whereHas('user', function ($query) use ($data) {
                    $query->where('phone', $data['phone']);
                })->first();
                $amount = $data['amount'];
                $fees = WalletFees::first();
                $fees_amount = $amount_after_fees = 0;
                if ($amount > $fees->start_amount) {
                    $fees_amount = ($fees->percentage / 100) * $amount;
                    $amount_after_fees = $amount - $fees_amount;
                }
                $senderWallet->update([
                    'balance' => $senderWallet->balance - $amount
                ]);
                $recievingWallet->update([
                    'balance' => $recievingWallet->balance + $amount_after_fees
                ]);
                TransactionHistory::create([
                    'amount' => $amount,
                    'type' => TransactionTypeEnum::SENDING,
                    'user_id' => $senderWallet->user_id,
                    'fees_amount' => $fees_amount,
                ]);
                TransactionHistory::create([
                    'amount' => $amount,
                    'type' => TransactionTypeEnum::RECIEVING,
                    'user_id' => $recievingWallet->user_id,
                    'fees_amount' => $fees_amount,
                ]);
            });
            session()->flash('success', 'Transfer successful!');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return false;
        }
        return true;
    }

}
