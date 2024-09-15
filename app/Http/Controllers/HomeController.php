<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function index()
    {
        $wallet=Wallet::where('user_id',auth()->id())->first();
        return view('home',compact('wallet'));
    }
}
