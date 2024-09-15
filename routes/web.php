<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();
Route::group(['middleware' => 'auth'], function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::group(['prefix' => 'wallet'], function () {
        Route::get('topup',[WalletController::class, 'topup'])->name('wallet.topup');
        Route::post('topup/payment',[WalletController::class, 'payment'])->name('wallet.topup.payment');
        Route::get('transfer',[WalletController::class, 'transfer'])->name('wallet.transfer');
        Route::post('transfer/payment',[WalletController::class, 'transfer_payment'])->name('wallet.transfer.payment');
        Route::get('transactions/history',[WalletController::class, 'history'])->name('wallet.transactions.history');
        Route::post('check_phone',[WalletController::class, 'check_phone'])->name('wallet.check_phone');
    });
});
