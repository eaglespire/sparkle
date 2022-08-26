
@extends('user.link.menu')

@section('content')
<div class="main-content">
    <div class="header py-5 pt-7">
        <div class="container">
            <div class="header-body text-center mb-7">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="card-profile-image mb-5">
                            <img src="{{asset('asset/'.$logo->dark)}}">
                        </div>
                        <h5 class="h4 font-weight-bolder mb-3"></h5>
                        <p class="text-white">{{__('Select from where you will make a payment')}}</p>
                        <h5 class="h4 font-weight-bolder text-white">{{__('Select more banks')}}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container mt--8 pb-5">
        <div class="row justify-content-center">  
            <div class="col-md-12">
                <div class="row">
                    @foreach(array_slice($institution, 0, 6) as $val)   
                        @if(in_array("CREATE_DOMESTIC_SINGLE_PAYMENT",$val->features))
                            <div class="col-6 col-xl-2">
                                <a href="{{route('authorize.payment', ['auth_token'=>$authtoken,'bank_id'=>$val->id,'trans_type'=>$type,'reference'=>$reference])}}">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row align-items-center text-center">
                                                <div class="col">
                                                    <div class="avatar">
                                                        <img alt="{{$val->name}}" src="{{$val->media[0]->source}}">
                                                    </div>
                                                    <p>{{str_limit($val->name,10)}}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="row justify-content-center">
                    <a data-toggle="modal" data-target="#create" href="" class="btn btn-neutral-2 rounded-pill my-4">Click here to see more banks</a>
                </div>
                <div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="mb-0 font-weight-bolder">{{__('Find your bank')}}</h3>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i class="fal fa-times"></i>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group row mb-5">
                                    <div class="col-lg-12">
                                        <input type="text" id="myInput" class="form-control" placeholder="{{__('Search acount provider')}}">
                                    </div>
                                </div>
                                <ul class="list-group list-group-flush list my--3" id="myDIV" style="display:block;height:350px;overflow-y:auto;">
                                @foreach($institution as $val)   
                                    @if(in_array("CREATE_DOMESTIC_SINGLE_PAYMENT",$val->features))
                                        <li class="list-group-item px-0">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <!-- Avatar -->
                                                    <a href="{{route('authorize.payment', ['auth_token'=>$authtoken,'bank_id'=>$val->id,'trans_type'=>$type,'reference'=>$reference])}}" class="avatar">
                                                    <img alt="{{$val->name}}" src="{{$val->media[0]->source}}">
                                                    </a>
                                                </div>
                                                <div class="col">
                                                    <a href="{{route('authorize.payment', ['auth_token'=>$authtoken,'bank_id'=>$val->id,'trans_type'=>$type,'reference'=>$reference])}}">
                                                        <h5>{{$val->name}}</h5>
                                                    </a>
                                                </div>
                                            </div>
                                        </li>
                                    @endif
                                @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
        </div>
        <div class="row justify-content-center mt-9">
            <div class="col-md-12">
                <div class="text-center">
                    <h5 class="text-white">{{__('Powered by')}} </h5><img alt="html" style="height:auto;max-width:20%;" src="{{asset('asset/images/SafeConnect_logo.png')}}">
                    <p class="text-white">{{__('Data is securely accessed in read-only format and only for the purposes of this payment request. This request is a one off, you will not receive any other requests from SafeConnect for this payment. SafeConnect will retrieve bank data needed to facilitate this payment based on your request and provide this information to ')}}{{$set->site_name}}. {{__('SafeConnect Ltd is authorised and regulated by the Financial Conduct Authority under the Payment Service Regulations 2017 [827001] for the provision of Account Information and Payment Initiation services.')}}</p>
                </div>
            </div>
        </div>
        <div class="row justify-content-center mt-5">
            <a class="text-white" href="{{url()->previous()}}"><i class="fal fa-times"></i> {{__('Cancel Payment')}}</a>
        </div>

@stop