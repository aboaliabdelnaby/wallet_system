@extends('layouts.app')

@section('content')

    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary" style="color: white">{{ __('Transfer') }}</div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <form id='checkout-form' method='post' action="{{ route('wallet.transfer.payment') }}">
                            @csrf
                            <div class="form-group mt-4">
                                <input placeholder="Amount" class="form-control @error('amount') is-invalid @enderror"
                                       type="text" id="amount" name="amount" required>
                                @error('amount')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <div class="form-group mt-4">
                                    <input type="text" name="phone" class="form-control" id="phone" placeholder="Phone">
                                    <button type="button" class="btn btn-primary mt-2 mb-2" id="search">search</button>
                                    <div id="user_data">

                                    </div>
                                </div>
                            </div>
                            <button class="btn btn-primary mt-2" id="transfer" disabled>transfer</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('js')
    <script>
        $(function (){
            $('#search').on('click',function (){
                let phone=$('#phone').val();
                $.ajax({
                    url: "{{ route('wallet.check_phone') }}",
                    type: 'POST',
                    dataType: "json",
                    data: {
                        phone: phone,
                        '_token':'{{ csrf_token() }}'
                    },
                    success: function(data) {
                       if(data.success){
                           $('#user_data').html("<span>"+data.data.name+" </span><span> "+data.data.email+"</span>")
                           $('#transfer').removeAttr('disabled')
                       }else{
                           $('#user_data').html("<span style='color:red'>"+data.error+"</span>")
                           $('#transfer').attr('disabled','true')
                       }
                    }
                });
            })
        })
    </script>
@endpush
