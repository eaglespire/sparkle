@extends('userlayout')

@section('content')
<div class="container-fluid mt--6">
  <div class="content-wrapper">
    <div class="row align-items-center py-4">
      <div class="col-lg-6 col-md-12 mb-2">
        <form class="navbar-search navbar-search-light" action="{{route('search')}}" method="post" id="navbar-search-main">
          @csrf
          <div class="form-group mb-0">
            <div class="input-group input-group-merge">
              <div class="input-group-prepend">
                <span class="input-group-text"><i class="fal fa-search"></i></span>
              </div>
              <input class="form-control" placeholder="{{__('Search name or description')}}" name="search" type="text">
            </div>
          </div>
        </form>
      </div>
      <div class="col-lg-6 col-md-12 text-md-end">
        <a data-toggle="modal" data-target="#filter" href="" class="btn btn-white"><i class="fal fa-filter"></i> {{__('Filter')}}</a>
        <a data-toggle="modal" data-target="#new" href="" class="btn btn-neutral"><i class="fal fa-angle-double-down"></i> {{__('Request Payment')}}</a>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="modal fade" id="filter" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h3 class="mb-0 font-weight-bolder">{{__('Filter Options')}}</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form action="{{route('payment.sort')}}" method="post">
                <div class="modal-body">
                  @csrf
                  <div class="form-group align-items-center">
                    <label class="form-control-label">{{__('Date Range')}}</label>
                    <input class="form-control" placeholder="{{__('Pick date rage')}}" value="{{$order}}" name="date">
                  </div>
                  <div class="form-group">
                    <label class="form-control-label">{{__('Status')}}</label>
                    <select class="form-control" name="status">
                      <option value="2" @if($status==2) selected @endif>{{__('All')}}</option>
                      <option value="1" @if($status==1) selected @endif>{{__('Active')}}</option>
                      <option value="0" @if($status==0) selected @endif>{{__('Disabled')}}</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label class="form-control-label">{{__('Currency')}}</label>
                    <select class="form-control" name="currency">
                      <option value="0" @if($limit=="All" ) selected @endif>{{__('All')}}</option>
                      @if(count(getAcceptedCountry())>0)
                      @foreach(getAcceptedCountry() as $val)
                      <option value="{{$val->id}}" @if($currency==$val->id) selected @endif>{{$val->real->currency}}</option>
                      @endforeach
                      @endif
                    </select>
                  </div>
                  <div class="form-group">
                    <label class="form-control-label">{{__('Limit')}}</label>
                    <select class="form-control" name="limit">
                      <option value="10" @if($limit==10) selected @endif>{{__('10')}}</option>
                      <option value="25" @if($limit==25) selected @endif>{{__('25')}}</option>
                      <option value="50" @if($limit==50) selected @endif>{{__('50')}}</option>
                      <option value="100" @if($limit==100) selected @endif>{{__('100')}}</option>
                    </select>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="submit" class="btn btn-neutral btn-block">{{__('Apply')}}</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <div class="modal fade" id="new" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h3 class="mb-0 font-weight-bolder">{{__('Request Payment')}}</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form action="{{route('submit.payment')}}" method="post">
                <div class="modal-body">
                  @csrf
                  <div class="form-group row">
                    <div class="col-lg-12">
                      <input type="text" name="name" class="form-control" placeholder="{{__('Name of your Page')}}" required>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-lg-12">
                      <div class="input-group">
                        <input type="number" step="any" class="form-control" autocomplete="off" id="amount" name="amount" placeholder="{{__('How much?')}}">
                        <span class="input-group-append">
                          <select class="form-control select" style="padding: 0.35rem 2rem 0.35rem;" id="currency" name="currency" required>
                            @if(count(getAcceptedCountry())>0)
                            @foreach(getAcceptedCountry() as $val)
                            <option value="{{$val->id}}*{{$val->min_amount}}*@if($val->max_amount==null)empty @else{{$val->max_amount}}@endif">{{$val->real->emoji.' '.$val->real->currency}}</option>
                            @endforeach
                            @endif
                          </select>
                        </span>
                      </div>
                      <span class="form-text">{{__('Leave empty to allow customers enter desired amount')}}</span>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-lg-12">
                      <textarea type="text" name="description" placeholder="{{__('Tell your customer why you are requesting this payment')}}" rows="5" class="form-control" required></textarea>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="submit" class="btn btn-neutral btn-block">{{__('Create Payment')}}</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      @if(count($links)>0)
      @foreach($links as $k=>$val)
      <div class="col-md-4">
        <div class="card bg-white">
          <!-- Card body -->
          <div class="card-body">
            <div class="row mb-2">
              <div class="col-8">
                <h5 class="h3 font-weight-bolder">@if(strlen($val->name)>10){{substr($val->name,0, 10)}}..@else {{$val->name}} @endif</h5>
              </div>
              <div class="col-4 text-right">
                <a class="mr-0 text-dark" data-toggle="dropdown" aria-haspopup="true" aria-expanded="fadse">
                  <i class="fal fa-chevron-circle-down"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-left">
                  <a class="dropdown-item" target="_blank" href="{{route('payment.link', ['id' => $val->ref_id])}}"><i class="fal fa-eye"></i>{{__('Preview')}}</a>
                  @if($val->active==1)
                  <a class='dropdown-item' href="{{route('payment.disable', ['id' => $val->ref_id])}}"><i class="fal fa-ban"></i>{{ __('Disable')}}</a>
                  @else
                  <a class='dropdown-item' href="{{route('payment.enable', ['id' => $val->ref_id])}}"><i class="fal fa-check"></i>{{ __('Activate')}}</a>
                  @endif
                  <a class="dropdown-item" href="{{route('payment.transactions', ['id' => $val->ref_id])}}"><i class="fal fa-sync"></i>{{__('Transactions')}}</a>
                  <a class="dropdown-item" data-toggle="modal" data-target="#edit{{$val->id}}" href="#"><i class="fal fa-pencil"></i>{{__('Edit')}}</a>
                  <a class="dropdown-item" data-toggle="modal" data-target="#share{{$val->id}}" href="#"><i class="fal fa-share"></i>{{__('Share')}}</a>
                  <a class="dropdown-item" data-toggle="modal" data-target="#delete{{$val->id}}" href=""><i class="fal fa-trash"></i>{{__('Delete')}}</a>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col">
                <p>{{__('Amount')}}: @if($val->amount==null) Not fixed [{{$val->getCurrency->real->currency}}]@else {{$val->getCurrency->real->currency_symbol.number_format($val->amount, 2)}} @endif</p>
                <p class="mb-2">{{__('Created')}}: {{date("j, M Y", strtotime($val->created_at))}}</p>
                @if($val->active==1)
                <span class="badge badge-pill badge-success"><i class="fal fa-check"></i> {{__('Active')}}</span>
                @else
                <span class="badge badge-pill badge-danger"><i class="fal fa-ban"></i> {{__('Disabled')}}</span>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal fade" id="edit{{$val->id}}" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="mb-0 font-weight-bolder">{{__('Edit Payment')}}</h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form action="{{route('update.payment', ['id' => $val->ref_id])}}" method="post">
              <div class="modal-body">
                @csrf
                <div class="form-group row">
                  <div class="col-lg-12">
                    <input type="text" name="name" class="form-control" value="{{$val->name}}" placeholder="{{__('Name of your Page')}}" required>
                  </div>
                </div>
                <div class="form-group row">
                  <div class="col-lg-12">
                    <div class="input-group">
                      <input type="number" step="any" class="form-control" id="amount" autocomplete="off" name="amount" value="{{$val->amount}}" placeholder="How much?">
                      <span class="input-group-append">
                        <select class="form-control select" style="padding: 0.35rem 2rem 0.35rem;" id="currency" name="currency" required>
                          @if(count(getAcceptedCountry())>0)
                          @foreach(getAcceptedCountry() as $dval)
                          <option value="{{$dval->id}}*{{$dval->min_amount}}*@if($dval->max_amount==null)empty @else{{$dval->max_amount}}@endif" @if($val->currency==$dval->id)selected @endif>{{$dval->real->emoji.' '.$dval->real->currency}}</option>
                          @endforeach
                          @endif
                        </select>
                      </span>
                    </div>
                    <span class="form-text">{{__('Leave empty to allow customers enter desired amount')}}</span>
                  </div>
                </div>
                <div class="form-group row">
                  <div class="col-lg-12">
                    <textarea type="text" name="description" rows="5" class="form-control" placeholder="{{__('Tell your customer why you are requesting this payment')}}" required>{{$val->description}}</textarea>
                  </div>
                </div>
                <input type="hidden" name="id" value="{{$val->id}}">
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-neutral btn-block">{{__('Update Payment')}}</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <div class="modal fade" id="delete{{$val->id}}" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="mb-0 font-weight-bolder">{{__('Delete Payment')}}</h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <p>{{__('Are you sure you want to delete this?, all transaction related to this payment link will also be deleted')}}</p>
            </div>
            <div class="modal-footer">
              <a href="{{route('delete.payment', ['id' => $val->ref_id])}}" class="btn btn-danger btn-block">{{__('Proceed')}}</a>
            </div>
          </div>
        </div>
      </div>
      <div class="modal fade" id="share{{$val->id}}" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
        <div class="modal-dialog modal- modal-dialog-centered modal-md" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="mb-0 font-weight-bolder">{{__('Share')}}</h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form>
                <div class="text-center">
                  {!! QrCode::eye('circle')->style('round')->size(250)->generate(route('payment.link', ['id' => $val->ref_id])); !!}
                </div>
                <div class="text-center mb-3 mt-3">
                  <p>{{__('Scan QR code or Share using:')}}</p>
                </div>
                <div class="form-group">
                  <div class="input-group">
                    <input type="text" class="form-control" value="{{route('payment.link', ['id' => $val->ref_id])}}">
                    <div class="input-group-append">
                      <span class="input-group-text castro-copy" data-clipboard-text="{{route('payment.link', ['id' => $val->ref_id])}}" title="Copy to clipboard"><i class="fal fa-copy"></i></span>
                    </div>
                  </div>
                </div>
                <div class="text-center">
                  <a href="https://wa.me/?text={{route('payment.link', ['id' => $val->ref_id])}}" target="_blank" class="btn btn-neutral btn-icon-only">
                    <span class="btn-inner--icon"><i class="fab fa-whatsapp"></i></span>
                  </a>
                  <a href="https://www.facebook.com/sharer/sharer.php?u={{route('payment.link', ['id' => $val->ref_id])}}" target="_blank" class="btn btn-neutral btn-icon-only">
                    <span class="btn-inner--icon"><i class="fab fa-facebook"></i></span>
                  </a>
                  <a href="https://twitter.com/intent/tweet?text={{route('payment.link', ['id' => $val->ref_id])}}" target="_blank" class="btn btn-neutral btn-icon-only">
                    <span class="btn-inner--icon"><i class="fab fa-twitter"></i></span>
                  </a>
                  <a href="mailto:?body={{route('payment.link', ['id' => $val->ref_id])}}" class="btn btn-neutral btn-icon-only">
                    <span class="btn-inner--icon"><i class="fal fa-envelope"></i></span>
                  </a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      @endforeach
      @else
      <div class="col-md-12 mb-5">
        <div class="text-center mt-8">
          <div class="btn-wrapper text-center mb-3">
            <a href="javascript:void;" class="mb-3">
              <span class=""><i class="fal fa-link fa-4x text-info"></i></span>
            </a>
          </div>
          <h3 class="text-dark">{{__('No Payment Link Found')}}</h3>
          <p class="text-dark">{{__('We couldn\'t find any payment to this account')}}</p>
        </div>
      </div>
      @endif
    </div>
    <div class="row align-items-center justify-content-lg-between mt-5">
        <div class="col-md-12">
          <p>{{__('Showing 1 to')}} {{$links->count()}} of {{ $links->total() }} {{__('entries')}}</p>
          {{ $links->onEachSide(2)->links('pagination::bootstrap-4')}}
        </div>
      </div>
    @stop
    @section('script')
    <script>
      "use strict";

      function currency() {
        var xx = $("#currency").find(":selected").val();
        var myarr = xx.split("*");
        $("#amount").attr("min", myarr[1].split("<"));
        if (myarr[1].split("<") != "empty") {
          $("#amount").attr("max", myarr[2].split("<"));
        }
      }
      $("#currency").change(currency);
      currency();
      $('input[name="date"]').daterangepicker();
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