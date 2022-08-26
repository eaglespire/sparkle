@extends('userlayout')

@section('content')
<!-- Page content -->
<div class="container-fluid mt--6">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12">
        <div class="nav-wrapper2">
          <ul class="nav nav-line-tabs nav-line-tabs-2x nav-stretch nav-trans b-b" id="tabs-icons-text" role="tablist">
            <li class="nav-item">
              <a class="nav-link mb-sm-3 mb-md-0 @if(route('user.profile')==url()->current()) active @endif" id="tabs-icons-text-1-tab" href="{{route('user.profile')}}" role="tab" aria-controls="tabs-icons-text-1" aria-selected="true">{{__('Profile')}}</a>
            </li>
            <li class="nav-item">
              <a class="nav-link mb-sm-3 mb-md-0 @if(route('user.security')==url()->current()) active @endif" id="tabs-icons-text-2-tab" href="{{route('user.security')}}" role="tab" aria-controls="tabs-icons-text-2" aria-selected="false">{{__('2fa & Password')}}</a>
            </li>
            <li class="nav-item">
              <a class="nav-link mb-sm-3 mb-md-0 @if(route('user.api')==url()->current()) active @endif" id="tabs-icons-text-3-tab" href="{{route('user.api')}}" role="tab" aria-controls="tabs-icons-text-3" aria-selected="false">{{__('API Keys & Webhooks')}}</a>
            </li>
            <li class="nav-item">
              <a class="nav-link mb-sm-3 mb-md-0 @if(route('user.beneficiary')==url()->current()) active @endif" id="tabs-icons-text-4-tab" href="{{route('user.beneficiary')}}" role="tab" aria-controls="tabs-icons-text-4" aria-selected="false">{{__('Beneficiary')}}</a>
            </li>
          </ul>
        </div>
        <div class="tab-content" id="myTabContent">
          <div class="tab-pane fade @if(route('user.profile')==url()->current())show active @endif" id="tabs-icons-text-1" role="tabpanel" aria-labelledby="tabs-icons-text-1-tab">
            <div class="row mt-3">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-body">
                    <form action="{{route('user.account')}}" method="post">
                      @csrf
                      <div class="form-group row">
                        <label class="col-form-label col-lg-2">{{__('Full Name')}}</label>
                        <div class="col-lg-10">
                          <div class="row">
                            <div class="col-6">
                              <input type="text" name="first_name" readonly class="form-control" placeholder="First Name" value="{{$user->first_name}}">
                            </div>
                            <div class="col-6">
                              <input type="text" name="last_name" readonly class="form-control" placeholder="Last Name" value="{{$user->last_name}}">
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label class="col-form-label col-lg-2">{{__('Email address')}}</label>
                        <div class="col-lg-10">
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <span class="input-group-text"><i class="fal fa-envelope"></i></span>
                            </div>
                            <input type="email" name="email" class="form-control" placeholder="{{__('Email Address')}}" value="{{$user->email}}">
                          </div>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label class="col-form-label col-lg-2">{{__('Who pays charges')}}</label>
                        <div class="col-lg-10">
                          <select class="form-control select" name="charges" required>
                            <option value="0" @if($user->business()->charges==0) selected @endif>Charge me for the transaction fees</option>
                            <option value="1" @if($user->business()->charges==1) selected @endif>Make customers pay the transaction fees</option>
                          </select>
                        </div>
                      </div>
                      @if($set->language==1)
                      <div class="form-group row">
                        <label class="col-form-label col-lg-2">{{__('Language')}}</label>
                        <div class="col-lg-10">
                          <select class="form-control select" name="language" required>
                            <option value="">Select a Language...</option>
                            <option value="dk" @if($user->language=="dk") selected @endif>Danish</option>
                            <option value="de" @if($user->language=="de") selected @endif>German</option>
                            <option value="en" @if($user->language=="en") selected @endif>English</option>
                            <option value="es" @if($user->language=="es") selected @endif>Spanish</option>
                            <option value="fr" @if($user->language=="fr") selected @endif>French</option>
                            <option value="lv" @if($user->language=="lv") selected @endif>Latvia</option>
                            <option value="lt" @if($user->language=="lt") selected @endif>Lithuania</option>
                            <option value="ee" @if($user->language=="ee") selected @endif>Estonia</option>
                            <option value="hu" @if($user->language=="hu") selected @endif>Hungarian</option>
                            <option value="nl" @if($user->language=="nl") selected @endif>Dutch</option>
                            <option value="pl" @if($user->language=="pl") selected @endif>Polish</option>
                            <option value="ro" @if($user->language=="ro") selected @endif>Romanian</option>
                            <option value="fi" @if($user->language=="fi") selected @endif>Finnish</option>
                            <option value="se" @if($user->language=="se") selected @endif>Swedish</option>
                            <option value="sl" @if($user->language=="sl") selected @endif>Slovenia</option>
                          </select>
                        </div>
                      </div>
                      @endif
                      <div class="form-group row" id="payment_method">
                        <label class="col-form-label col-lg-12">{{__('Payment Methods')}}<span class="text-danger">*</span></label>
                        <div class="col-lg-12">
                          @if(count(getAcceptedCountryCard())>0)
                          <div class="custom-control custom-control-alternative custom-checkbox">
                            <input type="checkbox" name="card" id=" customCheckLogin5" class="custom-control-input" value="1" @if($user->business()->card==1) checked @endif>
                            <label class="custom-control-label" for=" customCheckLogin5">
                              <span class="text-dark">{{__('Card')}} [@foreach(getAcceptedCountryCard() as $val)'{{$val->real->currency}}'@if(!$loop->last),@endif @endforeach]</span>
                            </label>
                          </div><br>
                          @endif
                          @if(count(getAcceptedCountryBank())>0)
                          <div class="custom-control custom-control-alternative custom-checkbox">
                            <input type="checkbox" name="bank_account" id="customCheckLogin6" class="custom-control-input" value="1" @if($user->business()->bank_account==1) checked @endif>
                            <label class="custom-control-label" for="customCheckLogin6">
                              <span class="text-dark">{{__('Bank account')}} [@foreach(getAcceptedCountryBank() as $val)'{{$val->real->currency}}'@if(!$loop->last),@endif @endforeach]</span>
                            </label>
                          </div><br>
                          @endif
                          @if(count(getAcceptedCountryMobileMoney())>0)
                          <div class="custom-control custom-control-alternative custom-checkbox">
                            <input type="checkbox" name="mobile_money" id=" customCheckLogin7" class="custom-control-input" value="1" @if($user->business()->mobile_money==1) checked @endif>
                            <label class="custom-control-label" for=" customCheckLogin7">
                              <span class="text-dark">{{__('Mobile Money')}} [@foreach(getAcceptedCountryMobileMoney() as $val)'{{$val->real->currency}}'@if(!$loop->last),@endif @endforeach]</span>
                            </label>
                          </div><br>
                          @endif
                        </div>
                      </div>
                      <div class="form-group row" id="payment_method">
                        <label class="col-form-label col-lg-12">{{__('Payment Email Notification')}}</label>
                        <div class="col-lg-12">
                          <div class="custom-control custom-control-alternative custom-checkbox">
                            <input type="checkbox" name="email_receiver" id=" customCheckLogint" class="custom-control-input" value="1" @if($user->business()->email_receiver==1) checked @endif>
                            <label class="custom-control-label" for=" customCheckLogint">
                              <span class="text-dark">{{__('Send me notification')}}</span>
                            </label>
                          </div><br>
                          <div class="custom-control custom-control-alternative custom-checkbox">
                            <input type="checkbox" name="email_sender" id="customCheckLoging" class="custom-control-input" value="1" @if($user->business()->email_sender==1) checked @endif>
                            <label class="custom-control-label" for="customCheckLoging">
                              <span class="text-dark">{{__('Send my customer payment receipt')}}</span>
                            </label>
                          </div><br>
                        </div>
                      </div>
                      <div class="text-right">
                        <button type="submit" class="btn btn-neutral">{{__('Save Changes')}}</button>
                      </div>
                    </form>
                  </div>
                </div>
                <div class="card">
                  <div class="card-header">
                    <h3 class="mb-0 font-weight-bolder">{{__('Delete Account')}}</h3>
                    <p>{{__('Closing this account means you will no longer be able to access this account on')}} {{$set->site_name}}</p>
                  </div>
                  <div class="card-footer">
                    <a data-toggle="modal" data-target="#modal-formp" href="" class="btn btn-neutral"><i class="fal fa-trash"></i> {{__('Delete Account')}}</a>
                  </div>
                </div>
              </div>
            </div>
            <div class="row justify-content-center mt-5">
              <a href="{{route('deltest')}}" class="btn btn-danger btn-sm rounded-pill"><i class="fal fa-trash"></i> {{__('Delete Test Data')}}</a>
            </div>
            <div class="modal fade" id="modal-formp" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h3 class="mb-0 fw-bold">{{__('Delete Account')}}</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <form action="{{route('delaccount')}}" method="post">
                      @csrf
                      <div class="form-group row">
                        <div class="col-lg-12">
                          <textarea type="text" name="reason" class="form-control" rows="5" placeholder="{{__('Sorry to see you leave, Please tell us why you are leaving')}}" required></textarea>
                        </div>
                      </div>
                      <div class="text-right">
                        <button type="submit" class="btn btn-danger btn-block">{{__('Delete account')}}</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="tab-pane fade @if(route('user.security')==url()->current())show active @endif" id="tabs-icons-text-2" role="tabpanel" aria-labelledby="tabs-icons-text-2-tab">
            <div class="card mt-3">
              <div class="card-body">
                <form action="{{route('change.password')}}" method="post">
                  @csrf
                  <div class="form-group row">
                    <div class="col-lg-12">
                      <input type="password" name="password" class="form-control" placeholder="{{__('Current password')}}" required>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-lg-12">
                      <input type="password" name="new_password" id="new_password" class="form-control" placeholder="{{__('New password')}}" required>
                      <span class="error form-error-msg"><span id="result"></span></span>

                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-lg-12">
                      <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="{{__('Confirm password')}}" required>
                      <span class="error form-error-msg" id="msg"></span>
                    </div>
                  </div>
                  <h4 class="mb-0 text-dark font-weight-bolder">{{__('Password requirements')}}</h4>
                  <p class="mb-2 text-default text-sm">{{__('Ensure that these requirements are met')}}</p>
                  <ul class="text-default text-sm">
                    <li>{{__('Minimum 8 characters long - the more, the better')}}</li>
                    <li>{{__('At least one lowercase character.')}}</li>
                    <li>{{__('At least one uppercase character.')}}</li>
                    <li>{{__('At least one number, symbol, or whitespace character.')}}</li>
                  </ul>
                  <div class="text-right">
                    <button type="submit" class="btn btn-neutral">{{__('Change Password')}}</button>
                  </div>
                </form>
              </div>
            </div>
            <div class="card">
              <div class="card-header">
                <h3 class="mb-0 font-weight-bolder">{{__('Two-Factor Security Option')}}</h3>
              </div>
              <div class="card-body">
                <div class="align-item-sm-center flex-sm-nowrap text-left">
                  <p class="text-sm mb-2">
                    {{__('Two-factor authentication is a method for protection your web account. 
                        When it is activated you need to enter not only your password, but also a special code. 
                        You can receive this code by in mobile app. 
                        Even if third person will find your password, then cant access with that code.')}}
                  </p>
                  <span class="badge badge-pill badge-primary mb-3">
                    @if($user->fa_status==0)
                    {{__('Disabled')}}
                    @else
                    {{__('Active')}}
                    @endif
                  </span>
                  <ul class="text-default text-sm">
                    <li>{{__('Install an authentication app on your device. Any app that supports the Time-based One-Time Password (TOTP) protocol should work.')}}</li>
                    <li>{{__('Use the authenticator app to scan the barcode below.')}}</li>
                    <li>{{__('Enter the code generated by the authenticator app.')}}</li>
                  </ul>
                  <a data-toggle="modal" data-target="#modal-form2fa" href="" class="btn btn-neutral">
                    @if($user->fa_status==0)
                    {{__('Enable 2fa')}}
                    @elseif($user->fa_status==1)
                    {{__('Disable 2fa')}}
                    @endif
                  </a>
                  <div class="modal fade" id="modal-form2fa" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                      <div class="modal-content">
                        <div class="modal-body text-center">
                          @if($user->fa_status==0)
                          <img src="{{$image}}" class="mb-3 user-profile">
                          @endif
                          <form action="{{route('change.2fa')}}" method="post">
                            @csrf
                            <div class="form-group row">
                              <div class="col-lg-12">
                                <input type="text" pattern="\d*" name="code" class="form-control" minlength="6" maxlength="6" placeholder="Six digit code" required>
                                <input type="hidden" name="vv" value="{{$secret}}">
                                @if($user->fa_status==0)
                                <input type="hidden" name="type" value="1">
                                @elseif($user->fa_status==1)
                                <input type="hidden" name="type" value="0">
                                @endif
                              </div>
                            </div>
                            <div class="text-right">
                              <button type="submit" class="btn btn-neutral btn-block">
                                @if($user->fa_status==0)
                                {{__('Enable 2fa')}}
                                @elseif($user->fa_status==1)
                                {{__('Disable 2fa')}}
                                @endif
                              </button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="tab-pane fade @if(route('user.api')==url()->current())show active @endif" id="tabs-icons-text-3" role="tabpanel" aria-labelledby="tabs-icons-text-3-tab">
            <div class="row mt-3">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-header header-elements-inline">
                    <div class="row">
                      <div class="col-12 text-right">
                        <a href="{{route('generateapi')}}" class="btn btn-neutral"><i class="fal fa-sync"></i> {{__('Generate new keys')}}</a>
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    @if($user->business()->live==1)
                    <div class="form-group row">
                      <label class="col-form-label col-lg-2">{{__('Public key')}}</label>
                      <div class="col-lg-10">
                        <div class="input-group">
                          <input type="text" name="public_key" disabled class="form-control" placeholder="Public key" value="{{$user->business()->public_key}}">
                          <div class="input-group-append">
                            <span class="input-group-text castro-copy" data-clipboard-text="{{$user->business()->public_key}}" title="Copy to clipboard"><i class="fal fa-copy"></i></span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label class="col-form-label col-lg-2">{{__('Secret key')}}</label>
                      <div class="col-lg-10">
                        <div class="input-group">
                          <input type="password" name="secret_key" disabled class="form-control" placeholder="Secret key" value="{{$user->business()->secret_key}}">
                          <div class="input-group-append">
                            <span class="input-group-text castro-copy" data-clipboard-text="{{$user->business()->secret_key}}" title="Copy to clipboard"><i class="fal fa-copy"></i></span>
                          </div>
                        </div>
                      </div>
                    </div>
                    @else
                    <div class="form-group row">
                      <label class="col-form-label col-lg-2">{{__('Test Public key')}}</label>
                      <div class="col-lg-10">
                        <div class="input-group">
                          <input type="text" name="public_key" disabled class="form-control" placeholder="Public key" value="{{$user->business()->test_public_key}}">
                          <div class="input-group-append">
                            <span class="input-group-text castro-copy" data-clipboard-text="{{$user->business()->test_public_key}}" title="Copy to clipboard"><i class="fal fa-copy"></i></span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label class="col-form-label col-lg-2">{{__('Test Secret key')}}</label>
                      <div class="col-lg-10">
                        <div class="input-group">
                          <input type="password" name="secret_key" disabled class="form-control" placeholder="Secret key" value="{{$user->business()->test_secret_key}}">
                          <div class="input-group-append">
                            <span class="input-group-text castro-copy" data-clipboard-text="{{$user->business()->test_secret_key}}" title="Copy to clipboard"><i class="fal fa-copy"></i></span>
                          </div>
                        </div>
                      </div>
                    </div>
                    @endif
                    <form action="{{route('savewebhook')}}" method="post">
                      @csrf
                      <div class="form-group row">
                        <label class="col-form-label col-lg-2">{{__('Webhook url')}}</label>
                        <div class="col-lg-10">
                          <div class="input-group">
                            <input type="url" name="webhook" class="form-control" placeholder="https://webhook.site" value="{{$user->business()->webhook}}">
                          </div>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label class="col-form-label col-lg-2">{{__('Webhook secret')}}</label>
                        <div class="col-lg-10">
                          <div class="input-group">
                            <input type="text" name="webhook_secret" class="form-control" placeholder="" value="{{$user->business()->webhook_secret}}">
                          </div>
                        </div>
                      </div>
                      <div class="form-group row">
                        <div class="col-lg-12">
                          <div class="custom-control custom-control-alternative custom-checkbox">
                            <input type="checkbox" name="receive_webhook" id="customCheckLoginr8" class="custom-control-input" value="1" @if($user->business()->receive_webhook==1)checked @endif>
                            <label class="custom-control-label" for="customCheckLoginr8">
                              <span class="text-muted">{{__('Receive Webhook Notifications')}}</span>
                            </label>
                          </div>
                        </div>
                      </div>
                      <div class="text-left">
                        <button type="submit" class="btn btn-neutral">{{__('Update Settings')}}</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="tab-pane fade @if(route('user.beneficiary')==url()->current())show active @endif" id="tabs-icons-text-4" role="tabpanel" aria-labelledby="tabs-icons-text-4-tab">
            @if(count($user->getBeneficiary())>0)
            @foreach($user->getBeneficiary() as $val)
            <div class="card mt-3">
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col-md-9">
                    <h2 class="mb-0 font-weight-bolder text-dark">{{$val->name}}</h2>
                    @if($val->getCurrency()->bank_format=="us")
                    <p>Routing Number - {{$val->routing_no}}</p>
                    @elseif($val->getCurrency()->bank_format=="eur")
                    <p>Iban - {{$val->iban}}</p>
                    @elseif($val->getCurrency()->bank_format=="uk")
                    <p>Account Holder - {{$val->acct_no}}</p>
                    <p>Sort Code - {{$val->sort_code}}</p>
                    @elseif($val->getCurrency()->bank_format=="normal")
                    <p>Bank - {{getBankFirst($val->bank_name)->name}}</p>
                    <p>Account Number - {{$val->acct_no}}</p>
                    <p>Account Holder - {{$val->acct_name}}</p>
                    @endif
                  </div>
                  <div class="col-md-3 text-right">
                    <a data-toggle="modal" data-target="#delete{{$val->id}}" href="" class="btn btn-neutral">{{__('Delete')}}</a>
                  </div>
                  <div class="modal fade" id="delete{{$val->id}}" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h3 class="mb-0 font-weight-bolder">{{__('Delete Beneficiary')}}</h3>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <div class="modal-body">
                          <p>{{__('Are you sure you want to delete this?')}}</p>
                        </div>
                        <div class="modal-footer">
                          <a href="{{route('user.beneficiary.delete', ['id'=>$val->id])}}" class="btn btn-danger btn-block">{{__('Proceed')}}</a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            @endforeach
            @else
            <div class="col-md-12 mb-5">
              <div class="text-center mt-8">
                <div class="btn-wrapper text-center mb-3">
                  <a href="javascript:void;" class="mb-3">
                    <span class=""><i class="fal fa-user fa-4x text-info"></i></span>
                  </a>
                </div>
                <h3 class="text-dark">{{__('No Beneficiary')}}</h3>
                <p class="text-dark card-text">{{__('We couldn\'t find any beneficiary to this account')}}</p>
              </div>
            </div>
            @endif
          </div>
        </div>
      </div>
    </div>
    @stop
    @section('script')
    <script>
      'use strict';
      var clipboard = new ClipboardJS('.castro-copy');

      clipboard.on('success', function(e) {
        navigator.clipboard.writeText(e.text);
        $(e.trigger)
          .attr('title', 'Copied!')
          .text('Copied!')
          .tooltip('_fixTitle')
          .tooltip('show')
          .attr('title', 'Copy to clipboard')
          .tooltip('_fixTitle')

        e.clearSelection()
      });

      clipboard.on('error', function(e) {
        console.error('Action:', e.action);
        console.error('Trigger:', e.trigger);
      });
    </script>
    @endsection