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
                    <h3>{{__('Verify your address')}}</h3>
                </div>
                <div class="card-body">
                    <form action="{{route('payment.avs.submit', ['id'=>$link->ref_id])}}" method="post" id="payment-form">
                        @csrf
                        <div class="row">
                            <div class="col-lg-12">
                                <input type="text" name="address" required class="form-control mb-2" value="@if(session('address')){{session('address')}}@endif" placeholder="Address" value="">
                                @if ($errors->has('address'))
                                <span>{!!$errors->first('address')!!}</span>
                                @endif
                            </div>
                            <div class="col-lg-12 mb-2">
                                <select class="form-control" id="country" name="country">
                                    <option value="">{{__('Select your Country')}}</option>
                                    @foreach(getAllCountry() as $val)
                                    <option value="{{$val->id}}*{{$val->iso2}}" @if(session('country')==$val->id) selected @endif>{{$val->emoji}} {{$val->name}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('country'))
                                <span>{!!$errors->first('country')!!}</span>
                                @endif
                            </div>
                            <div class="col-lg-12 mb-2">
                                <select class="form-control" id="state" name="state" required>
                                    <option value="">{{__('Select your state/county')}}</option>
                                </select>
                                @if ($errors->has('state'))
                                <span>{!!$errors->first('state')!!}</span>
                                @endif
                            </div>
                            <div class="col-lg-12 mb-2">
                                <input type="text" name="city" required class="form-control mb-2" value="@if(session('city')){{session('city')}}@endif" placeholder="City">
                                @if ($errors->has('city'))
                                <span>{!!$errors->first('city')!!}</span>
                                @endif
                            </div>
                            <div class="col-lg-12">
                                <input type="text" name="zip_code" required class="form-control mb-2" value="@if(session('zip_code')){{session('zip_code')}}@endif" placeholder="Zipcode">
                                @if ($errors->has('zip_code'))
                                <span>{!!$errors->first('zip_code')!!}</span>
                                @endif
                            </div>
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
<script>
    function statechange() {
        var selectedCountry = $("#country").find(":selected").val();
        $.ajax({
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            url: "{{route('address.state')}}",
            data: {
                "_token": "{{ csrf_token() }}",
                country: selectedCountry
            },
            success: function(response) {
                if (response.trim() == '') {

                } else {
                    $('#state').html(response);
                }
            },
            error: function(err) {
                console.log(err)
            }
        });
    }
    $("#country").change(statechange);
    statechange();
</script>
@endsection
