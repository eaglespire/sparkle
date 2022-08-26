@extends('userlayout')

@section('content')

<!-- Page content -->
<div class="container-fluid mt--6">
  <div class="content-wrapper mt-3">
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
    @elseif($user->business()->kyc_status=="PROCESSING")
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
    <a href="#" data-toggle="modal" data-target="#buy" class="btn btn-neutral mb-3">{{__('New card')}}</a>
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
                    <input type="number" step="any" class="form-control" autocomplete="off" id="amount" name="amount" placeholder="{{__('How much?')}}">
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
    <div class="card">
      <div class="table-responsive py-4">
        <table class="table table-flush" id="example">
          <thead>
            <tr>
              <th>{{__('S/N')}}</th>
              <th class="text-center">{{__('Card Holder')}}</th>
              <th class="text-center">{{__('Card Balance')}}</th>
              <th class="text-center">{{__('Card Number')}}</th>
              <th class="text-center">{{__('Status')}}</th>
              <th class="text-center">{{__('Created')}}</th>
            </tr>
          </thead>
          <tbody>
            @foreach($user->getVcard() as $k=>$val)
            <tr>
              <td>{{++$k}}.</td>
              <td data-href="{{route('transactions.virtual', ['id'=>$val->card_hash])}}" class="text-center">{{$val->name_on_card}}</td>
              <td data-href="{{route('transactions.virtual', ['id'=>$val->card_hash])}}" class="text-center">{{$val->getCurrency->real->currency.number_format($val->amount, 2)}}</td>
              <td data-href="{{route('transactions.virtual', ['id'=>$val->card_hash])}}" class="text-center">
                @if($val->card_type=="mastercard")
                <img style="height:auto; max-width:10%" src="{{asset('asset/images/mastercard.png')}}">
                @else
                <img style="height:auto; max-width:30px" src="{{asset('asset/images/visa.png')}}">
                @endif
                {{substr($val->card_pan,6,4)}}
              </td>
              
              <td data-href="{{route('transactions.virtual', ['id'=>$val->card_hash])}}" class="text-center">
                @if($val->status==1) <span class="badge badge-pill badge-success">Active</span> @elseif($val->status==2) <span class="badge badge-pill badge-danger">Blocked</span>@endif
              </td>
              <td data-href="{{route('transactions.virtual', ['id'=>$val->card_hash])}}" class="text-center">{{date("Y/m/d", strtotime($val->created_at))}}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
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
            "<'col-md-6 col-12 d-flex align-items-center justify-conten-start mb-2'>" +
            "<'col-md-6 col-12 d-flex align-items-center justify-content-end'f>" +
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
      $("#amount").keyup(currency);
      currency();
    </script>
    <script>
      $('td[data-href]').on("click", function() {
        window.location.href = $(this).data('href');
      });
    </script>
    @endsection