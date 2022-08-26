@extends('front.menu')
@section('css')

@stop
@section('content')
<section class="effect-section">
    <div class="container">
        <div class="row full-screen align-items-center p-50px-tb lg-p-100px-t justify-content-center">
            <div class="col-lg-6 m-50px-tb md-m-20px-t">
                <p class="typed theme3rd-bg p-5px-tb p-15px-lr d-inline-block white-color border-radius-15 m-25px-b">{{$set->title}}</p>
                <h1 class="display-4 m-20px-b">{{$ui->header_title}}</h1>
                <p class="lead m-35px-b">{{$ui->header_body}}</p>
                <div class="p-20px-t m-btn-wide">
                    @if (Auth::guard('user')->check())
                    <a class="m-btn m-btn-radius m-btn-t-dark m-10px-r" href="{{route('user.dashboard')}}">
                        <span class="m-btn-inner-text">{{__('Dashboard')}}</span>
                        <span class="m-btn-inner-icon arrow"></span>
                    </a>
                    @else
                    <a class="m-btn m-btn-radius m-btn-t-dark m-10px-r" href="{{route('login')}}">
                        <span class="m-btn-inner-text">{{__('Sign In')}}</span>
                        <span class="m-btn-inner-icon arrow"></span>
                    </a>
                    <a class="m-btn m-btn-radius m-btn m-btn-theme-light" href="{{route('register')}}">
                        <span class="m-btn-inner-text">{{__('Get Started')}}</span>
                    </a>
                    @endif
                </div>
            </div>
            <div class="col-lg-6 m-15px-tb">
                <img class="max-width-100" src="{{asset('asset/images/'.$ui->s4_image)}}" title="" alt="">
            </div>
        </div>
    </div>
</section>
<section class="section p-0px-t section-top-up-100">
    <div class="container">
        <div class="row">
            @foreach($item as $val)
            <div class="col-sm-6 col-lg-3 m-15px-tb">
                <div class="p-25px-lr p-35px-tb white-bg box-shadow-lg hover-top border-radius-15">
                    <h5 class="m-10px-b">{{$val->title}}</h5>
                    <p class="m-0px">{{$val->details}}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
<div class="p-40px-tb border-top-1 border-bottom-1 border-color-gray">
    <div class="container">
        <div class="owl-carousel owl-loaded owl-drag" data-items="7" data-nav-dots="false" data-md-items="6" data-sm-items="5" data-xs-items="4" data-xx-items="3" data-space="30" data-autoplay="true">
            @foreach($brand as $val)
            <div class="p8">
                <img src="{{asset('asset/brands/'.$val->image)}}" title="" alt="">
            </div>
            @endforeach
        </div>
    </div>
</div>
<section class="section effect-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 m-15px-tb">
                <h6 class="theme-color m-10px-b">We are {{$set->site_name}}</h6>
                <h3 class="h1 m-20px-b">{{$ui->s8_title}}</h3>
                <p class="m-0px">{{$ui->s8_body}}</p>
                <div class="p-25px-t row">
                    <div class="col-sm-6">
                        <ul class="list-type-01">
                            <li>Card Issuing for NGN, USD</li>
                            <li>Open Banking for GBP, EUR, USD</li>
                            <li>Mobile Money</li>
                            <li>Card Payment</li>
                            <li>Multiple currencies</li>
                        </ul>
                    </div>
                    <div class="col-sm-6">
                        <ul class="list-type-01">
                            <li>HTML & API checkout</li>
                            <li>Inline Js & Plugins</li>
                            <li>Currency exchange</li>
                            <li>Payment links</li>
                            <li>Instant Checkout</li>
                            <li>Virtual account - coming soon</li>
                        </ul>
                    </div>
                </div>
                <div class="p-30px-t">
                    <a class="m-link-theme" href="{{route('developers')}}">Getting Started Docs</a>
                </div>
            </div>
            <div class="col-lg-6 m-15px-tb text-center">
                <img src="{{asset('asset/images/'.$ui->s3_image)}}" class="rounded" title="" alt="">
            </div>
        </div>
    </div>
</section>
@if(count($review)>0)
<section class="p-50px-t">
    <div class="container">
        <div class="row justify-content-between">
            <div class="col-lg-6">
                <img src="{{asset('asset/images/'.$ui->s7_image)}}" title="" alt="">
            </div>
            <div class="col-lg-6 m-15px-tb">
                <h3 class="h1">{{$ui->s3_title}}</h3>
                <p class="font-2 p-0px-t">{{$ui->s3_body}}</p>
                <div class="border-left-2 border-color-theme p-25px-l m-35px-t">
                    <h6 class="font-2">{{$set->title}}</h6>
                    <p>{{$ui->s6_title}}</p>
                </div>
                <div class="p-20px-t">
                    <a class="m-btn m-btn-radius m-btn m-btn-theme-light" href="{{route('about')}}">
                        <span class="m-btn-inner-text">More About Us</span>
                        <span class="m-btn-inner-icon arrow"></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endif
<section class="section gray-bg">
    <div class="container">
        <div class="row justify-content-center md-m-25px-b m-40px-b">
            <div class="col-lg-6 text-center">
                <h3 class="h1 m-0px">{{$ui->s6_body}}</h3>
                <div class="p-20px-t">
                    <a class="m-btn m-btn-dark m-btn-radius" href="{{route('register')}}">{{__('Sign Up for Free')}} </a>
                </div>
            </div>
        </div>
    </div>
</section>
@stop