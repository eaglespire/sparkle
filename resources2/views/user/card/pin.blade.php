@extends('user.link.menu')

@section('content')
<div class="main-content">
    <div class="header py-5 pt-7">
        <div class="container">
            <div class="header-body text-center mb-7">
                <div class="row justify-content-center">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container mt--8 pb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header mb-0">
                    <h3>{{__('Enter your card pin')}}</h3>
                </div>
                <div class="card-body">
                    <form action="{{route('payment.pin.submit', ['id'=>$link->ref_id])}}" method="post" id="payment-form">
                        @csrf
                        <div class="form-group">
                            <input class="form-control" placeholder="{{__('Pin')}}" pattern="[0-9]*" maxlength="4" type="text" name="pin" autocomplete="off" required>
                            @if ($errors->has('pin'))
                            <span>{!!$errors->first('pin')!!}</span>
                            @endif
                        </div>
                        <div class="text-center">
                            <button type="submit" id="ggglogin" class="btn btn-neutral btn-block my-4">{{__('Submit')}}</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row justify-content-center mt-5">
                <a href="{{route('payment.cancel', ['id'=>$link->ref_id])}}" class="text-white"><i class="fal fa-times"></i> {{__('Cancel Payment')}}</a>
            </div>
        </div>
    </div>
</div>
@stop
@section('script')
@endsection