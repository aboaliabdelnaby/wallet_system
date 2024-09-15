@extends('layouts.app')

@section('content')

    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary" style="color: white">{{ __('history') }}</div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th scope="col">Date</th>
                                <th scope="col">Transaction Type</th>
                                <th scope="col">Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td>{{ $transaction->type }}</td>
                                    <td>{{ $transaction->type==\App\Http\Enum\TransactionTypeEnum::SENDING? '- '.$transaction->amount : '+ '. $transaction->amount }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-center">
                            {!! $transactions->links() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
