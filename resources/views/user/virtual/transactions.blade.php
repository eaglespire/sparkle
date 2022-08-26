@extends('userlayout')

@section('content')
<!-- Page content -->
<div class="container-fluid mt--6">
  <div class="content-wrapper mt-3">
    <div class="row">
      <div class="col-md-4">
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
        <div class="card mb-3">
          <div class="card-body">
            <h3 class="text-dark mb-2">{{__('Manage card')}}</h3>
            @if($val->status==1)
            <a data-toggle="modal" data-target="#modal-formfund{{$val->card_hash}}" href="" class="dropdown-item"><i class="fal fa-money-bill-wave-alt"></i> {{__('Fund Card')}}</a>
            <a data-toggle="modal" data-target="#modal-formwithdraw{{$val->card_hash}}" href="" class="dropdown-item"><i class="fal fa-arrow-circle-down"></i> {{__('Withdraw Money')}}</a>
            <a href="{{route('admin.terminate.virtual', ['id'=>$val->card_hash])}}" class="dropdown-item"><i class="fal fa-times"></i> {{__('Terminate')}}</a>
            <a href="{{route('admin.block.virtual', ['id'=>$val->card_hash])}}" class="dropdown-item"><i class="fal fa-ban"></i> {{__('Block')}}</a>
            @elseif($val->status==2)
            <a href="{{route('admin.unblock.virtual', ['id'=>$val->card_hash])}}" class="dropdown-item"><i class="fal fa-check"></i> {{__('Unblock')}}</a>
            @endif
          </div>
        </div>
        <div class="">
          <ul class="list-group list-group-flush list my--3">
            <li class="list-group-item bg-transparent">
              <div class="row align-items-center">
                <div class="col ml--2">
                  <p class="mb-0">{{__('Card Name')}}</p>
                </div>
                <div class="col-auto">
                  <h4 class="castro-copy" data-clipboard-text="{{$val->name_on_card}}">{{$val->name_on_card}} <i class="fal fa-copy"></i></h4>
                </div>
              </div>
            </li>
            <li class="list-group-item bg-transparent">
              <div class="row align-items-center">
                <div class="col ml--2">
                  <p class="mb-0">{{__('Card Number')}}</p>
                </div>
                <div class="col-auto">
                  <h4 class="castro-copy" data-clipboard-text="{{preg_replace('~^.{4}|.{4}(?!$)~', '$0 ', $val->card_pan)}}">{{preg_replace('~^.{4}|.{4}(?!$)~', '$0 ', $val->card_pan)}} <i class="fal fa-copy"></i></h4>
                </div>
              </div>
            </li>
            <li class="list-group-item bg-transparent">
              <div class="row align-items-center">
                <div class="col ml--2">
                  <p class="mb-0">{{__('Valid Till')}}</p>
                </div>
                <div class="col-auto">
                  <h4>{{$val->expiration}}</h4>
                </div>
              </div>
            </li>
            <li class="list-group-item bg-transparent">
              <div class="row align-items-center">
                <div class="col ml--2">
                  <p class="mb-0">{{__('Cvv')}}</p>
                </div>
                <div class="col-auto">
                  <h4>{{$val->cvv}}</h4>
                </div>
              </div>
            </li>
            <li class="list-group-item bg-transparent">
              <div class="row align-items-center">
                <div class="col ml--2">
                  <p class="mb-0">{{__('State')}}</p>
                </div>
                <div class="col-auto">
                  <h4>{{$val->state}}</h4>
                </div>
              </div>
            </li>
            <li class="list-group-item bg-transparent">
              <div class="row align-items-center">
                <div class="col ml--2">
                  <p class="mb-0">{{__('City')}}</p>
                </div>
                <div class="col-auto">
                  <h4>{{$val->city}}</h4>
                </div>
              </div>
            </li>
            <li class="list-group-item bg-transparent">
              <div class="row align-items-center">
                <div class="col ml--2">
                  <p class="mb-0">{{__('Zip Code')}}</p>
                </div>
                <div class="col-auto">
                  <h4>{{$val->zip_code}}</h4>
                </div>
              </div>
            </li>
            <li class="list-group-item bg-transparent">
              <div class="row align-items-center">
                <div class="col ml--2">
                  <p class="mb-0">{{__('Address')}}</p>
                </div>
                <div class="col-auto">
                  <h4>{{$val->address}}</h4>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
      <div class="col-md-8">
        <div class="card">
          <div class="card-header mb-0">
            <h3 class="text-dark mb-0">Transactions</h3>
          </div>
          <div class="table-responsive py-4">
            <table class="table table-flush" id="example">
              <thead>
                <tr>
                  <th>{{__('Description')}}</th>
                  <th>{{__('Amount')}}</th>
                  <th>{{__('Created')}}</th>
                </tr>
              </thead>
              <tbody>
                @foreach($log as $k=>$val)
                <tr>
                  <td>{{$val->description}}</td>
                  <td class="@if($val->type=='Credit')text-success @else text-danger @endif">{{$val->card->getCurrency->real->currency.number_format($val->amount, 2)}}</td>
                  <td>{{date("Y/m/d h:i:A", strtotime($val->created_at))}}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
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
    @stop
    @section('script')
    <script src="{{asset('asset/dashboard/vendor/datatables.net/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('asset/dashboard/vendor/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.0/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.0/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.0/js/buttons.print.min.js"></script>
    <script>
      $(document).ready(function() {
        $('#example').DataTable({
          "language": {
            "lengthMenu": "Show _MENU_",
          },
          "dom": "<'row'" +
            "<'col-md-12 col-12 d-flex mb-2'B>" +
            "<'col-md-12 col-12 d-flex'f>" +
            ">" +

            "<'table-responsive'tr>" +

            "<'row'" +
            "<'col-sm-12 col-md-4 d-flex align-items-center justify-content-center justify-content-md-start'l>" +
            "<'col-sm-12 col-md-4 d-flex align-items-center justify-content-center justify-content-md-start'i>" +
            "<'col-sm-12 col-md-4 d-flex align-items-center justify-content-center justify-content-md-end'p>" +
            ">",
          buttons: [
            'copy', 'excel', 'pdf', 'print'
          ]
        });
      });
    </script>
    @endsection