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
              <h2 class="fw-bold text-dark">{{ __('Add Business') }}</h2>
              <p>{{__('Set up a new business')}}</p>
            </div>
            <form role="form" action="{{route('submit.business')}}" id="payment-form" method="post">
              @csrf
              <div class="form-group mb-3">
                <input class="form-control slug_input" placeholder="{{__('Business Name')}}" type="text" name="business_name" required>
                @if ($errors->has('business_name'))
                <span class="text-xs text-uppercase">{{$errors->first('business_name')}}</span>
                @endif
              </div>
              <div class="loader" style="display: none;"><span class="spinner-border spinner-border-sm mb-2"></span></div>
              <p class="text-danger fs-14 mt-0 mb-0" id="name_exist" style="display: none;"><i class="fal fa-ban"></i> This name is already taken, try another one.</p>
              <p class="text-success fs-14 mt-0 mb-0" id="name_available" style="display: none;"><i class="fal fa-check-circle"></i> Name is available.</p>
              <div class="text-center">
                <button type="submit" id="ggglogin" disabled="disabled" class="btn btn-info btn-block my-4 register_button">{{__('Submit form')}}</button>
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
    (function($) {
      $(document).on('keyup', ".slug_input", function() {
        $('.loader').show();
        var valName = $(this).val();
        var url = "{{route('business.check')}}";
        $.post(url, {
          business_name: valName,
          "_token": "{{ csrf_token() }}"
        }, function(json) {
          if (json.st == 1) {
            $('.register_button').prop('disabled', false);
            $('.loader').hide();
            $("#name_exist").slideUp();
            $("#name_available").slideDown();
          } else {
            $('.register_button').prop('disabled', true);
            $('.loader').hide();
            $("#name_available").slideUp();
            $("#name_exist").slideDown();
          }
        }, 'json');
        return false;
      });
    })(jQuery);
  </script>
  @endsection