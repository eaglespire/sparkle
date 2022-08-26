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
            <label class="col-form-label col-lg-2">{{__('Business Type')}}</label>
            <div class="col-lg-10">
              <select class="form-control select" name="type" id="type" @if($user->business()->type!=null) disabled @endif required>
                <option value="1" @if($user->business()->type==1) selected @endif>Unregistered</option>
                <option value="2" @if($user->business()->type==2) selected @endif>Registered</option>
              </select>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-form-label col-lg-2">{{__('Industry')}}</label>
            <div class="col-lg-10">
              <select class="form-control select" name="industry" @if($user->business()->industry!=null) disabled @endif id="industry" required>
              </select>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-form-label col-lg-2">{{__('Category')}}</label>
            <div class="col-lg-10">
              <select class="form-control select" name="category" @if($user->business()->category!=null) disabled @endif id="category" required>
              </select>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-form-label col-lg-2">{{__('Staff Size')}}</label>
            <div class="col-lg-10">
              <select class="form-control select" name="staff_size" @if($user->business()->staff!=null) disabled @endif required>
                <option value="1-5" @if($user->business()->staff=="1-5") selected @endif>1-5 people</option>
                <option value="5-50" @if($user->business()->staff=="5-50") selected @endif>5-50 people</option>
                <option value="50+" @if($user->business()->staff=="50+") selected @endif>50+ people</option>
              </select>
            </div>
          </div>
          <div id="registered" style="display:none;">
            <div class="row mb-2">
              <label class="col-form-label col-lg-2">{{__('Legal Business Name')}}</label>
              <div class="col-lg-10">
                <input type="text" name="legal_name" id="legal_name" class="form-control" @if($user->business()->legal_name!=null) disabled @endif value="{{$user->business()->legal_name}}">
              </div>
            </div>
            <div class="row mb-2">
              <label class="col-form-label col-lg-2">{{__('Tax ID')}}</label>
              <div class="col-lg-10">
                <input type="text" name="tax_id" id="tax_id" class="form-control" @if($user->business()->tax_id!=null) disabled @endif value="{{$user->business()->tax_id}}">
              </div>
            </div>
            <div class="row mb-2">
              <label class="col-form-label col-lg-2">{{__('Vat ID')}}</label>
              <div class="col-lg-10">
                <input type="text" name="vat_id" id="vat_id" class="form-control" @if($user->business()->vat_id!=null) disabled @endif value="{{$user->business()->vat_id}}">
              </div>
            </div>
            <div class="row mb-2">
              <label class="col-form-label col-lg-2">{{__('Registration No')}}</label>
              <div class="col-lg-10">
                <input type="text" name="reg_no" id="reg_no" class="form-control" @if($user->business()->reg_no!=null) disabled @endif value="{{$user->business()->reg_no}}">
              </div>
            </div>
            <div class="row mb-2">
              <label class="col-form-label col-lg-2">{{__('Registration Type')}}</label>
              <div class="col-lg-10">
                <select class="form-control select" name="registration_type" id="registration_type" @if($user->business()->registration_type!=null) disabled @endif>
                  <option value="government_instrumentality" @if($user->business()->registration_type=="government_instrumentality") selected @endif>government instrumentality</option>
                  <option value="governmental_unit" @if($user->business()->registration_type=="governmental_unit") selected @endif>governmental unit</option>
                  <option value="incorporated_non_profit" @if($user->business()->registration_type=="incorporated_non_profit") selected @endif>incorporated non profit</option>
                  <option value="limited_liability_partnership" @if($user->business()->registration_type=="limited_liability_partnership") selected @endif>limited liability partnership</option>
                  <option value="multi_member_llc" @if($user->business()->registration_type=="multi_member_llc") selected @endif>multi member llc</option>
                  <option value="private_company" @if($user->business()->registration_type=="private_company") selected @endif>private company</option>
                  <option value="private_corporation" @if($user->business()->registration_type=="private_corporation") selected @endif>private corporation</option>
                  <option value="private_partnership" @if($user->business()->registration_type=="private_partnership") selected @endif>private partnership</option>
                  <option value="public_company" @if($user->business()->registration_type=="public_company") selected @endif>public company</option>
                  <option value="public_corporation" @if($user->business()->registration_type=="public_corporation") selected @endif>public corporation</option>
                  <option value="public_partnership" @if($user->business()->registration_type=="public_partnership") selected @endif>public partnership</option>
                  <option value="single_member_llc" @if($user->business()->registration_type=="single_member_llc") selected @endif>single member llc</option>
                  <option value="sole_proprietorship" @if($user->business()->registration_type=="sole_proprietorship") selected @endif>sole proprietorship</option>
                  <option value="tax_exempt_government_instrumentality" @if($user->business()->registration_type=="tax_exempt_government_instrumentality") selected @endif>tax exempt government instrumentality</option>
                  <option value="unincorporated_association" @if($user->business()->registration_type=="unincorporated_association") selected @endif>unincorporated association</option>
                  <option value="unincorporated_non_profit" @if($user->business()->registration_type=="unincorporated_non_profit") selected @endif>unincorporated non profit</option>
                </select>
              </div>
            </div>
            <div class="row mb-2">
              <label class="col-lg-2 col-form-label fs-6">{{__('Proof of Registration')}}</label>
              <div class="col-lg-10">
                <div class="row">
                  <div class="col-lg-12">
                    <div class="custom-file">
                      <input type="file" class="form-control" name="business_document" id="business_document">
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row mb-2">
              <label class="col-lg-2 col-form-label required fs-6">{{__('Business Address')}}</label>
              <div class="col-lg-10">
                <div class="row">
                  <div class="col-lg-12">
                    <input type="text" name="business_line_1" id="business_line_1" required class="form-control mb-2" placeholder="Line 1" value="{{$user->business()->business_line_1}}">
                  </div>
                  <div class="col-lg-12">
                    <input type="text" name="business_line_2" id="business_line_2" class="form-control mb-2" placeholder="Line 2 (Optional)" value="{{$user->business()->business_line_2}}">
                  </div>
                  <div class="col-lg-12 mb-2">
                    <select class="form-control" id="business_state" name="business_state">
                      <option value="">{{__('Select your state/county')}}</option>
                      @foreach($user->getState() as $val)
                      <option value="{{$val->id}}*{{$val->iso2}}">{{$val->name}}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-lg-12 mb-2" id="showBusinessState" style="display:none;">
                    <select class="form-control" id="business_city" name="city">
                    </select>
                  </div>
                  <div class="col-lg-12">
                    <input type="text" name="business_postal_code" id="business_postal_code" class="form-control mb-2" value="{{$user->business()->business_postal_code}}" placeholder="Postal code">
                  </div>
                </div>
              </div>
            </div>
            <div class="row mb-2">
              <label class="col-lg-2 col-form-label fs-6">{{__('Proof of address')}}</label>
              <div class="col-lg-10">
                <div class="custom-file">
                  <input type="file" class="form-control mb-2" name="business_proof_of_address" id="business_proof_of_address">
                  <span class="">{{__('The document must show your business address')}}</span><br>
                </div>
              </div>
            </div>
          </div>
          <div class="card">
            <div class="card-body">
              <p>{{__('Owner information')}}</p>
              <div class="row mb-2">
                <label class="col-lg-2 col-form-label fs-6">
                  <span class="required">{{__('Gender')}}</span>
                </label>
                <div class="col-lg-10">
                  <select class="form-control" required @if($user->business()->gender!=null) disabled @endif name="gender">
                    <option value="male" @if($user->business()->gender=="male") selected @endif>{{__('Male')}}</option>
                    <option value="female" @if($user->business()->gender=="female") selected @endif>{{__('Female')}}</option>
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
                      <select class="form-control" required @if($user->business()->b_month!=null) disabled @endif name="b_month" required>
                        <option value="1" @if($user->business()->b_month==1) selected @endif>Jan</option>
                        <option value="2" @if($user->business()->b_month==2) selected @endif>Feb</option>
                        <option value="3" @if($user->business()->b_month==3) selected @endif>Mar</option>
                        <option value="4" @if($user->business()->b_month==4) selected @endif>Apr</option>
                        <option value="5" @if($user->business()->b_month==5) selected @endif>May</option>
                        <option value="6" @if($user->business()->b_month==6) selected @endif>Jun</option>
                        <option value="7" @if($user->business()->b_month==7) selected @endif>Jul</option>
                        <option value="8" @if($user->business()->b_month==8) selected @endif>Aug</option>
                        <option value="9" @if($user->business()->b_month==9) selected @endif>Sep</option>
                        <option value="10" @if($user->business()->b_month==10) selected @endif>Oct</option>
                        <option value="11" @if($user->business()->b_month==11) selected @endif>Nov</option>
                        <option value="12" @if($user->business()->b_month==12) selected @endif>Dec</option>
                      </select>
                    </div>
                    <div class="col-lg-4">
                      <select class="form-control" required @if($user->business()->b_day!=null) disabled @endif name="b_day">
                        <option value="">{{ __('Day') }}</option>
                        @for($i=1; $i<=31; $i++) <option value="{{$i}}" @if($user->business()->b_day==$i){{ __('selected') }} @endif>{{$i}}</option>
                          $i++
                          @endfor
                      </select>
                    </div>
                    <div class="col-lg-4">
                      <input type="text" class="form-control" name="b_year" required @if($user->business()->b_year!=null) disabled @endif class="form-control" placeholder="Year" min="1950" max="{{date('Y')}}" value="{{$user->b_year}}">
                    </div>
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-lg-2 col-form-label required fs-6">{{__('Address')}}</label>
                <div class="col-lg-10">
                  <div class="row">
                    <div class="col-lg-12">
                      <input type="text" name="line_1" required class="form-control mb-2" placeholder="Line 1" value="{{$user->business()->line_1}}">
                    </div>
                    <div class="col-lg-12">
                      <input type="text" name="line_2" class="form-control mb-2" placeholder="Line 2 (Optional)" value="{{$user->business()->line_2}}">
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
                      <input type="text" name="postal_code" required class="form-control mb-2" value="{{$user->business()->postal_code}}" placeholder="Postal code">
                    </div>
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-lg-2 col-form-label fs-6">{{__('Proof of address')}}</label>
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
  populateIndustry("industry", "category");
</script>
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
  function businessaddresschange() {
    var selectedState = $("#business_state").find(":selected").val();
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
          $('#showBusinessState').hide();
          $('#businesscity').removeAttr('required', '');
        } else {
          $('#showBusinessState').show();
          $('#business_city').html(response);
          $('#business_city').attr('required', '');
        }
      },
      error: function(err) {
        console.log(err)
      }
    });
  }
  $("#business_state").change(businessaddresschange);
</script>
<script>
  function ben() {
    var type= $("#type").find(":selected").val();
    if(type==1){
      $('#registered').hide();
      $("#business_postal_code").removeAttr('required', '');
      $("#business_city").removeAttr('required', '');
      $("#business_state").removeAttr('required', '');
      $("#business_line_2").removeAttr('required', '');
      $("#business_line_1").removeAttr('required', '');
      $("#business_document").removeAttr('required', '');
      $("#registration_type").removeAttr('required', '');
      $("#reg_no").removeAttr('required', '');
      $("#vat_id").removeAttr('required', '');
      $("#tax_id").removeAttr('required', '');
      $("#legal_name").removeAttr('required', '');
    }else if(type==2){
      $('#registered').show();
      $("#business_postal_code").attr('required', '');
      $("#business_city").attr('required', '');
      $("#business_state").attr('required', '');
      $("#business_line_2").attr('required', '');
      $("#business_line_1").attr('required', '');
      $("#business_document").attr('required', '');
      $("#registration_type").attr('required', '');
      $("#reg_no").attr('required', '');
      $("#vat_id").attr('required', '');
      $("#tax_id").attr('required', '');
      $("#legal_name").attr('required', '');
    }
  }
  $("#type").change(ben);
  ben();
</script>
@endsection