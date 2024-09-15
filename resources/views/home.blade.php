@extends('layouts.app')

@section('content')
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">{{ __('Your Wallet') }}</div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <a class="btn btn-primary" href="{{ route('wallet.topup') }}">{{ __('Topup') }}</a>
                        <a class="btn btn-success" href="{{ route('wallet.transfer') }}">{{ __('Transfer') }}</a>
                        <a class="btn btn-secondary"
                           href="{{ route('wallet.transactions.history') }}">{{ __('Transactions History') }}</a>
                    </div>
                    <div class="col-md-4" style="font-size: 50px">{{ $wallet->balance }}</div>
                </div>
            </div>
        </div>
    </div>

@endsection
