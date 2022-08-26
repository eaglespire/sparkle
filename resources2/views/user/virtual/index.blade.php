@extends('userlayout')

@section('content')

<!-- Page content -->
<div class="container-fluid mt--6">
  <div class="content-wrapper mt-3">
    @if($user->kyc_status==null || $user->kyc_status=="RESUBMIT")
    <div class="card">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col-md-9">
            <h2 class="mb-0 font-weight-bolder text-dark">{{__('We need more information about you')}}</h2>
            <p>{{__('Compliance is currently due, please update your account information to have access to receiving payment.')}}</p>
          </div>
          <div class="col-md-3 text-right">
            <a href="{{route('user.compliance')}}" class="btn btn-neutral">{{__('Click here')}}</a>
          </div>
        </div>
      </div>
    </div>
    @elseif($user->kyc_status=="PROCESSING")
    <div class="card">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col-md-12">
            <h2 class="mb-0 font-weight-bolder text-dark">{{__('We are processing your request')}}</h2>
            <p>{{__('Compliance is currently being reviewed.')}}</p>
          </div>
        </div>
      </div>
    </div>
    @else
    <a href="#" data-toggle="modal" data-target="#buy" class="btn btn-neutral mb-5">{{__('New card')}}</a>
    @endif
    <div class="modal fade" id="buy" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
      <div class="modal-dialog modal- modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="mb-0 font-weight-bolder">{{__('New card')}}</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form role="form" action="{{route('user.check_plan')}}" method="post" id="payment-form">
            <div class="modal-body">
              @csrf
              <div class="form-group row">
                <div class="col-lg-12">
                  <div class="input-group">
                    <input type="number" step="any" class="form-control" autocomplete="off" onkeyup="currency()" id="amount" name="amount" placeholder="{{__('How much?')}}">
                    <span class="input-group-append">
                      <select class="form-control select" style="padding: 0.35rem 2rem 0.35rem;" id="currency" name="currency" required>
                        @if(count(getAcceptedCountryVirtual())>0)
                        @foreach(getAcceptedCountryVirtual() as $val)
                        <option value="{{$val->id}}*{{$val->virtual_min_amount}}*{{$val->virtual_max_amount}}*{{$val->virtual_fiat_charge}}*{{$val->virtual_percent_charge}}*{{$val->real->currency}}">{{$val->real->emoji.' '.$val->real->currency}}</option>
                        @endforeach
                        @endif
                      </select>
                    </span>
                  </div>
                </div>
              </div>
              <div class="card">
                <div class="card-body">
                  <div class="media align-items-center">
                    <div class="media-body">
                      <p>{{__('Card creation fee')}}: <span id="creation"></span></p>
                      <p>{{__('Total')}}: <span id="total"></span></p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-neutral btn-block my-4" id="ggglogin">{{__('Pay')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
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
          <a href="{{route('transactions.virtual', ['id'=>$val->card_hash])}}" class="dropdown-item"><i class="fal fa-sync"></i>{{__('Transactions')}}</a>
          <a data-toggle="modal" data-target="#modal-more{{$val->card_hash}}" href="" class="dropdown-item"><i class="fal fa-credit-card"></i>{{__('Card Details')}}</a>
          @if($val->status==1)
          <a data-toggle="modal" data-target="#modal-formfund{{$val->card_hash}}" href="" class="dropdown-item"><i class="fal fa-money-bill-wave-alt"></i>{{__('Fund Card')}}</a>
          <a data-toggle="modal" data-target="#modal-formwithdraw{{$val->card_hash}}" href="" class="dropdown-item"><i class="fal fa-arrow-circle-down"></i>{{__('Withdraw Money')}}</a>
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
          <div class="btn-wrapper text-center mb-3">
            <a href="javascript:void;" class="mb-3">
              <span class=""><i class="fal fa-credit-card-front fa-4x text-info"></i></span>
            </a>
          </div>
          <h3 class="text-dark">{{__('No Card')}}</h3>
          <p class="text-dark card-text">{{__('We couldn\'t find any card to this account')}}</p>
        </div>
      </div>
      @endif
    </div>
    @foreach($card as $k=>$val)
    <div class="modal fade" id="modal-formwithdraw{{$val->card_hash}}" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
      <div class="modal-dialog modal- modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="mb-0 font-weight-bolder">{{__('Withdraw funds from card')}}</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form method="post" action="{{route('withdraw.virtual')}}">
              @csrf
              <input type="hidden" name="id" value="{{$val->card_hash}}">
              <div class="form-group row">
                <label class="col-form-label col-lg-12">{{__('Amount')}}</label>
                <div class="col-lg-12">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">{{$val->getCurrency->real->currency}}</span>
                    </div>
                    <input type="number" step="any" name="amount" class="form-control" min="1" max="{{$val->amount}}" required>
                  </div>
                </div>
              </div>
              <div class="text-right">
                <button type="submit" class="btn btn-neutral btn-block my-4">{{__('Withdraw Funds')}}</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="modal-formfund{{$val->card_hash}}" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
      <div class="modal-dialog modal- modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="mb-0 font-weight-bolder">{{__('Add funds to card')}}</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form method="post" action="{{route('fund.virtual')}}">
              @csrf
              <input type="hidden" name="id" value="{{$val->card_hash}}">
              <div class="form-group row">
                <label class="col-form-label col-lg-12">{{__('Amount')}}</label>
                <div class="col-lg-12">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">{{$val->getCurrency->real->currency}}</span>
                    </div>
                    <input type="number" step="any" name="amount" class="form-control" min="{{$val->getCurrency->virtual_min_amount}}" max="{{$val->getCurrency->virtual_max_amount}}" required>
                  </div>
                </div>
              </div>
              <div class="text-right">
                <button type="submit" class="btn btn-neutral btn-block my-4">{{__('Pay')}}</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
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
    @section('script')
    <script>
      "use strict";

      function currency() {
        var xx = $("#currency").find(":selected").val();
        var myarr = xx.split("*");
        var cur = myarr[5].split("<");
        $("#amount").attr("min", myarr[1].split("<"));
        if (myarr[1].split("<") != "empty") {
          $("#amount").attr("max", myarr[2].split("<"));
        }
        var charge = (parseFloat($("#amount").val()) * parseFloat(myarr[4].split("<")) / 100) + isNaN(parseFloat(myarr[3].split("<")));
        var total = (parseFloat($("#amount").val()) * parseFloat(myarr[4].split("<")) / 100) + isNaN(parseFloat(myarr[3].split("<"))) + parseFloat($("#amount").val());
        if (isNaN(charge) || charge < 0) {
          charge = 0;
        }
        if (isNaN(total) || total < 0) {
          total = 0;
        }
        $("#creation").text(cur + ' ' + charge.toFixed(2));
        $("#total").text(cur + ' ' + total.toFixed(2));
      }
      $("#currency").change(currency);
      $("#amount").change(currency);
      currency();
    </script>
    @endsection