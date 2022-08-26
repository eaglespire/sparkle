@extends('master')
@section('content')
<div class="container-fluid mt--6">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.settings.update')}}" method="post">
                            @csrf
                            <div class="form-group row">
                                <label class="col-form-label col-lg-2">{{__('Website name')}}</label>
                                <div class="col-lg-10">
                                    <input type="text" name="site_name" maxlength="200" value="{{$set->site_name}}" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-2">{{__('Website email')}}</label>
                                <div class="col-lg-10">
                                    <input type="email" name="email" value="{{$set->email}}" class="form-control" required>
                                    <span class="form-text">Displayed on homepage</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-2">{{__('Support email')}}</label>
                                <div class="col-lg-10">
                                    <input type="email" name="support_email" value="{{$set->support_email}}" class="form-control" required>
                                    <span class="form-text">For ticket</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-2">{{__('Mobile')}}</label>
                                <div class="col-lg-10">
                                    <div class="input-group">
                                        <input type="text" name="mobile" max-length="14" value="{{$set->mobile}}" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-2">{{__('Website title')}}</label>
                                <div class="col-lg-10">
                                    <input type="text" name="title" max-length="200" value="{{$set->title}}" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-2">{{__('Short description')}}</label>
                                <div class="col-lg-10">
                                    <textarea type="text" name="site_desc" rows="4" class="form-control" required>{{$set->site_desc}}</textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-2">{{__('Livechat snippet code')}}</label>
                                <div class="col-lg-10">
                                    <textarea type="text" name="livechat" class="form-control" rows="4">{{$set->livechat}}</textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-2">{{__('Analytics snippet code')}}</label>
                                <div class="col-lg-10">
                                    <textarea type="text" name="analytic_snippet" class="form-control" rows="4">{{$set->analytic_snippet}}</textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-2">{{__('Default Website Font')}}</label>
                                <div class="col-lg-10">
                                    <select class="form-control select" name="default_font" required>
                                        <option value="HKGroteskPro" @if($set->default_font=="HKGroteskPro") selected @endif</option>{{__('HKGroteskPro')}}</option>
                                        <option value="Roboto" @if($set->default_font=="Roboto") selected @endif</option>{{__('Roboto')}}</option>
                                        <option value="STIX Two Text" @if($set->default_font=="STIX Two Text") selected @endif</option>{{__('STIX Two Text')}}</option>
                                        <option value="Atkinson Hyperlegible" @if($set->default_font=="Atkinson Hyperlegible") selected @endif</option>{{__('Atkinson Hyperlegible')}}</option>
                                        <option value="Open Sans" @if($set->default_font=="Open Sans") selected @endif</option>{{__('Open Sans')}}</option>
                                        <option value="Noto Sans JP" @if($set->default_font=="Noto Sans JP") selected @endif</option>{{__('Noto Sans JP')}}</option>
                                        <option value="Roboto Condensed" @if($set->default_font=="Roboto Condensed") selected @endif</option>{{__('Roboto Condensed')}}</option>
                                        <option value="Source Sans Pro" @if($set->default_font=="Source Sans Pro") selected @endif</option>{{__('Source Sans Pro')}}</option>
                                        <option value="Noto Sans" @if($set->default_font=="Noto Sans") selected @endif</option>{{__('Noto Sans')}}</option>
                                        <option value="PT Sans" @if($set->default_font=="PT Sans") selected @endif</option>{{__('PT Sans')}}</option>
                                        <option value="Georama" @if($set->default_font=="Georama") selected @endif>{{__('Georama')}}</option>
                                        <option value="Lato" @if($set->default_font=="Lato") selected @endif>{{__('Lato')}}</option>
                                        <option value="Montserrat" @if($set->default_font=="Montserrat") selected @endif>{{__('Montserrat')}}</option>
                                        <option value="Hahmlet" @if($set->default_font=="Hahmlet") selected @endif>{{__('Hahmlet')}}</option>
                                        <option value="Poppins" @if($set->default_font=="Poppins") selected @endif>{{__('Poppins')}}</option>
                                        <option value="Oswald" @if($set->default_font=="Oswald") selected @endif>{{__('Oswald')}}</option>
                                        <option value="Raleway" @if($set->default_font=="Raleway") selected @endif>{{__('Raleway')}}</option>
                                        <option value="Nunito" @if($set->default_font=="Nunito") selected @endif>{{__('Nunito')}}</option>
                                        <option value="Merriweather" @if($set->default_font=="Merriweather") selected @endif>{{__('Merriweather')}}</option>
                                        <option value="Ubuntu" @if($set->default_font=="Ubuntu") selected @endif>{{__('Ubuntu')}}</option>
                                        <option value="Rubik" @if($set->default_font=="Rubik") selected @endif>{{__('Rubik')}}</option>
                                        <option value="Lora" @if($set->default_font=="Lora") selected @endif>{{__('Lora')}}</option>
                                        <option value="Mukta" @if($set->default_font=="Mukta") selected @endif>{{__('Mukta')}}</option>
                                        <option value="Inter" @if($set->default_font=="Inter") selected @endif>{{__('Inter')}}</option>
                                        <option value="Quicksand" @if($set->default_font=="Quicksand") selected @endif>{{__('Quickand')}}</option>
                                        <option value="Heebo" @if($set->default_font=="Heebo") selected @endif>{{__('Karla')}}</option>
                                    </select>
                                    <span class="form-text">Not satisfied with font options, send an email to support@boomchart.net with your purchase code</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-2">{{__('Default Dashboard Color')}}</label>
                                <div class="col-lg-10">
                                    <input type="color" name="default_color" value="{{$set->default_color}}" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-2">{{__('Flutterwave public key')}}</label>
                                <div class="col-lg-10">
                                    <input type="text" name="public_key" value="{{$set->public_key}}" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-2">{{__('Flutterwave secret key')}}</label>
                                <div class="col-lg-10">
                                    <input type="text" name="secret_key" value="{{$set->secret_key}}" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-2">{{__('Flutterwave encrypt')}}</label>
                                <div class="col-lg-10">
                                    <input type="text" name="encrypt" value="{{$set->encrypt}}" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-2">{{__('Admin URL')}} </label>
                                <div class="col-lg-10">
                                    <input type="text" name="admin_url" value="{{$set->admin_url}}" class="form-control" required>
                                </div>
                            </div>
                            <div class="text-right">
                                <button type="submit" class="btn btn-primary">{{__('Save Changes')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3 class="fw-bold">{{__('Features')}}</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{route('admin.features.update')}}" method="post">
                            @csrf
                            <div class="form-group row">
                                <div class="col-lg-4">
                                    <div class="custom-control custom-control-alternative custom-checkbox">
                                        <input type="checkbox" name="email_verification" id="customCheckLogin2" class="custom-control-input" value="1" @if($set->email_verification==1)checked @endif>
                                        <label class="custom-control-label" for="customCheckLogin2">
                                            <span>{{__('Email verification')}}</span>
                                        </label>
                                    </div>
                                    <div class="custom-control custom-control-alternative custom-checkbox">
                                        <input type="checkbox" name="email_notify" id="customCheckLogin3" class="custom-control-input" value="1" @if($set->email_notify==1)checked @endif>
                                        <label class="custom-control-label" for="customCheckLogin3">
                                            <span>{{__('Email notify')}}</span>
                                        </label>
                                    </div>
                                    <div class="custom-control custom-control-alternative custom-checkbox">
                                        <input type="checkbox" name="registration" id="customCheckLogin4" class="custom-control-input" value="1" @if($set->registration==1)checked @endif>
                                        <label class="custom-control-label" for="customCheckLogin4">
                                            <span>{{__('Registration')}}</span>
                                        </label>
                                    </div>
                                    <div class="custom-control custom-control-alternative custom-checkbox">
                                        <input type="checkbox" name="recaptcha" id="customCheckLogin6" class="custom-control-input" value="1" @if($set->recaptcha==1)checked @endif>
                                        <label class="custom-control-label" for="customCheckLogin6">
                                            <span>{{__('Recaptcha')}}</span>
                                        </label>
                                    </div>
                                    <div class="custom-control custom-control-alternative custom-checkbox">
                                        <input type="checkbox" name="maintenance" id="customCheckLogin7" class="custom-control-input" value="1" @if($set->maintenance==1)checked @endif>
                                        <label class="custom-control-label" for="customCheckLogin7">
                                            <span>{{__('Maintenance mode')}}</span>
                                        </label>
                                    </div>
                                    <div class="custom-control custom-control-alternative custom-checkbox">
                                        <input type="checkbox" name="language" id="customCheckLogin8" class="custom-control-input" value="1" @if($set->language==1)checked @endif>
                                        <label class="custom-control-label" for="customCheckLogin8">
                                            <span>{{__('Language')}}</span>
                                        </label>
                                    </div>
                                    <div class="custom-control custom-control-alternative custom-checkbox">
                                        <input type="checkbox" name="preloader" id="customCheckLogin9" class="custom-control-input" value="1" @if($set->preloader==1)checked @endif>
                                        <label class="custom-control-label" for="customCheckLogin9">
                                            <span>{{__('Preloader')}}</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <button type="submit" class="btn btn-primary">{{__('Save Changes')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3 class="fw-bold">{{__('Security')}}</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{route('admin.account.update')}}" method="post">
                            @csrf
                            <div class="form-group row">
                                <label class="col-form-label col-lg-2">{{__('Username')}}</label>
                                <div class="col-lg-10">
                                    <input type="text" name="username" value="{{$val->username}}" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-2">{{__('Password')}}</label>
                                <div class="col-lg-10">
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                            </div>
                            <div class="text-right">
                                <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @stop