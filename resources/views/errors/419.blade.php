@php $title="419"; @endphp
@extends('errors.menu')

@section('content')
<div class="main-content">
    <div class="header py-7 py-lg-8 pt-lg-1">
        <div class="container">
            <div class="header-body text-center mb-3">
                <div class="row justify-content-center">
                    <div class="col-xl-5 col-lg-6 col-md-8 px-5">
                        <h2>419 Error</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container mt--8 pb-5 mb-0">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="card card-profile bg-white border-0 mb-5">
                    <div class="card-body pt-7 px-5">
                        <div class="text-center text-dark mb-5">
                            <div class="btn-wrapper text-center mb-3">
                                <a href="javascript:void;" class="mb-3">
                                    <span class=""><i class="fal fa-sad-tear fa-4x text-info"></i></span>
                                </a>
                            </div>
                            <h3>{{__('An error occured')}}</h3>
                            <p>{{__('Sorry an error occured while processing your request.')}}</p>
                            <a href="{{url()->previous()}}" class="btn btn-info btn-block mt-3">{{__('Go back')}}</a>
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