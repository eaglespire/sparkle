@extends('front.menu')
@section('css')

@stop
@section('content')
@if(count(getRegisteredCountryActive())>0)
<section class="parallax effect-section gray-bg">
    <div class="container position-relative">
        <div class="row screen-50 align-items-center justify-content-center p-100px-t">
            <div class="col-lg-10 text-center">
                <h6 class="white-color-black font-w-500">{{$set->title}}</h6>
                <h1 class="display-4 black-color m-20px-b">{{$title}}</h1>
                <div class="form-group">
                    <select class="form-control" id="xcountry" required style="font-size:1.5rem;font-weight: 700;color:#000;">
                        @foreach(getRegisteredCountryActive() as $val)
                        <option value="{{$val->id}}">{{$val->real->emoji}} {{$val->real->name}}</option>
                        @endforeach
                    </select>
                </div>
                @php
                foreach(getRegisteredCountryActive() as $vals){
                $ccx[]=$vals->id;
                }
                @endphp
                <input type="hidden" name="list" id="list" value="{{json_encode($ccx, true)}}">
            </div>
        </div>
    </div>
</section>
@foreach(getRegisteredCountryActive() as $val)
<div id="divCountry">
    <section class="section gray-bg" id="dcountry{{$val->id}}">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 col-lg-12 m-15px-tb">
                    <div class="box-shadow-lg white-bg rounded">
                        <div class="text-center p-25px-tb border-style bottom">
                            <p class="m-0px">{{getPricing($val->real->currency)->percent_charge.'%'.'+'.getPricing($val->real->currency)->fiat_charge.$val->real->currency}} Transaction Charge</p>
                        </div>
                        <div class="text-center p-25px-tb border-style bottom">
                            <p class="m-0px">{{getPricing($val->real->currency)->withdraw_percent_charge.'%'.'+'.getPricing($val->real->currency)->withdraw_fiat_charge.$val->real->currency}} Payout Charge</p>
                        </div>
                        <div class="text-center p-25px-tb border-style bottom">
                            <p class="m-0px">{{getPricing($val->real->currency)->min_amount.$val->real->currency}}-@if(getPricing($val->real->currency)->max_amount!=null){{getPricing($val->real->currency)->max_amount.$val->real->currency}}@else Infinite @endif Transaction Limit</p>
                        </div>
                        <div class="text-center p-25px-tb border-style bottom">
                            <p class="m-0px">{{getPricing($val->real->currency)->duration}} @if(getPricing($val->real->currency)->duration>1)Days @else Day @endif For settlement</p>
                        </div>
                        <div class="text-center p-25px-tb border-style bottom">
                            <p class="m-0px">Payment Methods available - 
                                @if(getPricing($val->real->currency)->card==1) Card @endif
                                @if(getPricing($val->real->currency)->mobile_money==1), Mobile Money @endif
                                @if(getPricing($val->real->currency)->bank_account==1), Open Banking @endif
                            </p>
                        </div>
                        @if(getPricing($val->real->currency)->virtual_card==1)
                        <div class="text-center p-25px-tb border-style bottom">
                            <p class="m-0px">Virtual card issuing fee - {{getPricing($val->real->currency)->virtual_percent_charge.'%'.'+'.getPricing($val->real->currency)->virtual_fiat_charge.$val->real->currency}}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endforeach
@else
@section('content')
<section class="parallax effect-section">
    <div class="mask white-bg opacity-8"></div>
        <div class="container position-relative">
            <div class="row screen-65 align-items-center justify-content-center p-100px-tb">
                <div class="col-lg-10 text-center">
                    <h6 class="white-color-black font-w-500">{{$set->title}}</h6>
                    <h1 class="display-4 black-color m-20px-b">{{__('No country found')}}</h1>
                </div>
            </div>
        </div>
        <div id="jarallax-container-0" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%; overflow: hidden; pointer-events: none; z-index: -100;"><div style="background-size: cover; background-image: url(&quot;file:///Users/mac/Documents/Templates/themeforest-cDhKHVPF-raino-multipurpose-responsive-template/template/static/img/1600x900.jpg&quot;); position: absolute; top: 0px; left: 0px; width: 1440px; height: 420px; overflow: hidden; pointer-events: none; margin-top: 31.5px; transform: translate3d(0px, -74.5px, 0px); background-position: 50% 50%; background-repeat: no-repeat no-repeat;">
        </div>
    </div>
</section>
@endif
@stop
@section('script')
<script>
    "use strict";

    function pricing() {
      const obj = $("#list").val();
      var list = JSON.parse(obj);
      list.forEach(myFunction);

      function myFunction(value, index, array) {
        $('#dcountry' + value).hide();
      }
      var country = $("#xcountry").find(":selected").val();
      var myarr = country.split("*");
      var ans = myarr[0].split("<");
      $('#dcountry' + ans).show();
    }
    const obj = $("#list").val();
    var list = JSON.parse(obj);
    list.forEach(myFunction);

    function myFunction(value, index, array) {
      $('#dcountry' + value).hide();
    }
    var country = $("#xcountry").find(":selected").val();
    var myarr = country.split("*");
    var ans = myarr[0].split("<");
    $('#dcountry' + ans).show();
    $("#xcountry").change(pricing);
  </script>
@endsection