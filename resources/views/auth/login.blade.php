@extends('auth.menu')

@section('content')
<div class="main-content">
  <!-- Header -->
  <div class="header py-5 pt-7">
    <div class="container">
      <div class="header-body text-center mb-7">
      </div>
    </div>
  </div>
  <!-- Page content -->
  <div class="container mt--8 pb-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        @if($set->maintenance==1)
        <div class="card">
          <div class="card-body">
            <div class="media align-items-center">
              <div class="media-body">
                <p class="text-dark">{{__('We are currently under maintenance, please try again later')}}</p>
              </div>
            </div>
          </div>
        </div>
        @endif
        <div class="card mb-0">
          <div class="card-body px-lg-5 py-lg-5">
            <div class="text-center text-dark mb-5">
              <h2 class="fw-bold">{{__('Sign In') }}</h2>
              <p>{{__('Welcome back, login to manage account') }}</p>
            </div>
            <form role="form" action="{{route('submitlogin')}}" method="post" id="payment-form">
              @csrf
              <div class="form-group mb-3">
                <input class="form-control" placeholder="{{ __('Email') }}" type="email" name="email" required>
                @if ($errors->has('email'))
                <span class="error form-error-msg ">
                  <strong>{{ $errors->first('email') }}</strong>
                </span>
                @endif
              </div>
              <div class="form-group">
                <div class="input-group">
                  <input class="form-control" placeholder="{{ __('Password') }}" id="password" data-toggle="password" type="password" name="password" required>
                  <div class="input-group-append">
                    <span class="input-group-text"><i class="fa fa-eye"></i></span>
                  </div>
                </div>
                @if ($errors->has('password'))
                <span class="error form-error-msg ">
                  <strong>{{ $errors->first('password') }}</strong>
                </span>
                @endif
              </div>
              <div class="row mt-3 mb-3">
                <div class="col-6">
                  <div class="custom-control custom-control-alternative custom-checkbox">
                    <input class="custom-control-input" id="customCheckLogin" type="checkbox" name="remember_me">
                    <label class="custom-control-label" for="customCheckLogin">
                      <span class="text-dark">{{__('Remember me')}}</span>
                    </label>
                  </div>
                </div>
                <div class="col-6 text-right">
                  <a href="{{route('user.password.request')}}" class="text-info">{{__('Forgot password?')}}</a>
                </div>
              </div>
              @if($set->recaptcha==1)
              {!! app('captcha')->display() !!}
              @if ($errors->has('g-recaptcha-response'))
              <span class="help-block">
                {{ $errors->first('g-recaptcha-response') }}
              </span>
              @endif
              @endif
              <div class="text-center">
                <button type="submit" id="ggglogin" class="btn btn-info btn-block my-4">{{__('Login')}}</button>
                <div class="loginSignUpSeparator"><span class="textInSeparator">OR</span></div>
                <a href="{{route('register')}}" class="btn btn-neutral btn-block my-0">{{__('Create an Account')}}</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  @stop
  @section('script')
  <script>
    ! function($) {
      'use strict';
      $(function() {
        $('[data-toggle="password"]').each(function() {
          var input = $(this);
          var eye_btn = $(this).parent().find('.input-group-text');
          eye_btn.css('cursor', 'pointer').addClass('input-password-hide');
          eye_btn.on('click', function() {
            if (eye_btn.hasClass('input-password-hide')) {
              eye_btn.removeClass('input-password-hide').addClass('input-password-show');
              eye_btn.find('.fa').removeClass('fa-eye').addClass('fa-eye-slash')
              input.attr('type', 'text');
            } else {
              eye_btn.removeClass('input-password-show').addClass('input-password-hide');
              eye_btn.find('.fa').removeClass('fa-eye-slash').addClass('fa-eye')
              input.attr('type', 'password');
            }
          });
        });
      });
    }(window.jQuery);
  </script>
  @endsection