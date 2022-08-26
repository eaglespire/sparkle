@extends('auth.menu')

@section('content')
<div class="main-content">
  <!-- Header -->
  <div class="header py-6 pt-7">
    <div class="container">
      <div class="header-body text-center mb-7">
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
              <h3 class="text-dark fw-bold">{{ __('Forgot password') }}</h3>
            </div>
            <form role="form" action="{{route('user.password.email')}}" method="post">
              @csrf
              <div class="form-group">
                <input class="form-control" placeholder="{{ __('Email address') }}" type="email" name="email" required>
              </div>
              <div class="text-center">
                <button type="submit" class="btn btn-info btn-block my-4">{{__('Reset password')}}</button>
                <div class="loginSignUpSeparator"><span class="textInSeparator">OR</span></div>
                <a href="{{route('login')}}" class="btn btn-neutral btn-block my-0">{{__('Sign In')}}</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  @stop