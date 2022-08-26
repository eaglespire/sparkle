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
  <div class="container mt--8 pb-5">
    <div class="row justify-content-center">
      <div class="col-md-5">
        <div class="card">
          <div class="card-header mb-0">
            <h2 class="fw-bold">{{ucwords($link->name)}}</h2>
            <p>{{$link->description}}</p>
          </div>
          <div class="card-body">
            <form action="{{ route('payment.submit', ['id'=>$link->ref_id])}}" method="post" id="payment-form">
              @csrf
              <div class="form-group">
                <select class="form-control" name="status" required>
                  <option value="">{{__('Select transaction response')}}</option>
                  <option value="1">{{__('Successful')}}</option>
                  <option value="2">{{__('Failed')}}</option>
                </select>
              </div>
              @if($link->amount==null)
              <div class="form-group">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text text-future">{{$link->getCurrency->real->currency_symbol}}</span>
                  </div>
                  <input class="form-control" max="@if($link->getCurrency->max_amount==null) @else{{$link->getCurrency->max_amount}}@endif" min="{{$link->getCurrency->min_amount}}" type="number" placeholder="0.00" onkeyup="service_charge()" id="transaction_charge" name="amount" autocomplete="off" required>
                </div>
                @if ($errors->has('amount'))
                <span class="text-xs text-uppercase mt-3">{{$errors->first('amount')}}</span>
                @endif
              </div>
              @else
              <input type="hidden" name="amount" value="{{$link->amount}}">
              @endif
              <input type="hidden" value="test" name="type">
              <input type="hidden" value="1" name="crf">
              <div class="form-group row">
                <div class="col-xs-12 col-md-12 form-group required">
                  <input type="email" class="form-control" name="email" placeholder="{{__('Email address')}}" autocomplete="off" required />
                  @if ($errors->has('email'))
                  <span>{!!$errors->first('email')!!}</span>
                  @endif
                </div>
                <div class="col form-group required">
                  <input type="text" class="form-control" name="first_name" placeholder="{{__('First name')}}" autocomplete="off" required />
                  @if ($errors->has('first_name'))
                  <span>{{$errors->first('first_name')}}</span>
                  @endif
                </div>
                <div class="col form-group required">
                  <input type="text" class="form-control" name="last_name" placeholder="{{__('Last name')}}" autocomplete="off" required />
                  @if ($errors->has('last_name'))
                  <span>{{$errors->first('last_name')}}</span>
                  @endif
                </div>
              </div>
              <div class="text-center">
                <button type="submit" id="ggglogin" class="btn btn-neutral btn-block my-4">{{__('Pay')}}
                  @if($link->user->business()->charges==1)
                  {{$link->getCurrency->real->currency}}<span id="tcr">{{number_format(($link->amount*$link->getCurrency->percent_charge/100)+($link->getCurrency->fiat_charge)+($link->amount),2)}}</span>
                  @else
                  {{$link->getCurrency->real->currency}}<span id="tcr">@if($link->amount==null)0.00 @else{{$link->amount}} @endif</span>
                  @endif
                </button>
                <span class="badge badge-pill badge-primary"><i class="fal fa-ban"></i> {{__('Test mode')}}</span></br></br>
                <p class="text-default text-xs"><i class="fal fa-lock"></i> {{__('Secured by')}} <span class="fw-bold">{{$set->site_name}}</span></p>
              </div>
            </form>
          </div>
        </div>
        <div class="row justify-content-center mt-5">
          <a href="{{route('login')}}" class="text-white"><i class="fal fa-times"></i> {{__('Cancel Payment')}}</a>
        </div>
      </div>
    </div>
  </div>
  @stop
  @section('script')
  @if($link->user->business()->charges==1)
  <script>
    "use strict";

    function service_charge() {
      var amount = $("#transaction_charge").val();
      var percent_charge = '{{$link->getCurrency->percent_charge}}';
      var fiat_charge = '{{$link->getCurrency->fiat_charge}}';
      var charge = (parseFloat(amount) * parseFloat(percent_charge) / 100) + parseFloat(fiat_charge) + parseFloat(amount);
      if (isNaN(charge) || charge < 0) {
        charge = 0;
      }
      $("#tcr").text(charge.toFixed(2));
    }
    $("#transaction_charge").change(service_charge);
  </script>
  @else
  <script>
    "use strict";

    function service_charge() {
      var amount = $("#transaction_charge").val();
      if (isNaN(amount) || amount < 0) {
        amount = 0;
      }
      $("#tcr").text(parseFloat(amount).toFixed(2));
    }
    $("#transaction_charge").change(service_charge);
  </script>
  @endif
  @endsection