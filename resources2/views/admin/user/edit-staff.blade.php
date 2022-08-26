@extends('master')

@section('content')
<div class="container-fluid mt--6">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12">
                <div class="card border-0 mb-0">
                    <div class="card-body">
                        <form action="{{route('staff.update')}}" method="post">
                            @csrf
                            <input type="hidden" name="id" value="{{$staff->id}}">
                            <div class="form-group row">
                                <div class="col-lg-12">
                                    <input type="text" name="first_name" class="form-control" placeholder="First Name" required value="{{$staff->first_name}}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-12">
                                    <input type="text" name="last_name" class="form-control" placeholder="Last Name" required value="{{$staff->last_name}}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-12">
                                    <input type="text" name="username" class="form-control" placeholder="Username" required value="{{$staff->username}}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label-castro col-lg-2">{{__('Password')}}</label>
                                <div class="col-lg-10">
                                    <a data-toggle="modal" data-target="#modal-formx" href="" class="btn btn-white btn-sm">{{__('Change password')}}</a>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-4">
                                    <div class="custom-control custom-control-alternative custom-checkbox">
                                        @if($staff->profile==1)
                                        <input type="checkbox" name="profile" id="customCheckLogin1" class="custom-control-input" value="1" checked>
                                        @else
                                        <input type="checkbox" name="profile" id="customCheckLogin1" class="custom-control-input" value="1">
                                        @endif
                                        <label class="custom-control-label" for="customCheckLogin1">
                                            <span class="text-muted">{{__('customer profile')}}</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="custom-control custom-control-alternative custom-checkbox">
                                        @if($staff->support==1)
                                        <input type="checkbox" name="support" id="customCheckLogin2" class="custom-control-input" value="1" checked>
                                        @else
                                        <input type="checkbox" name="support" id="customCheckLogin2" class="custom-control-input" value="1">
                                        @endif
                                        <label class="custom-control-label" for="customCheckLogin2">
                                            <span class="text-muted">{{__('support')}}</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="custom-control custom-control-alternative custom-checkbox">
                                        @if($staff->promo==1)
                                        <input type="checkbox" name="promo" id="customCheckLogin3" class="custom-control-input" value="1" checked>
                                        @else
                                        <input type="checkbox" name="promo" id="customCheckLogin3" class="custom-control-input" value="1">
                                        @endif
                                        <label class="custom-control-label" for="customCheckLogin3">
                                            <span class="text-muted">{{__('promo')}}</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="custom-control custom-control-alternative custom-checkbox">
                                        @if($staff->message==1)
                                        <input type="checkbox" name="message" id="customCheckLogin4" class="custom-control-input" value="1" checked>
                                        @else
                                        <input type="checkbox" name="message" id="customCheckLogin4" class="custom-control-input" value="1">
                                        @endif
                                        <label class="custom-control-label" for="customCheckLogin4">
                                            <span class="text-muted">{{__('message')}}</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="custom-control custom-control-alternative custom-checkbox">
                                        @if($staff->settlement==1)
                                        <input type="checkbox" name="settlement" id="customCheckLogin6" class="custom-control-input" value="1" checked>
                                        @else
                                        <input type="checkbox" name="settlement" id="customCheckLogin6" class="custom-control-input" value="1">
                                        @endif
                                        <label class="custom-control-label" for="customCheckLogin6">
                                            <span class="text-muted">{{__('settlement')}}</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="custom-control custom-control-alternative custom-checkbox">
                                        @if($staff->country_supported==1)
                                        <input type="checkbox" name="country_supported" id="customCheckLogin11" class="custom-control-input" value="1" checked>
                                        @else
                                        <input type="checkbox" name="country_supported" id="customCheckLogin11" class="custom-control-input" value="1">
                                        @endif
                                        <label class="custom-control-label" for="customCheckLogin11">
                                            <span class="text-muted">{{__('country_supported')}}</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="custom-control custom-control-alternative custom-checkbox">
                                        @if($staff->knowledge_base==1)
                                        <input type="checkbox" name="knowledge_base" id="customCheckLogin12" class="custom-control-input" value="1" checked>
                                        @else
                                        <input type="checkbox" name="knowledge_base" id="customCheckLogin12" class="custom-control-input" value="1">
                                        @endif
                                        <label class="custom-control-label" for="customCheckLogin12">
                                            <span class="text-muted">{{__('knowledge_base')}}</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="custom-control custom-control-alternative custom-checkbox">
                                        @if($staff->language==1)
                                        <input type="checkbox" name="language" id="customCheckLogin13" class="custom-control-input" value="1" checked>
                                        @else
                                        <input type="checkbox" name="language" id="customCheckLogin13" class="custom-control-input" value="1">
                                        @endif
                                        <label class="custom-control-label" for="customCheckLogin13">
                                            <span class="text-muted">{{__('language')}}</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="custom-control custom-control-alternative custom-checkbox">
                                        @if($staff->email_configuration==1)
                                        <input type="checkbox" name="email_configuration" id="customCheckLogin14" class="custom-control-input" value="1" checked>
                                        @else
                                        <input type="checkbox" name="email_configuration" id="customCheckLogin14" class="custom-control-input" value="1">
                                        @endif
                                        <label class="custom-control-label" for="customCheckLogin14">
                                            <span class="text-muted">{{__('email_configuration')}}</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="custom-control custom-control-alternative custom-checkbox">
                                        @if($staff->general_settings==1)
                                        <input type="checkbox" name="general_settings" id="customCheckLogin15" class="custom-control-input" value="1" checked>
                                        @else
                                        <input type="checkbox" name="general_settings" id="customCheckLogin15" class="custom-control-input" value="1">
                                        @endif
                                        <label class="custom-control-label" for="customCheckLogin15">
                                            <span class="text-muted">{{__('general_settings')}}</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="custom-control custom-control-alternative custom-checkbox">
                                        @if($staff->news==1)
                                        <input type="checkbox" name="news" id="customCheckLogin16" class="custom-control-input" value="1" checked>
                                        @else
                                        <input type="checkbox" name="news" id="customCheckLogin16" class="custom-control-input" value="1">
                                        @endif
                                        <label class="custom-control-label" for="customCheckLogin16">
                                            <span class="text-muted">{{__('news')}}</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="custom-control custom-control-alternative custom-checkbox">
                                        @if($staff->payment==1)
                                        <input type="checkbox" name="payment" id="customCheckLogin17" class="custom-control-input" value="1" checked>
                                        @else
                                        <input type="checkbox" name="payment" id="customCheckLogin17" class="custom-control-input" value="1">
                                        @endif
                                        <label class="custom-control-label" for="customCheckLogin17">
                                            <span class="text-muted">{{__('payment')}}</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="custom-control custom-control-alternative custom-checkbox">
                                        @if($staff->transactions==1)
                                        <input type="checkbox" name="transactions" id="customCheckLogin18" class="custom-control-input" value="1" checked>
                                        @else
                                        <input type="checkbox" name="transactions" id="customCheckLogin18" class="custom-control-input" value="1">
                                        @endif
                                        <label class="custom-control-label" for="customCheckLogin18">
                                            <span class="text-muted">{{__('transactions')}}</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="custom-control custom-control-alternative custom-checkbox">
                                        @if($staff->vcard==1)
                                        <input type="checkbox" name="vcard" id="customCheckLogin18g" class="custom-control-input" value="1" checked>
                                        @else
                                        <input type="checkbox" name="vcard" id="customCheckLogin18g" class="custom-control-input" value="1">
                                        @endif
                                        <label class="custom-control-label" for="customCheckLogin18">
                                            <span class="text-muted">{{__('Virtual card')}}</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <button type="submit" class="btn btn-success btn-sm">{{__('Save')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-formx" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
            <div class="modal-dialog modal- modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-body p-0">
                        <div class="card bg-white border-0 mb-0">
                            <div class="card-header header-elements-inline">
                                <h3 class="mb-0">{{__('Change Password')}}</h3>
                            </div>
                            <div class="card-body px-lg-5 py-lg-5">
                                <form action="{{route('staff.password')}}" method="post">
                                    @csrf
                                    <div class="form-group row">
                                        <label class="col-form-label col-lg-4">{{__('New Password')}}</label>
                                        <div class="col-lg-8">
                                            <input type="hidden" name="id" value="{{$staff->id}}">
                                            <input type="password" name="password" class="form-control" minlength="6" placeholder="New Password" required>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <button type="submit" class="btn btn-success btn-sm">{{__('Change Password')}}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @stop