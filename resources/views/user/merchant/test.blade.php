@extends('user.link.menu')

@section('content')
<div class="main-content">
    <div class="header py-5 pt-7">
        <div class="container">
            <div class="header-body text-center mb-8">
                <div class="row justify-content-center">
                </div>
            </div>
        </div>
    </div>
    <div class="container mt--8 pb-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header mb-0">
                        <div class="row align-items-center">
                            @if($link->logo!=null)
                            @if(UR_exists($link->logo))
                            <div class="col-auto">
                                <div class="avatar avatar-lg">
                                    <img alt="Image placeholder" src="{{$link->logo}}">
                                </div>
                            </div>
                            @endif
                            @endif
                            <div class="col @if($link->logo!=null)@if(UR_exists($link->logo))text-right @endif @endif">
                                <h2 class="fw-bold mb-0"><span class="text-sm">{{$link->getCurrency->real->currency}}</span> {{number_format($link->amount,2)}}</h2>
                                <p class="text-sm">{{$link->email}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('payment.submit', ['id'=>$link->ref_id])}}" method="post" id="payment-form">
                            @csrf
                            <div class="form-group">
                                <select class="form-control" name="status" required>
                                    <option value="">{{__('Select transaction response')}}</option>
                                    <option value="1">{{__('Successful')}}</option>
                                    <option value="2">{{__('Failed')}}</option>
                                </select>
                                @if ($errors->has('status'))
                                <span>{{$errors->first('status')}}</span>
                                @endif
                            </div>
                            <input type="hidden" value="test" name="type">
                            <input type="hidden" value="2" name="crf">
                            <div class="text-center">
                                <button type="submit" @if(getTransaction($link->id, $link->user_id)!=null) disabled @endif id="ggglogin" class="btn btn-neutral btn-block my-4">
                                    @if(getTransaction($link->id, $link->user_id)==null)
                                    {{__('Pay')}}
                                    @if($link->user->business()->charges==1)
                                    {{$link->getCurrency->real->currency}}<span id="tcr">{{number_format(($link->amount*$link->getCurrency->percent_charge/100)+($link->getCurrency->fiat_charge)+($link->amount),2)}}</span>
                                    @else
                                    {{$link->getCurrency->real->currency}}<span id="tcr">{{number_format($link->amount,2)}}</span>
                                    @endif
                                    @else
                                    {{__('SESSION EXPIRED')}}
                                    @endif
                                </button>
                                <span class="badge badge-pill badge-primary"><i class="fal fa-ban"></i> {{__('Test mode')}}</span></br></br>
                                <p class="text-default text-xs"><i class="fal fa-lock"></i> {{__('Secured by')}} <span class="fw-bold">{{$set->site_name}}</span></p>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row justify-content-center mt-5">
                    <a href="{{route('cancel.payment', ['id'=>$link->ref_id])}}" class="text-white"><i class="fal fa-times"></i> {{__('Cancel Payment')}}</a>
                </div>
            </div>
        </div>
    </div>
    @stop
    @section('script')
    @endsection