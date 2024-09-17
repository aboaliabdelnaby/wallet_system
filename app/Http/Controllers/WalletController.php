<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest;
use App\Http\Requests\TransferRequest;
use App\Http\Service\WalletServiceInterface;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{

    public function __construct(protected WalletServiceInterface $walletService){}

    public function topup()
    {
        return view('wallet.topup');
    }

    public function topup_payment(PaymentRequest $request): RedirectResponse
    {
        $result=$this->walletService->topup($request->all());
        return $result?redirect()->route('home'):redirect()->back();
    }

    public function history()
    {
        $transactions = $this->walletService->history();
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
        $result=$this->walletService->transfer($request->all());
        return $result?redirect()->route('home'):redirect()->back();

    }
}
