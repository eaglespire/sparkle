@extends('userlayout')

@section('content')
<div class="container-fluid mt--6">
  <div class="content-wrapper mt-3">
    <h2 class="mb-3 text-dark fw-bold">{{__('Welcome')}} {{ucwords(strtolower($user->first_name))}}, 👋🏼</h2>
    @if($user->business()->kyc_status==null || $user->business()->kyc_status=="RESUBMIT")
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
    @endif
    @if($user->business()->kyc_status=="PROCESSING")
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
    @endif
    @if(count(getAcceptedCountryVirtual())>0)
    <div class="card">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col-md-9 mb-2">
            <h2 class="mb-2 font-weight-bolder text-dark">{{__('Get a virtual card')}}</h2>
            <p class="mb-3">Get @foreach(getAcceptedCountryVirtual() as $val){{$val->real->currency}}@if(!$loop->last),@endif @endforeach virtual cards that work globally. Pay staff, interns, and recurring service providers instantly (at any time) so they can access salaries even on weekends.</p>
            <a href="{{route('user.card')}}" class="btn btn-neutral">{{__('Get your Virtual Card')}}</a>
          </div>
          <div class="col-md-3 text-md-left text-lg-right">
            <img src="{{asset('asset/images/credit-card.png')}}" style="max-width:40%; height:auto">
          </div>
        </div>
      </div>
    </div>
    @endif
    <div class="row">
      <div class="col-md-12">
        <div class="nav-wrapper">
          <ul class="nav nav-pills nav-fill nav-line-tabs nav-line-tabs-2x nav-stretch" id="tabs-icons-text" role="tablist">
            @foreach(getAcceptedCountry() as $val)
            <li class="nav-item">
              <a class="nav-link mb-sm-3 mb-md-0 @if(route('user.dashboard')==url()->current() && $val->id==$user->getFirstBalance()->country_id) active @endif @if($currency==$user->getBalance($val->id)->ref_id) active @endif" id="tabs-icons-text-{{$val->id}}-tab" href="{{route('user.dashboard', ['currency'=>$user->getBalance($val->id)->ref_id])}}" role="tab" aria-controls="tabs-icons-text-{{$val->id}}" aria-selected="true">{{$val->real->emoji.' '.$val->real->currency}}</a>
            </li>
            @endforeach
          </ul>
        </div>
      </div>
    </div>
      <div class="tab-content" id="myTabContent">
        @foreach(getAcceptedCountry() as $val)
        <div class="tab-pane fade @if(route('user.dashboard')==url()->current() && $val->id==$user->getFirstBalance()->country_id) show active @endif @if($currency==$user->getBalance($val->id)->ref_id) show active @endif" id="tabs-icons-text-{{$val->id}}" role="tabpanel" aria-labelledby="tabs-icons-text-{{$val->id}}-tab">
          <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6 col-12 mb-2">
                        <div class="media align-items-center">
                            <div class="media-body">
                                @if($user->business()->live==1)
                                <h2 class="mb-1 text-dark font-weight-bolder">{{__('Available Balance')}}: {{number_format($user->getBalance($val->id)->amount,2).' '.$val->real->currency}}</h2>
                                <p class="text-dark">{{__('Pending Balance')}}: {{number_format($user->getPendingTransactions($val->id),2).' '.$val->real->currency}}</p>
                                @else
                                <h2 class="mb-0 text-dark font-weight-bolder">{{__('Balance')}}: {{number_format($user->getBalance($val->id)->test,2).' '.$val->real->currency}}</h2>
                                @endif
                                @if(count($user->getUniqueTransactions($val->id))>0)
                                <p class="text-dark">{{__('Last transaction')}}: {{date("Y/m/d h:i:A", strtotime($user->getLastTransaction($val->id)->created_at))}}</p>
                                @else
                                <p class="text-dark">{{__('Last transaction')}}: {{__('No record')}}</p>
                                @endif
                                <p class="text-dark">{{__('Wallet id')}}: {{$user->getBalance($val->id)->id}}</p>
                            </div>
                        </div>
                    </div>
                    @if($user->business()->live==1)
                    <div class="col-md-6 col-12 text-md-end">
                        @if($val->funding==1)
                        <a href="{{route('fund.account', ['id'=>$user->getBalance($val->id)->ref_id])}}" class="btn btn-neutral"><i class="fal fa-plus-circle"></i> {{__('Deposit')}}</a>
                        @endif
                        @if($user->getBalance($val->id)->amount>0)
                        <a data-toggle="modal" data-target="#payout{{$val->id}}" href="" class="btn btn-neutral"><i class="fal fa-share"></i> {{__('Transfer')}} {{$val->real->currency}}</a>
                        @endif
                    </div>
                    @endif
                    <div class="modal fade" id="payout{{$val->id}}" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h3 class="mb-0 font-weight-bolder">{{__('Request Payout')}}</h3>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="{{route('withdraw.submit', ['id'=>$user->getBalance($val->id)->ref_id])}}" method="post">
                                    <div class="modal-body">
                                        @csrf
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text text-future">{{$val->real->currency_symbol}}</span>
                                                </div>
                                                <input type="number" class="form-control" min="1" max="{{$user->getBalance($val->id)->amount}}" autocomplete="off" id="amount{{$val->id}}" name="amount" placeholder="{{__('How much?')}}" required>
                                            </div>
                                        </div>
                                        <input type="hidden" id="withdraw_percent_charge{{$val->id}}" value="{{$val->withdraw_percent_charge}}">
                                        <input type="hidden" id="withdraw_fiat_charge{{$val->id}}" value="{{$val->withdraw_fiat_charge}}">
                                        <div class="form-group">
                                            <select class="form-control" id="payout_type{{$val->id}}" name="payout_type" required>
                                              <option value="2*{{$val->bank_format}}">Beneficiary</option>
                                              <option value="1*{{$val->bank_format}}">New Beneficiary</option>
                                            </select>
                                        </div>
                                        <div class="form-group" id="old_beneficiary{{$val->id}}" style="display:none;">
                                            <select class="form-control" id="beneficiary{{$val->id}}" name="beneficiary">
                                              <option value="">{{__('Select beneficiary')}}</option>
                                              @foreach($user->getBeneficiary($val->id) as $ben)
                                                <option value="{{$ben->id}}">{{$ben->name}} -
                                                @if($val->bank_format=="us")
                                                {{$ben->routing_no}}
                                                @elseif($val->bank_format=="eur")
                                                {{$ben->iban}}
                                                @elseif($val->bank_format=="uk")
                                                {{$ben->acct_no}} - {{$ben->sort_code}}
                                                @elseif($val->bank_format=="normal")
                                                {{getBankFirst($ben->bank_name)->name}} - {{$ben->acct_no}} 
                                                @endif
                                                </option>
                                              @endforeach
                                            </select>
                                        </div>
                                        <div id="bank{{$val->id}}" style="display:none;">
                                          <div class="form-group">
                                              <input type="text" name="name" id="name{{$val->id}}" maxlength="255" class="form-control" placeholder="{{__('Name of account holder')}}">
                                          </div>
                                          @if($val->bank_format=="us")
                                          <div class="form-group">
                                              <input type="text" name="routing_no" id="routing_no{{$val->id}}" pattern="\d*" maxlength="9" minlength="9" class="form-control" placeholder="{{__('Routing number')}}">
                                          </div>
                                          @elseif($val->bank_format=="eur")
                                          <div class="form-group">
                                              <input type="text" name="iban" id="iban{{$val->id}}" pattern="\d*" maxlength="16" minlength="16" class="form-control" placeholder="{{__('Iban')}}">
                                          </div>
                                          @elseif($val->bank_format=="uk")
                                          <div class="form-group">
                                              <input type="text" name="acct_no" id="acct_no{{$val->id}}" pattern="\d*" maxlength="8" minlength="8" class="form-control" placeholder="{{__('Account number')}}">
                                          </div>
                                          <div class="form-group">
                                              <input type="text" name="sort_code" id="sort_code{{$val->id}}" pattern="\d*" maxlength="6" minlength="6" class="form-control" placeholder="{{__('Sort code')}}">
                                          </div>
                                          @elseif($val->bank_format=="normal")
                                          <div class="form-group">
                                              <select class="form-control" name="bank_name" id="bank_name{{$val->id}}">
                                                  <option value="">Select bank</option>
                                                  @foreach(getBank($val->id) as $bank)
                                                  <option value="{{$bank->id}}">{{$bank->name}}</option>
                                                  @endforeach
                                              </select>
                                          </div>
                                          <div class="form-group">
                                              <input type="text" name="acct_no" id="acct_no{{$val->id}}" pattern="\d*" maxlength="10" minlength="10" class="form-control" placeholder="{{__('Account number')}}">
                                          </div>
                                          <div class="form-group">
                                              <input type="text" name="acct_name" id="acct_name{{$val->id}}" class="form-control" placeholder="{{__('Account name')}}">
                                          </div>
                                          @endif
                                        </div>
                                        <div class="row mt-3 mb-3" id="new_beneficiary{{$val->id}}" style="display:none;">
                                          <div class="col-6">
                                            <div class="custom-control custom-control-alternative custom-checkbox">
                                              <input class="custom-control-input" id="custombeneficiary{{$val->id}}" type="checkbox" name="new_beneficiary">
                                              <label class="custom-control-label" for="custombeneficiary{{$val->id}}">
                                                <span class="text-dark">{{__('Save as Beneficiary')}}</span>
                                              </label>
                                            </div>
                                          </div>
                                        </div>
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="media align-items-center">
                                                    <div class="media-body">
                                                        <p>{{__('You will receive')}}: <span id="receive{{$val->id}}">0.00</span>{{$val->real->currency}}</p>
                                                        <p>{{__('Transaction charge')}}: <span id="charge{{$val->id}}">0.00</span>{{$val->real->currency}}</p>
                                                        <p>{{__('Next settlement')}}: {{date("M j, Y", strtotime(nextPayoutDate($val->duration)))}}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" id="payment{{$val->id}}" class="btn btn-neutral btn-block">{{__('Submit Request')}}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
          </div>
          @if($user->business()->live==1)
          @if(count(getCountryRates($val->id))>0)
          <div class="card">
            <div class="card-header">
              <h2 class="mb-0 font-weight-bolder text-dark">{{__('Convert')}} {{$val->real->emoji.$val->real->currency}}</h2>
            </div>
            <div class="card-body">
              <form action="{{route('swap.submit', ['id'=>$user->getBalance($val->id)->ref_id])}}" method="post" id="payment-form{{$val->id}}">
                @csrf
                <input type="hidden" id="min_swap{{$val->id}}" value="{{$val->swap_min_amount}}">
                <input type="hidden" id="max_swap{{$val->id}}" value="{{$val->swap_max_amount}}">
                <div class="form-group row">
                    <div class="col-lg-5">
                        <div class="input-group">
                            <input type="number" name="from_amount" step="any" id="from_amount{{$val->id}}" value="{{$val->swap_min_amount}}" min="{{$val->swap_min_amount}}" max="{{$val->swap_max_amount}}" onkeyup="convert{{$val->id}}()" class="form-control form-control-lg fw-bold text-lg" autocomplete="off" required>
                            <span class="input-group-prepend ">
                              <span class="input-group-text fw-bold text-lg">{{$val->real->emoji.$val->real->currency}}</span>
                            </span>
                        </div>
                        <div class="invalid-feedback" id="invalid-feedback-from{{$val->id}}">
                          You can only swap between {{number_format($val->swap_min_amount,2).$val->real->currency.' - '.number_format($val->swap_max_amount).$val->real->currency}}
                        </div>
                        @if ($errors->has('from_amount'))
                        <span>{{$errors->first('from_amount')}}</span>
                        @endif
                    </div>   
                    <div class="col-lg-2 text-center mt-2 mb-2">
                      <span class="text-indigo">
                        <i class="fal fa-sync-alt fa-3x text-indigo"></i><br><br>
                        Rate: <span id="dd{{$val->id}}"></span><br><br>
                        Charge: <span id="cc{{$val->id}}"></span>
                      </span>
                    </div>                   
                    <div class="col-lg-5">
                      <div class="input-group">
                        <input type="number" step="any" class="form-control form-control-lg fw-bold text-lg" autocomplete="off" id="to_amount{{$val->id}}" onkeyup="convertalt{{$val->id}}()" name="amount" required>
                        <span class="input-group-append">
                          <select class="form-control select form-control-lg fw-bold text-lg" style="padding: 0.35rem 2rem 0.35rem;" id="rate{{$val->id}}" onclick="convertselect{{$val->id}}()"name="currency" required>
                            @if(count(getCountryRates($val->id))>0)
                            @foreach(getCountryRates($val->id) as $dal)
                            <option value="{{$dal->id}}*{{$dal->rate}}*{{$dal->to_currency}}*{{$dal->getCurrency->real->currency}}*{{$dal->charge}}">{{$dal->getCurrency->real->emoji.' '.$dal->getCurrency->real->currency}}</option>
                            @endforeach
                            @endif
                          </select>
                        </span>
                      </div>
                      <div class="invalid-feedback"  id="invalid-feedback-to{{$val->id}}">
                        You can only swap between {{number_format($val->swap_min_amount,2).$val->real->currency.' - '.number_format($val->swap_max_amount).$val->real->currency}}
                      </div>
                    </div>
                </div>                                                                  
                <div class="text-center">
                    <button type="submit" class="btn btn-neutral" id="ggglogin{{$val->id}}">{{__('Make Transaction')}}</button>
                </div>
                <div class="accordion" id="accordionExample">
                  <div class="card-header border-bottom" style="padding: 1rem 1rem;" id="heading{{$val->id}}">
                      <div data-toggle="collapse" data-target="#collapse{{$val->id}}" aria-expanded="true" aria-controls="collapse{{$val->id}}">
                        <p class="text-default">Todays Rate</p>
                      </div>
                  </div>
                  <div id="collapse{{$val->id}}" class="collapse" aria-labelledby="heading{{$val->id}}" data-parent="#accordionExample">
                    <table class="table table-flush border-top-0">
                      <thead class="border-top-0">
                          <tr>
                              <th class="text-left">{{__('Currency')}}</th>
                              <th class="text-right">{{__('Rate')}}</th>
                          </tr>
                      </thead>
                      <tbody>
                          @foreach(getCountryRates($val->id) as $k=>$dal)
                          <tr>
                              <td class="text-left">{{$dal->getCurrency->real->emoji.' '.$dal->getCurrency->real->currency}}</td>
                              <td class="text-right">{{$dal->getCurrency->real->currency_symbol.number_format($dal->rate,2)}}</td>
                          </tr>
                          @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
              </form>
            </div>
          </div>
          @endif
          @endif
          <div class="card no-shadow">
            <div class="card-header">
              <h2 class="mb-0 font-weight-bolder text-dark">{{__('Earnings')}} {{$val->real->emoji.$val->real->currency}}</h2>
            </div>
            <div class="card-body">
              @if(count($user->getTransactionsExceptPayout($val->id))>0)
              <div id="myChart{{$val->id}}"></div>
              @else
              <div class="text-center mt-5 mb-3">
                <div class="btn-wrapper text-center mb-3">
                  <a href="javascript:void;" class="mb-3">
                    <span class=""><i class="fal fa-waveform-path fa-4x text-muted"></i></span>
                  </a>
                </div>
                <h3 class="text-dark">{{__('No Earning History')}}</h3>
                <p class="text-dark">{{__('We couldn\'t find any earning log to this account')}}</p>
              </div>
              @endif
            </div>
          </div>
        </div>
        @endforeach
      </div>
    <div class="card">
      <div class="card-body">
        <h2 class="mb-0 font-weight-bolder text-dark">{{__('API Documentation')}}</h2>
        <p class="mb-2">{{__('Our documentation contains what you need to integrate')}} {{$set->site_name}} {{__('in your website.')}}</p>
        <a href="{{route('user.documentation')}}" class="btn btn-neutral mb-5"><i class="fal fa-file-alt"></i> {{__('Documentation')}}</a>
        <h4 class="mb-2 font-weight-bolder">{{__('Your Keys')}}</h4>
        <div class="mb-3">
          <span class="text-gray mb-3">{{__('Also available in')}}</span> <a href="{{route('user.api')}}">{{__('Settings > API Keys')}}</a>
        </div>
        @if($user->business()->live==1)
        <div class="form-group row">
          <label class="col-form-label col-lg-2">{{__('Public key')}}</label>
          <div class="col-lg-10">
            <div class="input-group">
              <input type="text" name="public_key" disabled class="form-control" placeholder="Public key" value="{{$user->business()->public_key}}">
              <div class="input-group-append">
                <span class="input-group-text castro-copy" data-clipboard-text="{{$user->business()->public_key}}" title="{{__('Copy to clipboard')}}"><i class="fal fa-copy"></i></span>
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
                <span class="input-group-text castro-copy" data-clipboard-text="{{$user->business()->secret_key}}" title="{{__('Copy to clipboard')}}"><i class="fal fa-copy"></i></span>
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
                <span class="input-group-text castro-copy" data-clipboard-text="{{$user->business()->test_public_key}}" title="{{__('Copy to clipboard')}}"><i class="fal fa-copy"></i></span>
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
                <span class="input-group-text castro-copy" data-clipboard-text="{{$user->business()->test_secret_key}}" title="{{__('Copy to clipboard')}}"><i class="fal fa-copy"></i></span>
              </div>
            </div>
          </div>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>
@stop
@section('script')
@foreach(getAcceptedCountry() as $gval)
@php $currency = $gval->real->currency; @endphp
<script type="text/javascript">
  $(document).ready(function() {
    var element@php echo $gval->id;@endphp = document.getElementById('myChart{{$gval->id}}');
    var height = parseInt(200);
    var labelColor = '#a1a5b7';
    var borderColor = '#eff2f5';
    var baseColor = '#00a3ff';
    var lightColor = '#f1faff';

    if (!element@php echo $gval->id;@endphp) {
      return;
    }

    var options = {
      series: [{
        name: 'Received',
        data: [<?php foreach ($user->getTransactionsExceptPayout($gval->id) as $val) {
                  echo $val->amount . ',';
                } ?>]
      }],
      chart: {
        fontFamily: 'inherit',
        type: 'area',
        height: height,
        width: "100%",
        toolbar: {
          show: !1
        },
        zoom: {
          enabled: true
        },
        sparkline: {
          enabled: !0
        }
      },
      plotOptions: {

      },
      legend: {
        show: true
      },
      dataLabels: {
        enabled: true,
        enabledOnSeries: undefined,
        formatter: function(val, opts) {
          return '@php echo $currency; @endphp' + val
        },
        textAnchor: 'middle',
        distributed: false,
        offsetX: 0,
        offsetY: 0,
        style: {
          fontSize: '14px',
          fontFamily: 'inherit',
          colors: undefined
        },
        background: {
          enabled: true,
          foreColor: '#000',
          padding: 4,
          borderRadius: 2,
          borderWidth: 1,
          borderColor: '#000',
          opacity: 0.9,
          dropShadow: {
            enabled: false,
            top: 1,
            left: 1,
            blur: 1,
            color: '#000',
            opacity: 0.45
          }
        },
        dropShadow: {
          enabled: false,
          top: 1,
          left: 1,
          blur: 1,
          color: '#000',
          opacity: 0.45
        }
      },
      fill: {
        type: 'solid',
        opacity: 1
      },
      stroke: {
        curve: 'smooth',
        show: true,
        width: 0.5,
        colors: [baseColor]
      },
      xaxis: {
        categories: [<?php foreach ($user->getTransactionsExceptPayout($gval->id) as $val) {
                        echo "'" . date("M j", strtotime($val->updated_at)) . "'" . ',';
                      } ?>],
        axisBorder: {
          show: true,
        },
        axisTicks: {
          show: true
        },
        labels: {
          style: {
            colors: labelColor,
            fontSize: '12px'
          }
        },
        crosshairs: {
          position: 'front',
          stroke: {
            color: baseColor,
            width: 1,
            dashArray: 3
          }
        },
        tooltip: {
          enabled: true,
          formatter: undefined,
          offsetY: 0,
          style: {
            fontSize: '12px'
          }
        }
      },
      yaxis: {
        labels: {
          style: {
            colors: labelColor,
            fontSize: '12px'
          }
        }
      },
      states: {
        normal: {
          filter: {
            type: 'none',
            value: 0
          }
        },
        hover: {
          filter: {
            type: 'none',
            value: 0
          }
        },
        active: {
          allowMultipleDataPointsSelection: false,
          filter: {
            type: 'none',
            value: 0
          }
        }
      },
      tooltip: {
        style: {
          fontSize: '12px'
        },
        y: {
          formatter: function(val) {
            return '@php echo $currency; @endphp' + val
          }
        }
      },
      colors: [lightColor],
      grid: {
        borderColor: borderColor,
        strokeDashArray: 4,
        yaxis: {
          lines: {
            show: true
          }
        }
      },
      markers: {
        strokeColor: baseColor,
        strokeWidth: 3
      }
    };
    var chart@php echo $gval->id; @endphp = new ApexCharts(element@php echo $gval->id;@endphp, options);
    chart@php echo $gval->id;@endphp.render();
  });
</script>
@endforeach
  @foreach(getAcceptedCountry() as $val)
    <script>  
      function ben@php echo $val->id; @endphp() {
          var payout_type@php echo $val->id; @endphp = $("#payout_type{{$val->id}}").find(":selected").val();
          var myarr@php echo $val->id; @endphp = payout_type@php echo $val->id; @endphp.split("*");
          if(myarr@php echo $val->id; @endphp[0].split("<")==1){
            $("#bank{{$val->id}}").show();
            $("#new_beneficiary{{$val->id}}").show();
            $("#old_beneficiary{{$val->id}}").hide();
            $("#name{{$val->id}}").attr('required', '');
            if(myarr@php echo $val->id; @endphp[1].split("<")=="us"){
              $("#routing_no{{$val->id}}").attr('required', '');
            }else if(myarr@php echo $val->id; @endphp[1].split("<")=="eur"){
              $("#iban{{$val->id}}").attr('required', '');
            }else if(myarr@php echo $val->id; @endphp[1].split("<")=="uk"){
              $("#acct_no{{$val->id}}").attr('required', '');
              $("#sort_code{{$val->id}}").attr('required', '');
            }else if(myarr@php echo $val->id; @endphp[1].split("<")=="normal"){
              $("#bank_name{{$val->id}}").attr('required', '');
              $("#acct_no{{$val->id}}").attr('required', '');
              $("#acct_name{{$val->id}}").attr('required', '');
            }
            $("#beneficiary{{$val->id}}").removeAttr('required', '');
          }else if(myarr@php echo $val->id; @endphp[0].split("<")==2){
            $("#bank{{$val->id}}").hide();
            $("#old_beneficiary{{$val->id}}").show();
            $("#new_beneficiary{{$val->id}}").hide();
            $("#name{{$val->id}}").removeAttr('required', '');
            if(myarr@php echo $val->id; @endphp[1].split("<")=="us"){
              $("#routing_no{{$val->id}}").removeAttr('required', '');
            }else if(myarr@php echo $val->id; @endphp[1].split("<")=="eur"){
              $("#iban{{$val->id}}").removeAttr('required', '');
            }else if(myarr@php echo $val->id; @endphp[1].split("<")=="uk"){
              $("#acct_no{{$val->id}}").removeAttr('required', '');
              $("#sort_code{{$val->id}}").removeAttr('required', '');
            }else if(myarr@php echo $val->id; @endphp[1].split("<")=="normal"){
              $("#bank_name{{$val->id}}").removeAttr('required', '');
              $("#acct_no{{$val->id}}").removeAttr('required', '');
              $("#acct_name{{$val->id}}").removeAttr('required', '');
            }
            $("#beneficiary{{$val->id}}").attr('required', '');
          }
      }
      $("#payout_type{{$val->id}}").change(ben@php echo $val->id; @endphp);
      ben@php echo $val->id; @endphp();
    </script>
    <script>
        function withdraw@php echo $val->id; @endphp() {
            var amount@php echo $val->id; @endphp = $("#amount{{$val->id}}").val();
            var percent@php echo $val->id; @endphp = $("#withdraw_percent_charge{{$val->id}}").val();
            var fiat@php echo $val->id; @endphp = $("#withdraw_fiat_charge{{$val->id}}").val();
            var receive@php echo $val->id; @endphp = parseFloat(amount@php echo $val->id; @endphp)-parseFloat(fiat@php echo $val->id; @endphp)-(parseFloat(amount@php echo $val->id; @endphp)*isNaN(parseFloat(percent@php echo $val->id; @endphp))/100);
            var charge@php echo $val->id; @endphp = parseFloat(fiat@php echo $val->id; @endphp)+(parseFloat(amount@php echo $val->id; @endphp)*isNaN(parseFloat(percent@php echo $val->id; @endphp))/100);
            if (isNaN(receive@php echo $val->id; @endphp) || receive@php echo $val->id; @endphp < 0) {
                receive@php echo $val->id; @endphp=0;
            }
            $("#receive{{$val->id}}").text(receive@php echo $val->id; @endphp.toFixed(2));
            $("#charge{{$val->id}}").text(charge@php echo $val->id; @endphp.toFixed(2));
            if(receive@php echo $val->id; @endphp<charge@php echo $val->id; @endphp){
                $("#payment{{$val->id}}").attr('disabled','disabled');
            }else{
                $("#payment{{$val->id}}").removeAttr('disabled','');
            }
        }
        $("#amount{{$val->id}}").keyup(withdraw@php echo $val->id; @endphp);
    </script>
    <script>
      "use strict";
      function convert@php echo $val->id; @endphp(){
        var from_amount@php echo $val->id; @endphp = $("#from_amount{{$val->id}}").val();
        var to_amount@php echo $val->id; @endphp = $("#to_amount{{$val->id}}").val();
        var min@php echo $val->id; @endphp = $("#min_swap{{$val->id}}").val();
        var max@php echo $val->id; @endphp = $("#max_swap{{$val->id}}").val();
        var xx@php echo $val->id; @endphp = $("#rate{{$val->id}}").find(":selected").val();
        var myarr@php echo $val->id; @endphp = xx@php echo $val->id; @endphp.split("*");
        var gain@php echo $val->id; @endphp =  parseFloat(from_amount@php echo $val->id; @endphp)*parseFloat(myarr@php echo $val->id; @endphp[1].split("<"));
        if(parseFloat(from_amount@php echo $val->id; @endphp)<parseFloat(min@php echo $val->id; @endphp)){
          //$("#from_amount{{$val->id}}").val(Math.round(min@php echo $val->id; @endphp));
          $("#from_amount{{$val->id}}").addClass('is-invalid');
          $("#invalid-feedback-from{{$val->id}}").show();
        }else if(parseFloat(from_amount@php echo $val->id; @endphp)>parseFloat(max@php echo $val->id; @endphp)){
          //$("#from_amount{{$val->id}}").val(Math.round(max@php echo $val->id; @endphp));
          $("#from_amount{{$val->id}}").addClass('is-invalid');
          $("#invalid-feedback-from{{$val->id}}").show();
        }else if(parseFloat(from_amount@php echo $val->id; @endphp)<=parseFloat(max@php echo $val->id; @endphp) || parseFloat(from_amount@php echo $val->id; @endphp)>=parseFloat(min@php echo $val->id; @endphp)){
          $("#from_amount{{$val->id}}").removeClass('is-invalid');
          $("#to_amount{{$val->id}}").removeClass('is-invalid');
          $("#invalid-feedback-from{{$val->id}}").hide();
          $("#invalid-feedback-to{{$val->id}}").hide();
          $("#to_amount{{$val->id}}").val(Math.round(gain@php echo $val->id; @endphp));
        }
            $("#dd{{$val->id}}").text(myarr@php echo $val->id; @endphp[1].split("<")+' '+myarr@php echo $val->id; @endphp[3].split("<"));
            $("#cc{{$val->id}}").text(myarr@php echo $val->id; @endphp[4].split("<")+' '+myarr@php echo $val->id; @endphp[3].split("<"));
      }
      $("#from_amount{{$val->id}}").change(convert@php echo $val->id; @endphp);
      convert@php echo $val->id; @endphp();
    </script>
    <script>
      "use strict";
      function convertalt@php echo $val->id; @endphp(){
        var from_amount@php echo $val->id; @endphp = $("#from_amount{{$val->id}}").val();
        var to_amount@php echo $val->id; @endphp = $("#to_amount{{$val->id}}").val();
        var xx@php echo $val->id; @endphp = $("#rate{{$val->id}}").find(":selected").val();
        var myarr@php echo $val->id; @endphp = xx@php echo $val->id; @endphp.split("*");
        var min@php echo $val->id; @endphp = parseFloat(myarr@php echo $val->id; @endphp[1].split("<"))*parseFloat($("#min_swap{{$val->id}}").val());
        var max@php echo $val->id; @endphp = parseFloat(myarr@php echo $val->id; @endphp[1].split("<"))*parseFloat($("#max_swap{{$val->id}}").val());
        var gain@php echo $val->id; @endphp =  parseFloat(to_amount@php echo $val->id; @endphp)/parseFloat(myarr@php echo $val->id; @endphp[1].split("<"));
        if(parseFloat(to_amount@php echo $val->id; @endphp)<parseFloat(min@php echo $val->id; @endphp)){
          //$("#to_amount{{$val->id}}").val(Math.round(min@php echo $val->id; @endphp));
          $("#to_amount{{$val->id}}").addClass('is-invalid');
          $("#invalid-feedback-to{{$val->id}}").show();
        }else if(parseFloat(to_amount@php echo $val->id; @endphp)>parseFloat(max@php echo $val->id; @endphp)){
          //$("#to_amount{{$val->id}}").val(Math.round(max@php echo $val->id; @endphp));
          $("#to_amount{{$val->id}}").addClass('is-invalid');
          $("#invalid-feedback-to{{$val->id}}").show();
        }else if(parseFloat(to_amount@php echo $val->id; @endphp)<=parseFloat(max@php echo $val->id; @endphp) || parseFloat(to_amount@php echo $val->id; @endphp)>=parseFloat(min@php echo $val->id; @endphp)){
          $("#to_amount{{$val->id}}").removeClass('is-invalid');
          $("#from_amount{{$val->id}}").removeClass('is-invalid');
          $("#invalid-feedback-to{{$val->id}}").hide();
          $("#invalid-feedback-from{{$val->id}}").hide();
          $("#from_amount{{$val->id}}").val(Math.round(gain@php echo $val->id; @endphp));
        }
        $("#dd{{$val->id}}").text(myarr@php echo $val->id; @endphp[1].split("<")+' '+myarr@php echo $val->id; @endphp[3].split("<"));
        $("#cc{{$val->id}}").text(myarr@php echo $val->id; @endphp[4].split("<")+' '+myarr@php echo $val->id; @endphp[3].split("<"));
      }
      $("#to_amount{{$val->id}}").change(convertalt@php echo $val->id; @endphp);
    </script>
    <script>
    "use strict";
    function convertselect@php echo $val->id; @endphp(){
      var from_amount@php echo $val->id; @endphp = $("#from_amount{{$val->id}}").val();
      var to_amount@php echo $val->id; @endphp = $("#to_amount{{$val->id}}").val();
      var xx@php echo $val->id; @endphp = $("#rate{{$val->id}}").find(":selected").val();
      var myarr@php echo $val->id; @endphp = xx@php echo $val->id; @endphp.split("*");
      var gain@php echo $val->id; @endphp =  parseFloat(from_amount@php echo $val->id; @endphp)*parseFloat(myarr@php echo $val->id; @endphp[1].split("<"));
      $("#to_amount{{$val->id}}").val(Math.round(gain@php echo $val->id; @endphp));
      $("#dd{{$val->id}}").text(myarr@php echo $val->id; @endphp[1].split("<")+' '+myarr@php echo $val->id; @endphp[3].split("<"));
      $("#cc{{$val->id}}").text(myarr@php echo $val->id; @endphp[4].split("<")+' '+myarr@php echo $val->id; @endphp[3].split("<"));
    }
    $("#rate{{$val->id}}").change(convertselect@php echo $val->id; @endphp);
    </script>
    <script>
    "use strict"
    $('#ggglogin{{$val->id}}').on('click',function()
    {
      $(this).text('Please wait ...').attr('disabled','disabled');
      $('#payment-form{{$val->id}}').submit();
    });
    </script>
  @endforeach
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