@extends('userlayout')

@section('content')
<!-- Page content -->
<div class="container-fluid mt--6">
    <div class="content-wrapper mt-3">
        <div class="row">
            <div class="col-md-12">
                <div class="nav-wrapper">
                    <ul class="nav nav-pills nav-fill nav-line-tabs nav-line-tabs-2x nav-stretch" id="tabs-icons-text" role="tablist">
                        @foreach(getAcceptedCountry() as $val)
                        <li class="nav-item">
                            <a class="nav-link mb-sm-3 mb-md-0 @if($balance==$user->getBalance($val->id)->ref_id) active @endif" id="tabs-icons-text-{{$val->id}}-tab" href="{{route('user.transactions', ['balance'=>$user->getBalance($val->id)->ref_id])}}" role="tab" aria-controls="tabs-icons-text-{{$val->id}}" aria-selected="true">{{$val->real->emoji.' '.$val->real->currency}}</a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="tab-content" id="myTabContent">
            @foreach(getAcceptedCountry() as $val)
            <div class="tab-pane fade @if($balance==$user->getBalance($val->id)->ref_id) show active @endif" id="tabs-icons-text-{{$val->id}}" role="tabpanel" aria-labelledby="tabs-icons-text-{{$val->id}}-tab">
                <div class="card">
                    <div class="table-responsive py-4">
                        <table class="table table-flush" id="example{{$val->id}}">
                            <thead>
                                <tr>
                                    <th>SN</th>
                                    <th class="text-center">{{__('Status')}}</th>
                                    <th class="text-center">{{__('Amount')}}</th>
                                    <th class="text-center">{{__('Customer')}}</th>
                                    <th class="text-center">{{__('Type')}}</th>
                                    <th class="text-center">{{__('Reference')}}</th>
                                    <th class="text-center">{{__('Date')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->getUniqueTransactions($val->id) as $k=>$val)
                                @if($user->live==1)
                                    @if($val->payment_type=="card")
                                        @php $type="analytics" @endphp
                                    @else
                                        @php $type="webhook" @endphp
                                    @endif
                                @else
                                    @php $type="webhook" @endphp
                                @endif
                                <tr>
                                    <td data-href="{{route('view.transactions', ['id' => $val->ref_id,'type' => $type])}}">
                                        {{$loop->iteration}}.
                                    </td>
                                    <td data-href="{{route('view.transactions', ['id' => $val->ref_id,'type' => $type])}}" class="text-center">
                                        @if($val->status==0) <span class="badge badge-pill badge-primary"><i class="fal fa-sync"></i> Pending</span>
                                        @elseif($val->status==1) <span class="badge badge-pill badge-success"><i class="fal fa-check"></i> Success</span>
                                        @elseif($val->status==2) <span class="badge badge-pill badge-danger"><i class="fal fa-ban"></i> Failed/cancelled</span>
                                        @elseif($val->status==3) <span class="badge badge-pill badge-info"><i class="fal fa-arrow-alt-circle-left"></i> Refunded</span>
                                        @elseif($val->status==4) <span class="badge badge-pill badge-info"><i class="fal fa-arrow-alt-circle-left"></i> Reversed (Chargeback)</span>
                                        @endif
                                    </td>
                                    <td data-href="{{route('view.transactions', ['id' => $val->ref_id,'type' => $type])}}" class="text-center">
                                        @if($val->client==0)
                                        {{$val->getCurrency->real->currency.' '.number_format($val->amount, 2)}}
                                        @else
                                        {{$val->getCurrency->real->currency.' '.number_format($val->amount-$val->charge, 2)}}
                                        @endif
                                    </td>
                                    <td data-href="{{route('view.transactions', ['id' => $val->ref_id,'type' => $type])}}" class="text-center">
                                        @if($val->email!=null){{$val->email}}@else {{$user->email}} @endif
                                    </td>
                                    <td data-href="{{route('view.transactions', ['id' => $val->ref_id,'type' => $type])}}" class="text-center">
                                        @if($val->type==1) Payment 
                                        @elseif($val->type==2) API 
                                        @elseif($val->type==3) Payout 
                                        @elseif($val->type==4) Funding 
                                        @elseif($val->type==5) Swapping 
                                        @endif
                                    </td>
                                    <td class="text-center castro-copy" data-clipboard-text="{{$val->ref_id}}">{{$val->ref_id}} <i class="fal fa-copy"></i></td>
                                    <td data-href="{{route('view.transactions', ['id' => $val->ref_id,'type' => $type])}}" class="text-center">{{date("Y/m/d h:i:A", strtotime($val->created_at))}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endforeach
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
        @foreach(getAcceptedCountry() as $val)
        <script>
            $(document).ready(function() {
                $('#example{{$val->id}}').DataTable({
                        "language": {
                            "lengthMenu": "Show _MENU_",
                            },
                        "dom":
                        "<'row'" +
                        "<'col-md-6 col-12 d-flex align-items-center justify-conten-start mb-2'B>" +
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
                    }
                );
            });
            function withdraw@php echo $val->id; @endphp() {
                var amount@php echo $val->id; @endphp = $("#amount{{$val->id}}").val();
                var percent@php echo $val->id; @endphp = $("#withdraw_percent_charge{{$val->id}}").val();
                var fiat@php echo $val->id; @endphp = $("#withdraw_fiat_charge{{$val->id}}").val();
                var receive@php echo $val->id; @endphp = parseFloat(amount@php echo $val->id; @endphp)-parseFloat(fiat@php echo $val->id; @endphp)-(parseFloat(amount@php echo $val->id; @endphp)*parseFloat(percent@php echo $val->id; @endphp)/100);
                var charge@php echo $val->id; @endphp = parseFloat(fiat@php echo $val->id; @endphp)+(parseFloat(amount@php echo $val->id; @endphp)*parseFloat(percent@php echo $val->id; @endphp)/100);
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
        @endforeach
        <script>
            $('td[data-href]').on("click", function() {
                window.location.href = $(this).data('href');
            });
        </script>
        <script>
            'use strict';
            var clipboard = new ClipboardJS('.castro-copy');

            clipboard.on('success', function(e) {
                navigator.clipboard.writeText(e.text);
                $(e.trigger)
                    .attr('title', 'Copied!')
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