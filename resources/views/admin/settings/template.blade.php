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
                                <label class="col-form-label col-lg-12">{{__('Welcome message')}}</label>
                                <div class="col-lg-12">
                                    <textarea type="text" name="welcome_message" rows="3" class="form-control tinymce">{{$set->welcome_message}}</textarea>
                                </div>
                            </div>                             
                            <div class="form-group row">
                                <label class="col-form-label col-lg-12">{{__('Compliance approval')}}</label>
                                <div class="col-lg-12">
                                    <textarea type="text" name="compliance_approval" rows="3" class="form-control tinymce">{{$set->compliance_approval}}</textarea>
                                </div>
                            </div> 
                            <div class="form-group row">
                                <label class="col-form-label col-lg-12">{{__('Compliance resubmit')}}</label>
                                <div class="col-lg-12">
                                    <textarea type="text" name="compliance_resubmit" rows="3" class="form-control tinymce">{{$set->compliance_resubmit}}</textarea>
                                    <span class="form-text">Reason for decline will be provided by script, you can remove &#123;&#123;reason&#125;&#125; if you don't want reason for compliance to sent</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-12">{{__('Compliance reject')}}</label>
                                <div class="col-lg-12">
                                    <textarea type="text" name="compliance_reject" rows="3" class="form-control tinymce">{{$set->compliance_reject}}</textarea>
                                </div>
                            </div>                      
                            <div class="form-group row">
                                <label class="col-form-label col-lg-12">{{__('Payout Decline')}}</label>
                                <div class="col-lg-12">
                                    <textarea type="text" name="payout_decline" rows="3" class="form-control tinymce">{{$set->payout_decline}}</textarea>
                                </div>
                            </div>
                            <div class="text-right">
                                <button type="submit" class="btn btn-primary">{{__('Save Changes')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @stop