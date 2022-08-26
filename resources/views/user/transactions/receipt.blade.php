@extends('user.link.menu')

@section('content')

<div class="main-content">
  <div class="header py-5 pt-7">
    <div class="container">
      <div class="header-body text-center mb-7">
        <div class="row justify-content-center">

        </div>
      </div>
    </div>
  </div>
  <div class="container mt--8 pb-5 mb-0">
    <div class="row justify-content-center">
      <div class="col-md-7">
        <div class="card">
          <div class="card-header text-center">
            <h3 class="mb-1 mt-5 font-weight-bold">{{__('Receipt from')}} {{$set->site_name}} </h3>
            <span class="text-dark">{{__('Receipt')}} #{{$link->ref_id}} [{{__('PAID')}}]</span>
          </div>
          <div class="card-body px-lg-5 py-lg-5">
            <div class="row justify-content-between align-items-center">
              <div class="col">
                <div class="my-4">
                  <h3 class="mb-1 h5 text-dark">{{__('AMOUNT PAID')}}</h3>
                  <span class="text-dark">{{$link->getCurrency->real->currency_symbol.number_format($link->amount, 2)}}</span><br>
                </div>
              </div>
              <div class="col">
                <div class="my-4">
                  <h3 class="mb-1 h5 text-dark">{{__('DATE PAID')}}</h3>
                  <span class="text-dark">{{date("M j, Y", strtotime($link->created_at))}}</span><br>
                </div>
              </div>
              <div class="col">
                <div class="my-4">
                  <h3 class="mb-1 h5 text-dark">{{__('PAYMENT METHOD')}}</h3>
                  <span class="text-dark text-uppercase"> {{$link->payment_type}}</span><br>
                </div>
              </div>
            </div>
            <div class="row justify-content-between align-items-center">
              <div class="col">
                <div class="my-4">
                  <h3 class="mb-1 h5 text-dark">{{__('FOR')}}</h3>
                  @if($link->type!=4)
                  <span class="text-dark">Name:{{$link->first_name.' '.$link->last_name}}</span><br>
                  <span class="text-dark">Email:{{$link->email}}</span><br>
                  @else
                  <span class="text-dark">Name:{{$link->receiver->first_name.' '.$link->receiver->last_name}}</span><br>
                  <span class="text-dark">Email:{{$link->receiver->email}}</span><br>
                  @endif
                </div>
              </div>
            </div>
            <div class="row justify-content-between align-items-center">
              <div class="col">
                <div class="my-4">
                  <h3 class="mb-1 h5 text-dark">{{__('SUMMARY')}}</h3>
                  <table style="padding-left: 5px; padding-right:5px;" width="100%">
                    <tbody>
                      <tr>
                        <td class="Table-description Font Font--body" style="border: 0;border-collapse: collapse;margin: 0;padding: 0;-webkit-font-smoothing: antialiased;-moz-osx-font-smoothing: grayscale;mso-line-height-rule: exactly;vertical-align: middle;color: #525f7f;font-size: 15px;line-height: 24px; width: 100%; ">
                          @if($link->type==1)
                          {{__('Payment')}}
                          @elseif($link->type==2)
                          {{__('API')}}
                          @elseif($link->type==3)
                          {{__('Payout')}}                          
                          @elseif($link->type==4)
                          {{__('Funding')}}
                          @endif
                        </td>
                        <td class="Spacer Table-gap" width="8" style="border: 0;border-collapse: collapse;margin: 0;padding: 0;-webkit-font-smoothing: antialiased;-moz-osx-font-smoothing: grayscale;color: #ffffff;font-size: 1px;line-height: 1px;mso-line-height-rule: exactly;">&nbsp;</td>
                        <td class="Table-amount Font Font--body" align="right" valign="top" style="border: 0;border-collapse: collapse;margin: 0;padding: 0;-webkit-font-smoothing: antialiased;-moz-osx-font-smoothing: grayscale;mso-line-height-rule: exactly;vertical-align: middle;color: #525f7f;font-size: 15px;line-height: 24px;">
                        @if($link->client==0)
                          <span class="text-dark">{{$link->getCurrency->real->currency_symbol.number_format($link->amount, 2)}}</span><br>
                          @else
                          <span class="text-dark">{{$link->getCurrency->real->currency_symbol.number_format($link->amount-$link->charge, 2)}}</span><br>
                          @endif
                        </td>
                      </tr>
                      @if($link->client==1 || $link->type==4)
                      <tr>
                        <td class="Table-description Font Font--body" style="border: 0;border-collapse: collapse;margin: 0;padding: 0;-webkit-font-smoothing: antialiased;-moz-osx-font-smoothing: grayscale;mso-line-height-rule: exactly;vertical-align: middle;color: #525f7f;font-size: 15px;line-height: 24px; width: 100%; ">
                          <strong>{{__('Amount charged')}}</strong>
                        </td>
                        <td class="Spacer Table-gap" width="8" style="border: 0;border-collapse: collapse;margin: 0;padding: 0;-webkit-font-smoothing: antialiased;-moz-osx-font-smoothing: grayscale;color: #ffffff;font-size: 1px;line-height: 1px;mso-line-height-rule: exactly;">&nbsp;</td>
                        <td class="Table-amount Font Font--body" align="right" valign="top" style="border: 0;border-collapse: collapse;margin: 0;padding: 0;-webkit-font-smoothing: antialiased;-moz-osx-font-smoothing: grayscale;mso-line-height-rule: exactly;vertical-align: middle;color: #525f7f;font-size: 15px;line-height: 24px;">
                          <strong>{{$link->getCurrency->real->currency_symbol.number_format($link->charge, 2)}}</strong>
                        </td>
                      </tr>
                      @endif
                    </tbody>
                  </table>
                  <hr>
                </div>
              </div>
            </div>
            <div class="row justify-content-between align-items-center">
              <div class="col">
                <div class="my-4">
                  <p class="text-dark">{{__('If you have any questions contact us at')}} <a href="mailto:{{$set->email}}">{{$set->email}}</a> {{__('or call at')}}. <a href="{{route('download.receipt', ['id' => $link->ref_id])}}">{{__('Click here')}}</a> {{__('to download receipt')}}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row justify-content-center mt-5">
      <a href="{{route('login')}}" class="text-white"><i class="fal fa-times"></i> {{__('Close Browser')}}</a>
    </div>
  </div>
  @stop