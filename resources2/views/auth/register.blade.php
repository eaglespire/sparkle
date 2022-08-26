@extends('auth.menu')

@section('content')
<div class="main-content">
  <!-- Header -->
  <div class="header py-5 pt-6">
    <div class="container">
      <div class="header-body text-center mb-7">
      </div>
    </div>
  </div>
  <!-- Page content -->
  <div class="container mt--8 pb-5">
    <div class="row justify-content-center">
      <div class="col-lg-7 col-md-7">
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
              <h2 class="fw-bold">{{ __('Sign Up') }}</h2>
              <p>{{$set->title}}</p>
            </div>
            <form role="form" action="{{route('submitregister')}}" id="payment-form" method="post">
              @csrf
              <div class="form-group row">
                <div class="col-lg-12">
                  <select class="form-control select" name="country" required>
                    <option value="">{{__('Select Country')}}</option>
                    @if(count(getRegisteredCountryActive())>0)
                    @foreach(getRegisteredCountryActive() as $val)
                    <option value="{{$val->id}}*{{$val->real->currency}}">{{$val->real->emoji.' '.$val->real->name}}</option>
                    @endforeach
                    @endif
                  </select>
                  @if ($errors->has('country'))
                  <span>{{$errors->first('country')}}</span>
                  @endif
                </div>
              </div>
              <div class="form-group row">
                <div class="col-lg-12">
                  <div class="row">
                    <div class="col-6">
                      <input class="form-control value=" @if(session('first_name')){{session('first_name')}}@endif" placeholder="{{__('First name')}}" type="text" name="first_name" required>
                      @if ($errors->has('first_name'))
                      <span>{{$errors->first('first_name')}}</span>
                      @endif
                    </div>
                    <div class="col-6">
                      <input class="form-control value=" @if(session('last_name')){{session('last_name')}}@endif" placeholder="{{__('Last name')}}" type="text" name="last_name" required>
                      @if ($errors->has('last_name'))
                      <span>{{$errors->first('last_name')}}</span>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <input class="form-control" placeholder="{{ __('Email')}}" value="@if(session('email')){{session('email')}}@endif" type="email" autocomplete="off" name="email" required>
                @if ($errors->has('email'))
                <span>{!!$errors->first('email')!!}</span>
                @endif
              </div>
              <div class="form-group">
                <div class="input-group">
                  <input class="form-control" placeholder="{{ __('Password') }}" id="password" data-toggle="password" type="password" autocomplete="off" name="password" required>
                  <div class="input-group-append">
                    <span class="input-group-text"><i class="fa fa-eye"></i></span>
                  </div>
                </div>
                @if ($errors->has('password'))
                <span>{{$errors->first('password')}}</span>
                @endif
              </div>
              <div class="custom-control custom-control-alternative custom-checkbox">
                <input class="custom-control-input" id=" customCheckLogin" type="checkbox" name="terms" required>
                <label class="custom-control-label" for=" customCheckLogin">
                  <span class="text-dark">I agree to our <a href="{{route('terms')}}" class="text-info">terms & conditions</a></span>
                </label>
              </div>
              @if ($errors->has('terms'))
              <span>{{$errors->first('terms')}}</span>
              @endif
              @if($set->recaptcha==1)
              {!! app('captcha')->display() !!}
              @if ($errors->has('g-recaptcha-response'))
              <span class="help-block">
                {{ $errors->first('g-recaptcha-response') }}
              </span>
              @endif
              @endif
              <div class="text-center">
                <button type="submit" id="ggglogin" class="btn btn-info btn-block my-4" id="update_password">{{__('Submit form')}}</button>
                <div class="loginSignUpSeparator"><span class="textInSeparator">OR</span></div>
                <a href="{{route('login')}}" class="btn btn-neutral btn-block">{{__('Got an account?')}}</a>
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