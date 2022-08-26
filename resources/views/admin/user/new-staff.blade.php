@extends('master')

@section('content')
<div class="container-fluid mt--6">
  <div class="content-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-0">
                <div class="card-body">
                    <form action="{{route('create.staff')}}" method="post">
                        @csrf
                        <div class="form-group row">
                            <div class="col-lg-12">
                                <input type="text" name="first_name" class="form-control" placeholder="First Name" required>
                            </div>
                        </div>                              
                        <div class="form-group row">
                            <div class="col-lg-12">
                                <input type="text" name="last_name" class="form-control" placeholder="Last Name" required>
                            </div>
                        </div>                            
                        <div class="form-group row">
                            <div class="col-lg-12">
                                <input type="text" name="username" class="form-control" placeholder="Username" required>
                            </div>
                        </div>                            
                        <div class="form-group row">
                            <div class="col-lg-12">
                                <input type="password" name="password" class="form-control" placeholder="Password" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-4">
                                <div class="custom-control custom-control-alternative custom-checkbox">
                                    <input type="checkbox" name="profile" id="customCheckLogin1"  class="custom-control-input" value="1">
                                    <label class="custom-control-label" for="customCheckLogin1">
                                    <span class="text-muted">{{__('customer profile')}}</span>     
                                    </label>
                                </div>                  
                            </div>                               
                            <div class="col-lg-4">
                                <div class="custom-control custom-control-alternative custom-checkbox">
                                    <input type="checkbox" name="support" id="customCheckLogin2"  class="custom-control-input" value="1">
                                    <label class="custom-control-label" for="customCheckLogin2">
                                    <span class="text-muted">{{__('support')}}</span>     
                                    </label>
                                </div>                  
                            </div>                               
                            <div class="col-lg-4">
                                <div class="custom-control custom-control-alternative custom-checkbox">
                                    <input type="checkbox" name="promo" id="customCheckLogin3"  class="custom-control-input" value="1">
                                    <label class="custom-control-label" for="customCheckLogin3">
                                    <span class="text-muted">{{__('promo')}}</span>     
                                    </label>
                                </div>                  
                            </div>                               
                            <div class="col-lg-4">
                                <div class="custom-control custom-control-alternative custom-checkbox">
                                    <input type="checkbox" name="message" id="customCheckLogin4"  class="custom-control-input" value="1">
                                    <label class="custom-control-label" for="customCheckLogin4">
                                    <span class="text-muted">{{__('message')}}</span>     
                                    </label>
                                </div>                  
                            </div>                                                      
                            <div class="col-lg-4">
                                <div class="custom-control custom-control-alternative custom-checkbox">
                                    <input type="checkbox" name="settlement" id="customCheckLogin6"  class="custom-control-input" value="1">
                                    <label class="custom-control-label" for="customCheckLogin6">
                                    <span class="text-muted">{{__('settlement')}}</span>     
                                    </label>
                                </div>                  
                            </div>                                                                                    
                            <div class="col-lg-4">
                                <div class="custom-control custom-control-alternative custom-checkbox">
                                    <input type="checkbox" name="country_supported" id="customCheckLogin11"  class="custom-control-input" value="1">
                                    <label class="custom-control-label" for="customCheckLogin11">
                                    <span class="text-muted">{{__('country_supported')}}</span>     
                                    </label>
                                </div>                  
                            </div>                             
                            <div class="col-lg-4">
                                <div class="custom-control custom-control-alternative custom-checkbox">
                                    <input type="checkbox" name="knowledge_base" id="customCheckLogin12"  class="custom-control-input" value="1">
                                    <label class="custom-control-label" for="customCheckLogin12">
                                    <span class="text-muted">{{__('knowledge_base')}}</span>     
                                    </label>
                                </div>                  
                            </div>                         
                            <div class="col-lg-4">
                                <div class="custom-control custom-control-alternative custom-checkbox">
                                    <input type="checkbox" name="language" id="customCheckLogin13"  class="custom-control-input" value="1">
                                    <label class="custom-control-label" for="customCheckLogin13">
                                    <span class="text-muted">{{__('language')}}</span>     
                                    </label>
                                </div>                  
                            </div>                         
                            <div class="col-lg-4">
                                <div class="custom-control custom-control-alternative custom-checkbox">
                                    <input type="checkbox" name="email_configuration" id="customCheckLogin14"  class="custom-control-input" value="1">
                                    <label class="custom-control-label" for="customCheckLogin14">
                                    <span class="text-muted">{{__('email_configuration')}}</span>     
                                    </label>
                                </div>                  
                            </div>                         
                            <div class="col-lg-4">
                                <div class="custom-control custom-control-alternative custom-checkbox">
                                    <input type="checkbox" name="general_settings" id="customCheckLogin15"  class="custom-control-input" value="1">
                                    <label class="custom-control-label" for="customCheckLogin15">
                                    <span class="text-muted">{{__('general_settings')}}</span>     
                                    </label>
                                </div>                  
                            </div>                         
                            <div class="col-lg-4">
                                <div class="custom-control custom-control-alternative custom-checkbox">
                                    <input type="checkbox" name="news" id="customCheckLogin16"  class="custom-control-input" value="1">
                                    <label class="custom-control-label" for="customCheckLogin16">
                                    <span class="text-muted">{{__('news')}}</span>     
                                    </label>
                                </div>                  
                            </div>                             
                            <div class="col-lg-4">
                                <div class="custom-control custom-control-alternative custom-checkbox">
                                    <input type="checkbox" name="payment" id="customCheckLogin17"  class="custom-control-input" value="1">
                                    <label class="custom-control-label" for="customCheckLogin17">
                                    <span class="text-muted">{{__('payment')}}</span>     
                                    </label>
                                </div>                  
                            </div>                             
                            <div class="col-lg-4">
                                <div class="custom-control custom-control-alternative custom-checkbox">
                                    <input type="checkbox" name="transactions" id="customCheckLogin18"  class="custom-control-input" value="1">
                                    <label class="custom-control-label" for="customCheckLogin18">
                                    <span class="text-muted">{{__('transactions')}}</span>     
                                    </label>
                                </div>                  
                            </div>                                
                            <div class="col-lg-4">
                                <div class="custom-control custom-control-alternative custom-checkbox">
                                    <input type="checkbox" name="vcard" id="customCheckLogin18g"  class="custom-control-input" value="1">
                                    <label class="custom-control-label" for="customCheckLogin18g">
                                    <span class="text-muted">{{__('Virtul card')}}</span>     
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
@stop