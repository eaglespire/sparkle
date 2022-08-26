@extends('master')

@section('content')
<div class="container-fluid mt--6">
  <div class="content-wrapper">
    @if($set->maintenance==1)
    <div class="card">
      <div class="card-body">
        <div class="media align-items-center">
          <div class="media-body">
            <p class="text-dark">{{__('We are currently under maintenance')}}</p>
          </div>
        </div>
      </div>
    </div>
    @endif
    <div class="row">
      <div class="col-md-3">
        <div class="card">
          <div class="card-body">
            <div class="d-sm-flex align-item-sm-center flex-sm-nowrap">
              <div>
                <h3 class="card-title">{{__('Users')}}</h3>
                <ul class="list list-unstyled mb-0 text-sm">
                  <li>{{__('Active users:')}} <span class="font-weight-semibold">#{{$activeusers}}</span></li>
                  <li>{{__('Blocked users:')}} <span class="font-weight-semibold">#{{$blockedusers}}</span></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card">
          <div class="card-body">
            <div class="d-sm-flex align-item-sm-center flex-sm-nowrap">
              <div>
                <h3 class="card-title">{{__('Support Ticket')}}</h3>
                <ul class="list list-unstyled mb-0 text-sm">
                  <li>{{__('Open tickets:')}} <span class="font-weight-semibold">
                      #{{$openticket}}</span></li>
                  <li>{{__('Closed tickets:')}} <span class="font-weight-semibold">
                      #{{$closedticket}}</span>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card">
          <div class="card-body">
            <div class="d-sm-flex align-item-sm-center flex-sm-nowrap">
              <div>
                <h3 class="card-title">{{__('Platform Reviews')}}</h3>
                <ul class="list list-unstyled mb-0 text-sm">
                  <li>{{__('Published reviews:')}} <span class="font-weight-semibold">
                      #{{$pubreview}}</span></li>
                  <li>{{__('Pending reviews:')}} <span class="font-weight-semibold">
                      #{{$unpubreview}}</span>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card">
          <div class="card-body">
            <div class="d-sm-flex align-item-sm-center flex-sm-nowrap">
              <div>
                <h3 class="card-title">{{__('Messages')}}</h3>
                <ul class="list list-unstyled mb-0 text-sm">
                  <li>{{__('Read:')}} <span class="font-weight-semibold">
                      #{{$pubmessage}}</span></li>
                  <li>{{__('Unread:')}} <span class="font-weight-semibold">
                      #{{$unpubmessage}}</span>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="">
          <div class="nav-wrapper">
            <ul class="nav nav-pills nav-fill nav-line-tabs nav-line-tabs-2x nav-stretch" id="tabs-icons-text" role="tablist">
              @foreach(getAcceptedCountry() as $val)
              <li class="nav-item">
                <a class="nav-link mb-sm-3 mb-md-0 @if($loop->first==$val->id) active @endif" id="tabs-icons-text-{{$val->id}}-tab" href="#tabs-icons-text-{{$val->id}}" data-toggle="tab" role="tab" aria-controls="tabs-icons-text-{{$val->id}}" aria-selected="true">{{$val->real->emoji}} {{$val->real->currency}}</a>
              </li>
              @endforeach
            </ul>
          </div>
        </div>
        <div class="tab-content" id="myTabContent">
          @foreach(getAcceptedCountry() as $val)
          <div class="tab-pane fade @if($loop->first==$val->id) show active @endif" id="tabs-icons-text-{{$val->id}}" role="tabpanel" aria-labelledby="tabs-icons-text-{{$val->id}}-tab">
            <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-body">
                    <div class="d-sm-flex align-item-sm-center flex-sm-nowrap">
                      <div>
                        <h3 class="card-title">{{__('Transactions')}}</h3>
                        <ul class="list list-unstyled mb-0 text-sm">
                          <li>{{__('This year')}} <span class="font-weight-semibold">{{$val->real->currency_symbol.$val->transactionYear(1)}} [{{$val->transactionYear(2)}}]</span></li>
                          <li>{{__('This Month')}} <span class="font-weight-semibold">{{$val->real->currency_symbol.$val->transactionMonth(1)}} [{{$val->transactionMonth(2)}}]</span></li>
                          <li>{{__('Today')}} <span class="font-weight-semibold">{{$val->real->currency_symbol.$val->transactionToday(1)}} [{{$val->transactionToday(2)}}]</span></li>
                          <li>{{__('Total')}} <span class="font-weight-semibold">{{$val->real->currency_symbol.$val->transactionTotal(1)}} [{{$val->transactionTotal(2)}}]</span></li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card">
                  <div class="card-body">
                    <div class="d-sm-flex align-item-sm-center flex-sm-nowrap">
                      <div>
                        <h3 class="card-title">{{__('Charges')}}</h3>
                        <ul class="list list-unstyled mb-0 text-sm">
                          <li>{{__('This year')}} <span class="font-weight-semibold">{{$val->real->currency_symbol.$val->chargeYear(1)}} [{{$val->chargeYear(2)}}]</span></li>
                          <li>{{__('This Month')}} <span class="font-weight-semibold">{{$val->real->currency_symbol.$val->chargeMonth(1)}} [{{$val->chargeMonth(2)}}]</span></li>
                          <li>{{__('Today')}} <span class="font-weight-semibold">{{$val->real->currency_symbol.$val->chargeToday(1)}} [{{$val->chargeToday(2)}}]</span></li>
                          <li>{{__('Total')}} <span class="font-weight-semibold">{{$val->real->currency_symbol.$val->chargeTotal(1)}} [{{$val->chargeTotal(2)}}]</span></li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card">
                  <div class="card-body">
                    <div class="d-sm-flex align-item-sm-center flex-sm-nowrap">
                      <div>
                        <h3 class="card-title">{{__('Payout')}}</h3>
                        <ul class="list list-unstyled mb-0 text-sm">
                          <li>{{__('This year')}} <span class="font-weight-semibold">{{$val->real->currency_symbol.$val->payoutYear(1)}} [{{$val->payoutYear(2)}}]</span></li>
                          <li>{{__('This Month')}} <span class="font-weight-semibold">{{$val->real->currency_symbol.$val->payoutMonth(1)}} [{{$val->payoutMonth(2)}}]</span></li>
                          <li>{{__('Today')}} <span class="font-weight-semibold">{{$val->real->currency_symbol.$val->payoutToday(1)}} [{{$val->payoutToday(2)}}]</span></li>
                          <li>{{__('Total')}} <span class="font-weight-semibold">{{$val->real->currency_symbol.$val->payoutTotal(1)}} [{{$val->payoutTotal(2)}}]</span></li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>
@stop