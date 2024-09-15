@extends('layouts.app')

@section('content')

    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary" style="color: white">{{ __('Top up Payment') }}</div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <form id='checkout-form' method='post' action="{{ route('wallet.topup.payment') }}">
                            @csrf
                            <div class="form-group mt-4">
                                <input placeholder="Amount" class="form-control @error('amount') is-invalid @enderror"
                                       type="text" id="amount" name="amount" required>
                                @error('amount')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <input type='hidden' name='stripeToken' id='stripe-token-id'>
                            <br>
                            <div id="card-element" class="form-control"></div>
                            <button
                                id='pay-btn'
                                class="btn btn-success mt-3"
                                type="button"
                                style="margin-top: 20px; width: 100%;padding: 7px;"
                                onclick="createToken()">Pay
                            </button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

        @endsection
        @push('js')
            <script src="https://js.stripe.com/v3/"></script>
            <script type="text/javascript">

                let stripe = Stripe('{{ env('STRIPE_KEY') }}')
                let elements = stripe.elements();
                let cardElement = elements.create('card');
                cardElement.mount('#card-element');

                /*------------------------------------------
                --------------------------------------------
                Create Token Code
                --------------------------------------------
                --------------------------------------------*/
                function createToken() {
                    document.getElementById("pay-btn").disabled = true;
                    stripe.createToken(cardElement).then(function (result) {

                        if (typeof result.error != 'undefined') {
                            document.getElementById("pay-btn").disabled = false;
                            alert(result.error.message);
                        }

                        /* creating token success */
                        if (typeof result.token != 'undefined') {
                            document.getElementById("stripe-token-id").value = result.token.id;
                            document.getElementById('checkout-form').submit();
                        }
                    });
                }
            </script>
    @endpush
