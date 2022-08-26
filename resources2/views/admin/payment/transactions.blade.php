@extends('master')

@section('content')
<!-- Page content -->
<div class="container-fluid mt--6">
    <div class="content-wrapper mt-3">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <form class="navbar-search navbar-search-light" action="{{route('admin.transaction.search')}}" method="post" id="navbar-search-main">
                            @csrf
                            <div class="form-group mb-0">
                                <div class="input-group input-group-merge">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fal fa-search"></i></span>
                                    </div>
                                    <input class="form-control" placeholder="Email, amount, reference" name="search" type="text">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-flush" id="">
                    <thead>
                        <tr>
                            <th>SN</th>
                            <th class="text-center">@sortablelink('status', 'Status')</th>
                            <th class="text-center">@sortablelink('amount', 'Amount')</th>
                            <th class="text-center">@sortablelink('receiver_id', 'Merchant')</th>
                            <th class="text-center">@sortablelink('email', 'Customer')</th>
                            <th class="text-center">@sortablelink('type', 'Type')</th>
                            <th class="text-center">@sortablelink('ref_id', 'Reference')</th>
                            <th class="text-center">@sortablelink('created_at', 'Date')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($links as $k=>$val)
                        <tr>
                            <td data-href="{{route('admin.transactions', ['id' => $val->ref_id,'type' => 'analytics'])}}">
                                {{$loop->iteration}}.
                            </td>
                            <td data-href="{{route('admin.transactions', ['id' => $val->ref_id,'type' => 'analytics'])}}" class="text-center">
                                @if($val->status==0) <span class="badge badge-pill badge-primary"><i class="fal fa-sync"></i> Pending</span>
                                @elseif($val->status==1) <span class="badge badge-pill badge-success"><i class="fal fa-check"></i> Success</span>
                                @elseif($val->status==2) <span class="badge badge-pill badge-danger"><i class="fal fa-ban"></i> Failed/cancelled</span>
                                @elseif($val->status==3) <span class="badge badge-pill badge-info"><i class="fal fa-arrow-alt-circle-left"></i> Refunded</span>
                                @elseif($val->status==4) <span class="badge badge-pill badge-info"><i class="fal fa-arrow-alt-circle-left"></i> Reversed (Chargeback)</span>
                                @endif
                            </td>
                            <td data-href="{{route('admin.transactions', ['id' => $val->ref_id,'type' => 'analytics'])}}" class="text-center">
                                @if($val->client==0)
                                {{$val->getCurrency->real->currency.' '.number_format($val->amount, 2)}}
                                @else
                                {{$val->getCurrency->real->currency.' '.number_format($val->amount-$val->charge, 2)}}
                                @endif
                            </td>
                            <td>@if($val->receiver->first_name==null) [Deleted] @else <a href="{{route('user.manage', ['id'=>$val->receiver_id])}}">{{$val->receiver->first_name.' '.$val->receiver->last_name}}</a> @endif</td>
                            <td data-href="{{route('admin.transactions', ['id' => $val->ref_id,'type' => 'analytics'])}}" class="text-center">
                                @if($val->email!=null){{$val->email}}@else {{$val->receiver->email}} @endif
                            </td>
                            <td data-href="{{route('admin.transactions', ['id' => $val->ref_id,'type' => 'analytics'])}}" class="text-center">@if($val->type==1) Payment @elseif($val->type==2) API @elseif($val->type==3) Payout @elseif($val->type==4) Funding @elseif($val->type==5) Swapping @endif</td>
                            <td class="text-center castro-copy" data-clipboard-text="{{$val->ref_id}}">{{$val->ref_id}} <i class="fal fa-copy"></i></td>
                            <td data-href="{{route('admin.transactions', ['id' => $val->ref_id,'type' => 'analytics'])}}" class="text-center">{{date("Y/m/d h:i:A", strtotime($val->created_at))}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <p>Showing 1 to {{$links->count()}} of {{ $links->total() }} entries</p>
                        </div>
                        <div class="col-md-12 text-right">
                            {{ $links->onEachSide(1)->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @stop
        @section('script')
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