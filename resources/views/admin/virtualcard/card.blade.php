@extends('master')

@section('content')

<!-- Page content -->
<div class="container-fluid mt--6">
  <div class="content-wrapper">
    <div class="row align-items-center">
      @if(count($card)>0)
      @foreach($card as $k=>$val)
      <div class="col-md-4">
        <a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <div class="card credit-card">
            <div class="card__front card__part">
              <img class="card__front-square card__square" src="{{asset('asset/'.$logo->image_link)}}">
              <div class="card__front-logo card__logo text-dark">{{$val->getCurrency->real->currency.number_format($val->amount, 2)}}</div>
              <p class="card_number2 text-left"><img class="card__logo2 mb-0 pb-0" src="{{asset('asset/images/silver.png')}}"></p>
              <p class="card_number mb-0">{{preg_replace('~^.{4}|.{4}(?!$)~', '$0 ', $val->card_pan)}}</p>
              <div class="card__space-75">
                <span class="card__label">VALID TILL <span class="card__info2">{{$val->expiration}}</span></span>
                <p class="card__info">{{$val->name_on_card}}</p>
              </div>
              <div class="card__space-25">
                @if($val->card_type=="mastercard")
                <img class="card__front-logo2 card__logo" src="{{asset('asset/images/mastercard.png')}}">
                @else
                <img class="card__front-logo2 card__logo" src="{{asset('asset/images/visa.png')}}">
                @endif
              </div>
            </div>

            <div class="card__back card__part {{$val->bg}}">
              <div class="card__black-line"></div>
              <div class="card__back-content">
                <div class="card__secret">
                  <p class="card__secret--last">{{$val->cvv}}</p>
                </div>
                <img class="card__back-square card__square" src="{{asset('asset/'.$logo->image_link)}}">
                <div class="card__back-logo2 card__logo">@if($val->status==1) <span class="badge badge-pill badge-success">Active</span> @elseif($val->status==2) <span class="badge badge-pill badge-danger">Blocked</span>@endif</div>

              </div>
            </div>
          </div>
        </a>
        <div class="dropdown-menu dropdown-menu-top">
          @if($val->status==1)
          <a href="{{route('terminate.virtual', ['id'=>$val->card_hash])}}" class="dropdown-item"><i class="fal fa-times"></i>{{__('Terminate')}}</a>
          <a href="{{route('block.virtual', ['id'=>$val->card_hash])}}" class="dropdown-item"><i class="fal fa-ban"></i>{{__('Block')}}</a>
          @elseif($val->status==2)
          <a href="{{route('unblock.virtual', ['id'=>$val->card_hash])}}" class="dropdown-item"><i class="fal fa-check"></i>{{__('Unblock')}}</a>
          @endif
        </div>

      </div>
      @endforeach
      <div class="row">
        <div class="col-md-12">
          {{ $card->links('pagination::bootstrap-4') }}
        </div>
      </div>
      @else
      <div class="col-md-12 mb-5">
        <div class="text-center mt-8">
          <div class="btn-wrapper text-center">
            <a href="javascript:void;" class="btn btn-soft-warning btn-icon mb-3">
              <span class="btn-inner--icon"><i class="fad fa-credit-card-front fa-4x"></i></span>
            </a>
          </div>
          <h3 class="text-dark">No Virtual Card</h3>
          <p class="text-dark text-sm card-text">We couldn't find any virtual card</p>

        </div>
      </div>
      @endif
    </div>
    @foreach($card as $k=>$val)
    <div class="modal fade" id="modal-more{{$val->card_hash}}" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
      <div class="modal-dialog modal- modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="mb-0 font-weight-bolder">{{__('Card Details')}}</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>State: {{$val->state}}</p>
            <p>City: {{$val->city}}</p>
            <p>Zip Code: {{$val->zip_code}}</p>
            <p>Address: {{$val->address}}</p>
          </div>
        </div>
      </div>
    </div>
    @endforeach

    @stop