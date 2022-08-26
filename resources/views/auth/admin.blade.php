@extends('auth.menu')

@section('content')
<div class="main-content">
    <!-- Header -->
    <div class="header py-7 py-lg-5 pt-lg-9">
      <div class="container">
        <div class="header-body text-center mb-7">
          <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-8 px-5">
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Page content -->
    <div class="container mt--8 pb-5">
      <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">
          <div class="card mb-0">
            <div class="card-header bg-transparent pb-3">
            </div>
            <div class="card-body px-lg-5 py-lg-5">
              <div class="text-center text-dark mb-5">
                <h2 class="text-dark fw-bold">{{ __('Sign In') }}</h2>
                <p>{{ __('Welcome back, login to manage account') }}</p>
              </div>
              <form role="form" action="{{route('admin.login')}}" method="post">
                @csrf
                <div class="form-group mb-3">
                  <input class="form-control" placeholder="{{ __('Username') }}" type="text" name="username" required>
                </div>
                <div class="form-group">
                  <input class="form-control" placeholder="{{ __('Password') }}" type="password" name="password" required>
                </div>
                <div class="custom-control custom-control-alternative custom-checkbox">
                  <input class="custom-control-input" id=" customCheckLogin" type="checkbox" name="remember_me">
                  <label class="custom-control-label" for=" customCheckLogin">
                    <span class="text-dark">{{__('Remember me')}}</span>
                  </label>
                </div>                
                <div class="text-center">
                  <button type="submit" class="btn btn-neutral my-4 btn-block">LOGIN</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
@stop