<?php

namespace App\Http\Controllers;

use App\Http\Enum\TransactionTypeEnum;
use App\Http\Requests\PaymentRequest;
use App\Http\Requests\TransferRequest;
use App\Models\TransactionHistory;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Charge;
use Stripe\Stripe;
use Stripe\Transfer;

class WalletController extends Controller
{
    public function topup()
    {
        return view('wallet.topup');
    }

    public function payment(PaymentRequest $request): RedirectResponse
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            Charge::create([
                'amount' => $request->get('amount') * 100,
                'currency' => 'usd',
                'source' => $request->get('stripeToken'),
                'description' => 'Test Payment',
            ]);
            DB::transaction(function () use ($request) {
                $wallet = Wallet::where('user_id', auth()->id())->first();
                $wallet->update([
                    'balance' => $wallet->balance + $request->get('amount')
                ]);
                TransactionHistory::create([
                    'amount' => $request->get('amount'),
                    'type' => TransactionTypeEnum::TOPUP,
                    'user_id' => auth()->id()
                ]);
            });
            $request->session()->flash('success', 'Payment successful!');
        } catch (\Exception $e) {
            $request->session()->flash('error', $e->getMessage());
            return redirect()->back();
        }
        return redirect()->route('home');
    }

    public function history()
    {
        $transactions = TransactionHistory::where('user_id', auth()->id())->latest()->paginate(10);
        return view('wallet.history', compact('transactions'));
    }

    public function check_phone(Request $request)
    {
        $user = User::where('phone', $request->get('phone'))->where('id', '!=', auth()->id())->first();
        if ($user) {
            return response()->json(['success' => true, 'data' => ['name' => $user->name, 'phone' => $user->phone, 'email' => $user->email]]);
        } else {
            return response()->json(['success' => false, 'error' => 'User not found']);
        }
    }

    public function transfer()
    {
        return view('wallet.transfer');
    }

    public function transfer_payment(TransferRequest $request)
    {
        $user = User::where('phone', $request->get('phone'))->where('id', '!=', auth()->id())->first();
        if (!$user) {
            $request->session()->flash('error', 'user not found');
            return redirect()->back();
        }
        $wallet = Wallet::where('user_id', auth()->id())->first();
        if ($wallet->balance < $request->get('amount')) {
            $request->session()->flash('error', 'balance not enough');
            return redirect()->back();
        }
        try {
            DB::transaction(function () use ($request) {
                $senderWallet = Wallet::where('user_id', auth()->id())->first();
                $recievingWallet = Wallet::whereHas('user', function ($query) use ($request) {
                    $query->where('phone', $request->get('phone'));
                })->first();
                $senderWallet->update([
                    'balance' => $senderWallet->balance - $request->get('amount')
                ]);
                $recievingWallet->update([
                    'balance' => $recievingWallet->balance + $request->get('amount')
                ]);
                TransactionHistory::create([
                    'amount' => $request->get('amount'),
                    'type' => TransactionTypeEnum::SENDING,
                    'user_id' => $senderWallet->user_id
                ]);
                TransactionHistory::create([
                    'amount' => $request->get('amount'),
                    'type' => TransactionTypeEnum::RECIEVING,
                    'user_id' => $recievingWallet->user_id
                ]);
            });
            $request->session()->flash('success', 'Transfer successful!');
        } catch (\Exception $e) {
            $request->session()->flash('error', $e->getMessage());
            return redirect()->back();
        }
        return redirect()->route('home');

    }
}
