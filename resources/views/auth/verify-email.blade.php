@extends('auth.menu')

@section('content')
<div class="main-content">
    <div class="header py-7 py-lg-8 pt-lg-1">
        <div class="container">
            <div class="header-body text-center mb-3">
                <div class="row justify-content-center">
                    <div class="col-xl-5 col-lg-6 col-md-8 px-5">
                        <h2 class="fw-bold">{{__('Verification link sent!')}}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container mt--8 pb-5 mb-0">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="card mb-5">
                    <div class="card-body pt-7 px-5">
                        <div class="text-center text-dark mb-5">
                            <div class="btn-wrapper text-center mb-3">
                                <a href="javascript:void;" class="mb-3">
                                    <span class=""><i class="fal fa-envelope fa-4x text-info"></i></span>
                                </a>
                            </div>
                            <h3>{{__('Please check your inbox!')}}</h3>
                            <p>{{__('If you have not received the verification email yet, you can resend it ')}} <a href="{{route('user.send-email')}}" class="text-info">{{__('here')}}</a></p>
                            <a href="{{route('home')}}" class="btn btn-info btn-block mt-3">{{__('Back to safety')}}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="text-center mt-3">
        <p>{{__('Contact')}} <a href="mailto:{{$set->email}}" class="text-info">{{$set->email}}</a> {{__('for any information')}}</p>
    </div>
@stop