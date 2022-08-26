@extends('auth.menu')

@section('content')
<div class="main-content">
    <!-- Header -->
    <div class="header py-7 py-lg-8 pt-lg-9">
      <div class="container">
        <div class="header-body text-center mb-7">
          <div class="row justify-content-center">
          </div>
        </div>
      </div>
    </div>
    <!-- Page content -->
    <div class="container mt--8 pb-5 mb-0">
      <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">
          <div class="card mb-5">
            <div class="card-body pt-7 px-5">
              <div class="text-center text-dark mb-5">
                <div class="btn-wrapper text-center">
                  <a href="javascript:void;" class="btn btn-neutral btn-icon mb-3">
                      <span class="btn-inner--icon"><i class="fal fa-sad-tear fa-4x"></i></span>
                  </a>
                </div>
                <h3 class="text-uppercase">{{__('Account has been suspended')}}<h3>
                <p>{{__('Click')}}, <span class="text-muted"><a href="{{url('contact')}}">{{__('here')}}</a></span> {{__('to contact administrator')}}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
@stop