@extends('auth.menu')

@section('content')
<div class="main-content">
    <!-- Header -->
    <div class="header py-7">
      <div class="container">
        <div class="header-body text-center mb-7">
          <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-8 px-5">
              <div class="card-profile-image mb-5">
                  <img src="{{url('/')}}/asset/profile/person.png" class="">
              </div>
              <h2 class="text-default fw-bold">{{__('Two Factor Authentication')}}</h2>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Page content -->
    <div class="container mt--8 pb-5 mb-0">
      <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">
          <div class="card">
            <div class="card-body pt-7 px-5">
              <form role="form" action="{{route('submitfa')}}" method="post">
                @csrf
                <div class="form-group">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fal fa-unlock"></i></span>
                    </div>
                    <input class="form-control" placeholder="{{ __('Code') }}" type="password" name="code" required>
                  </div>
                </div>
                <div class="text-center">
                  <button type="submit" class="btn btn-neutral btn-block my-4">{{__('Verify')}}</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
@stop