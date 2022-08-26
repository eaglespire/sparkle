@extends('userlayout')

@section('content')
<div class="container-fluid mt--6">
  <div class="content-wrapper mt-3">
    <div class="row align-items-center py-4">
      <div class="col-lg-6 col-5 text-left">
        <a href="{{route('user.dashboard')}}" class="btn btn-neutral"><i class="fal fa-caret-left"></i> {{__('Go back')}}</a>
      </div>
    </div>
    <div class="card">
      <form action="{{route('submit.compliance')}}" method="post" enctype="multipart/form-data">
        <div class="card-body">
          @csrf
          <div class="row mb-2">
            <label class="col-lg-2 col-form-label fs-6">
              <span class="required">{{__('Gender')}}</span>
            </label>
            <div class="col-lg-10">
              <select class="form-control" required @if($user->gender!=null) disabled @endif name="gender">
                <option value="male" @if($user->gender=="male") selected @endif>{{__('Male')}}</option>
                <option value="female" @if($user->gender=="female") selected @endif>{{__('Female')}}</option>
              </select>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-lg-2 col-form-label fs-6">
              <span class="required">{{__('Date of Birth')}}</span>
            </label>
            <div class="col-lg-10">
              <div class="row">
                <div class="col-lg-4">
                  <select class="form-control" required @if($user->b_month!=null) disabled @endif name="b_month" required>
                    <option value="1" @if($user->b_month==1) selected @endif>Jan</option>
                    <option value="2" @if($user->b_month==2) selected @endif>Feb</option>
                    <option value="3" @if($user->b_month==3) selected @endif>Mar</option>
                    <option value="4" @if($user->b_month==4) selected @endif>Apr</option>
                    <option value="5" @if($user->b_month==5) selected @endif>May</option>
                    <option value="6" @if($user->b_month==6) selected @endif>Jun</option>
                    <option value="7" @if($user->b_month==7) selected @endif>Jul</option>
                    <option value="8" @if($user->b_month==8) selected @endif>Aug</option>
                    <option value="9" @if($user->b_month==9) selected @endif>Sep</option>
                    <option value="10" @if($user->b_month==10) selected @endif>Oct</option>
                    <option value="11" @if($user->b_month==11) selected @endif>Nov</option>
                    <option value="12" @if($user->b_month==12) selected @endif>Dec</option>
                  </select>
                </div>
                <div class="col-lg-4">
                  <select class="form-control" required @if($user->b_day!=null) disabled @endif name="b_day">
                    <option value="">{{ __('Day') }}</option>
                    @for($i=1; $i<=31; $i++) <option value="{{$i}}" @if($user->b_day==$i){{ __('selected') }} @endif>{{$i}}</option>
                      $i++
                      @endfor
                  </select>
                </div>
                <div class="col-lg-4">
                  <input type="text" class="form-control" name="b_year" required @if($user->b_year!=null) disabled @endif class="form-control" placeholder="Year" min="1950" max="{{date('Y')}}" value="{{$user->b_year}}">
                </div>
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-lg-2 col-form-label required fs-6">{{__('Address')}}</label>
            <div class="col-lg-10">
              <div class="row">
                <div class="col-lg-12">
                  <input type="text" name="line_1" required class="form-control mb-2" placeholder="Line 1" value="{{$user->line_1}}">
                </div>
                <div class="col-lg-12">
                  <input type="text" name="line_2" class="form-control mb-2" placeholder="Line 2 (Optional)" value="{{$user->line_2}}">
                </div>
                <div class="col-lg-12 mb-2">
                  <select class="form-control" id="state" name="state" required>
                    <option value="">{{__('Select your state/county')}}</option>
                    @foreach($user->getState() as $val)
                    <option value="{{$val->id}}*{{$val->iso2}}">{{$val->name}}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-lg-12 mb-2" id="showState" style="display:none;">
                  <select class="form-control" id="city" name="city">
                  </select>
                </div>
                <div class="col-lg-12">
                  <input type="text" name="postal_code" required class="form-control mb-2" value="{{$user->postal_code}}" placeholder="Postcode">
                </div>
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-lg-2 col-form-label fs-6">{{__('Home address')}}</label>
            <div class="col-lg-10">
              <div class="custom-file">
                <input type="file" class="form-control mb-2" name="proof_of_address" required>
                <span class="">{{__('The document must show your name and address')}}</span><br>
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-lg-2 col-form-label fs-6">{{__('ID Document')}}</label>
            <div class="col-lg-10">
              <div class="row">
                <div class="col-lg-12 mb-2">
                  <select class="form-control" name="doc_type" required>
                    <option value="">{{__('Please select document to verify your identity with')}}</option>
                    <option value="Passport">{{__('Passport')}}</option>
                    <option value="Driver license">{{__('Driver license')}}</option>
                    <option value="Resident permit">{{__('Resident permit')}}</option>
                    <option value="Citizen card">{{__('Citizen card')}}</option>
                    <option value="Electoral ID">{{__('Electoral ID')}}</option>
                  </select>
                </div>
                <div class="col-lg-12">
                  <div class="custom-file">
                    <input type="file" class="form-control" name="document" required>
                    <span class="">{{__('The document must show exactly this information; legal name of person - ')}}{{$user->first_name.' '.$user->last_name}}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
          @if($user->due==0)
          <button type="submit" class="btn btn-primary"> {{__('Submit for review')}}</button>
          @elseif($user->due==1)
          <span class="badge badge-pill badge-primary"> {{__('Under Review')}}</span>
          @endif
        </div>
      </form>
    </div>
  </div>
</div>
@stop
@section('script')
<script>
  function addresschange() {
    var selectedState = $("#state").find(":selected").val();
    $.ajax({
      headers: {
        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
      },
      type: "POST",
      url: "{{route('user.address.state')}}",
      data: {
        "_token": "{{ csrf_token() }}",
        state: selectedState
      },
      success: function(response) {
        console.log(response);
        if (response.trim() == '') {
          $('#showState').hide();
          $('#city').removeAttr('required', '');
        } else {
          $('#showState').show();
          $('#city').html(response);
          $('#city').attr('required', '');
        }
      },
      error: function(err) {
        console.log(err)
      }
    });
  }
  $("#state").change(addresschange);
</script>
@endsection