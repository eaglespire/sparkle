@extends('userlayout')
@section('content')
<div class="container-fluid mt--6">
    <div class="content-wrapper">
        <div class="row">
            <div class="@if($val->type!=3 && $val->type!=5)col-md-6 @else col-md-12 @endif">
                <div class="card-body">
                    <a href="{{route('user.transactions', ['balance'=>$user->getBalance($val->currency)->ref_id])}}" class="btn btn-neutral mb-3"><i class="fal fa-caret-left"></i> {{__('Go back')}}</a>
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <p class="text-dark">{{__('Amount')}}</p>
                            <h1 class="fw-bold">
                                @if($val->client==0)
                                {{$val->getCurrency->real->currency.number_format($val->amount, 2)}}
                                @else
                                {{$val->getCurrency->real->currency.number_format($val->amount-$val->charge, 2)}}
                                @endif
                            </h1>
                        </div>
                        <div class="col-md-4 text-right">
                            @if($val->status==0) <span class="badge badge-pill badge-primary"><i class="fal fa-sync"></i> {{__('Pending')}}</span>
                            @elseif($val->status==1) <span class="badge badge-pill badge-success"><i class="fal fa-check"></i> {{__('Success')}}</span>
                            @elseif($val->status==2) <span class="badge badge-pill badge-danger"><i class="fal fa-ban"></i> {{__('Failed/cancelled')}}</span>
                            @elseif($val->status==3) <span class="badge badge-pill badge-info"><i class="fal fa-arrow-alt-circle-left"></i> {{__('Refunded')}}</span>
                            @elseif($val->status==4) <span class="badge badge-pill badge-info"><i class="fal fa-arrow-alt-circle-left"></i> {{__('Reversed (Chargeback)')}}</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            @if($val->type!=3)
                            <ul class="list-group list-group-flush list my--3">
                                <li class="list-group-item bg-transparent">
                                    <div class="row align-items-center">
                                        <div class="col ml--2">
                                            <p class="mb-0">{{__('Transaction Reference')}}</p>
                                        </div>
                                        <div class="col-auto">
                                            <h4 class="castro-copy" data-clipboard-text="{{$val->ref_id}}">{{$val->ref_id}} <i class="fal fa-copy"></i></h4>
                                        </div>
                                    </div>
                                </li>
                                @if($val->type==1)
                                <li class="list-group-item bg-transparent">
                                    <div class="row align-items-center">
                                        <div class="col ml--2">
                                            <p class="mb-0">{{__('Payment Reference')}}</p>
                                        </div>
                                        <div class="col-auto">
                                            <h4 class="castro-copy" data-clipboard-text="{{$val->link->ref_id}}">{{$val->link->ref_id}} <i class="fal fa-copy"></i></h4>
                                        </div>
                                    </div>
                                </li>
                                @elseif($val->type==2)
                                <li class="list-group-item bg-transparent">
                                    <div class="row align-items-center">
                                        <div class="col ml--2">
                                            <p class="mb-0">{{__('Payment Reference')}}</p>
                                        </div>
                                        <div class="col-auto">
                                            <h4 class="castro-copy" data-clipboard-text="{{$val->api->ref_id}}">{{$val->api->ref_id}} <i class="fal fa-copy"></i></h4>
                                        </div>
                                    </div>
                                </li>                                
                                @elseif($val->type==4)
                                <li class="list-group-item bg-transparent">
                                    <div class="row align-items-center">
                                        <div class="col ml--2">
                                            <p class="mb-0">{{__('Payment Reference')}}</p>
                                        </div>
                                        <div class="col-auto">
                                            <h4 class="castro-copy" data-clipboard-text="{{$val->balance->ref_id}}">{{$val->balance->ref_id}} <i class="fal fa-copy"></i></h4>
                                        </div>
                                    </div>
                                </li>
                                @endif
                                <li class="list-group-item bg-transparent">
                                    <div class="row align-items-center">
                                        <div class="col ml--2">
                                            <p class="mb-0">{{__('Channel')}}</p>
                                        </div>
                                        <div class="col-auto">
                                            <h4>{{ucwords(strtolower($val->payment_type))}}</h4>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item bg-transparent">
                                    <div class="row align-items-center">
                                        <div class="col ml--2">
                                            <p class="mb-0">{{$set->site_name}} {{__('Fees')}}</p>
                                        </div>
                                        <div class="col-auto">
                                            <h4>{{$val->getCurrency->real->currency.number_format($val->charge, 2)}}</h4>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item bg-transparent">
                                    <div class="row align-items-center">
                                        <div class="col ml--2">
                                            <p class="mb-0">{{__('Paid Fees')}}</p>
                                        </div>
                                        <div class="col-auto">
                                            <h4>@if($val->client==0){{__('You')}} @else {{__('Customer')}} @endif</h4>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item bg-transparent">
                                    <div class="row align-items-center">
                                        <div class="col ml--2">
                                            <p class="mb-0">{{__('Paid At')}}</p>
                                        </div>
                                        <div class="col-auto">
                                            <h4>{{$val->updated_at->format('M j, Y h:i:A')}} {{config('app.timezone')}}</h4>
                                        </div>
                                    </div>
                                </li>
                                @if($val->type!=4 && $val->type!=5)
                                <li class="list-group-item bg-transparent">
                                    <div class="row align-items-center">
                                        <div class="col ml--2">
                                            <p class="mb-0">{{__('Name')}}</p>
                                        </div>
                                        <div class="col-auto">
                                            <h4>{{$val->first_name.' '.$val->last_name}}</h4>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item bg-transparent">
                                    <div class="row align-items-center">
                                        <div class="col ml--2">
                                            <p class="mb-0">{{__('Email')}}</p>
                                        </div>
                                        <div class="col-auto">
                                            <h4>{{$val->email}}</h4>
                                        </div>
                                    </div>
                                </li>
                                @else
                                <li class="list-group-item bg-transparent">
                                    <div class="row align-items-center">
                                        <div class="col ml--2">
                                            <p class="mb-0">{{__('Name')}}</p>
                                        </div>
                                        <div class="col-auto">
                                            <h4>{{$val->receiver->first_name.' '.$val->receiver->last_name}}</h4>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item bg-transparent">
                                    <div class="row align-items-center">
                                        <div class="col ml--2">
                                            <p class="mb-0">{{__('Email')}}</p>
                                        </div>
                                        <div class="col-auto">
                                            <h4>{{$val->receiver->email}}</h4>
                                        </div>
                                    </div>
                                </li>
                                @endif
                            </ul>
                            @else
                            <ul class="list-group list-group-flush list my--3">
                                <li class="list-group-item bg-transparent">
                                    <div class="row align-items-center">
                                        <div class="col ml--2">
                                            <p class="mb-0">{{__('Account holder')}}</p>
                                        </div>
                                        <div class="col-auto">
                                            <h4>{{$val->name}} </h4>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item bg-transparent">
                                    <div class="row align-items-center">
                                        <div class="col ml--2">
                                            <p class="mb-0">{{__('Reference')}}</p>
                                        </div>
                                        <div class="col-auto">
                                            <h4 class="castro-copy" data-clipboard-text="{{$val->ref_id}}">{{$val->ref_id}} <i class="fal fa-copy"></i></h4>
                                        </div>
                                    </div>
                                </li>
                                @if($val->getCurrency->bank_format=="normal")
                                <li class="list-group-item bg-transparent">
                                    <div class="row align-items-center">
                                        <div class="col ml--2">
                                            <p class="mb-0">{{__('Bank')}}</p>
                                        </div>
                                        <div class="col-auto">
                                            <h4>{{$val->getBank->name}}</h4>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item bg-transparent">
                                    <div class="row align-items-center">
                                        <div class="col ml--2">
                                            <p class="mb-0">{{__('Account Number')}}</p>
                                        </div>
                                        <div class="col-auto">
                                            <h4>{{$val->acct_no}}</h4>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item bg-transparent">
                                    <div class="row align-items-center">
                                        <div class="col ml--2">
                                            <p class="mb-0">{{__('Account Name')}}</p>
                                        </div>
                                        <div class="col-auto">
                                            <h4>{{$val->acct_name}}</h4>
                                        </div>
                                    </div>
                                </li>
                                @elseif($val->getCurrency->bank_format=="us")
                                <li class="list-group-item bg-transparent">
                                    <div class="row align-items-center">
                                        <div class="col ml--2">
                                            <p class="mb-0">{{__('Routing Number')}}</p>
                                        </div>
                                        <div class="col-auto">
                                            <h4>{{$val->routing_no}}</h4>
                                        </div>
                                    </div>
                                </li>
                                @elseif($val->getCurrency->bank_format=="eur")
                                <li class="list-group-item bg-transparent">
                                    <div class="row align-items-center">
                                        <div class="col ml--2">
                                            <p class="mb-0">{{__('Iban')}}</p>
                                        </div>
                                        <div class="col-auto">
                                            <h4>{{$val->iban}}</h4>
                                        </div>
                                    </div>
                                </li>
                                @elseif($val->getCurrency->bank_format=="uk")
                                <li class="list-group-item bg-transparent">
                                    <div class="row align-items-center">
                                        <div class="col ml--2">
                                            <p class="mb-0">{{__('Account Name')}}</p>
                                        </div>
                                        <div class="col-auto">
                                            <h4>{{$val->acct_name}}</h4>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item bg-transparent">
                                    <div class="row align-items-center">
                                        <div class="col ml--2">
                                            <p class="mb-0">{{__('Sort Code')}}</p>
                                        </div>
                                        <div class="col-auto">
                                            <h4>{{$val->sort_code}}</h4>
                                        </div>
                                    </div>
                                </li>
                                @endif
                                <li class="list-group-item bg-transparent">
                                    <div class="row align-items-center">
                                        <div class="col ml--2">
                                            <p class="mb-0">{{$set->site_name}} {{__('Fees')}}</p>
                                        </div>
                                        <div class="col-auto">
                                            <h4>{{$val->getCurrency->real->currency.number_format($val->charge, 2)}}</h4>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item bg-transparent">
                                    <div class="row align-items-center">
                                        <div class="col ml--2">
                                            <p class="mb-0">{{__('Next Settlement')}}</p>
                                        </div>
                                        <div class="col-auto">
                                            <h4>{{Carbon\Carbon::parse($val->next_settlement)->format('M j, Y h:i:A')}} {{config('app.timezone')}}</h4>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            @if($val->status==1 && $val->type!=3 && $val->type!=5)
                            <a href="{{route('download.receipt', ['id' => $val->ref_id])}}" class="btn btn-block btn-neutral"><i class="fal fa-arrow-down"></i> {{__('Download Receipt')}}</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @if($val->type!=3 && $val->type!=5)
            <div class="col-md-6">
                <div class="vl d-none d-sm-block"></div>
                <div class="nav-wrapper2">
                    <ul class="nav nav-line-tabs nav-line-tabs-2x nav-stretch nav-trans b-b" id="tabs-icons-text" role="tablist">
                        @if($val->mode==1)
                        @if($val->payment_type=="card")
                        <li class="nav-item">
                            <a class="nav-link mb-sm-3 mb-md-0 @if($type=='analytics')active @endif" id="tabs-icons-text-1-tab" href="{{route('view.transactions', ['id' => $val->ref_id,'type' => 'analytics'])}}" role="tab" aria-controls="tabs-icons-text-1" aria-selected="true">{{__('Analytics')}}</a>
                        </li>
                        @endif
                        @endif
                        {{--
                        @if($val->status==1)
                        @if($val->mode==1)
                        <li class="nav-item">
                            <a class="nav-link mb-sm-3 mb-md-0 text-default @if($type=='refund')active @endif" id="tabs-icons-text-2-tab" href="{{route('view.transactions', ['id' => $val->ref_id,'type' => 'refund'])}}" role="tab" aria-controls="tabs-icons-text-2" aria-selected="false">{{__('Refunds')}}</a>
                        </li>
                        @endif
                        @endif
                        --}}
                        @if($user->receive_webhook==1)
                        <li class="nav-item">
                            <a class="nav-link mb-sm-3 mb-md-0 text-default @if($type=='webhook')active @endif" id="tabs-icons-text-3-tab" href="{{route('view.transactions', ['id' => $val->ref_id,'type' => 'webhook'])}}" role="tab" aria-controls="tabs-icons-text-3" aria-selected="false">{{__('Webhooks')}}</a>
                        </li>
                        @endif
                    </ul>
                </div>
                <div class="tab-content" id="myTabContent">
                    @if($val->mode==1)
                    @if($val->mode==1)
                    <div class="tab-pane fade @if($type=='analytics')show active @endif" id="tabs-icons-text-1" role="tabpanel" aria-labelledby="tabs-icons-text-1-tab">
                        <div class="card-body">
                            <div class="row">
                                @if($val->card_type!=null)
                                <div class="col-6 mb-2">
                                    <h4 class="mb-0">{{__('Card Type')}}</h4>
                                    <p>{{ucwords(strtolower($val->card_type))}}</p>
                                </div>
                                @endif
                                @if($val->card_number!=null)
                                <div class="col-6 mb-2">
                                    <h4 class="mb-0">{{__('Card Number')}}</h4>
                                    <p>{{substr($val->card_number,0,4)}}****{{substr($val->card_number,6,4)}}</p>
                                </div>
                                @endif
                                @if($val->auth_type!=null)
                                <div class="col-6 mb-2">
                                    <h4 class="mb-0">{{__('Auth Type')}}</h4>
                                    <p>{{strtoupper($val->auth_type)}}</p>
                                </div>
                                @endif
                                @if($val->ip_address!=null)
                                <div class="col-6 mb-2">
                                    <h4 class="mb-0">{{__('IP Address')}}</h4>
                                    <p>{{$val->ip_address}}</p>
                                </div>
                                @endif
                                @if($val->card_issuer!=null)
                                <div class="col-6 mb-2">
                                    <h4 class="mb-0">{{__('Issuer')}}</h4>
                                    <p>{{ucwords(strtolower($val->card_issuer))}}</p>
                                </div>
                                @endif
                                @if($val->card_country!=null)
                                <div class="col-6 mb-2">
                                    <h4 class="mb-0">{{__('Country')}}</h4>
                                    <p>{{$val->card_country}}</p>
                                </div>
                                @endif
                            </div>
                            <hr>
                            <div class="row">
                                @if(count(getCardLogs($val->trace_id))>0)
                                <div class="col-md-4 col-6">
                                    <div class="time-spent">
                                        <h3 class="time-spent_number">
                                            {{gmdate('i:s', getFirstCardLog($val->trace_id)->diffInSeconds(getLastCardLog($val->trace_id)))}}
                                        </h3>
                                        <h5 class="time-spent_value">
                                            {{__('minutes')}}
                                        </h5>
                                        <span class="time-spent_label text-info">{{__('Spent on Page')}}</span>
                                    </div>
                                </div>
                                @endif
                                <div class="col-md-5 col-6">
                                    <div class="row align-items-center mb-3">
                                        <div class="col-auto">
                                            <i class="fal fa-{{$val->device}} text-default fa-2x"></i>
                                        </div>
                                        <div class="col ml--2">
                                            <h4 class="mb-0 text-uppercase">{{__('Device type')}}</h4>
                                            <p>@if($val->device=="tv") {{__('Desktop')}} @elseif($val->device=="tablet") {{__('Tablet')}} @else {{__('Phone')}} @endif</p>
                                        </div>
                                    </div>
                                    <div class="row align-items-center mb-3">
                                        <div class="col-auto">
                                            <i class="fal fa-sync text-default fa-2x"></i>
                                        </div>
                                        <div class="col ml--2">
                                            <h4 class="mb-0 text-uppercase">{{__('Attempts')}}</h4>
                                            <p>{{$val->attempts}} @if(2>$val->attempts) {{__('attempt')}} @else {{__('attempts')}} @endif </p>
                                        </div>
                                    </div>
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <i class="fal fa-info-circle text-default fa-2x"></i>
                                        </div>
                                        <div class="col ml--2">
                                            <h4 class="mb-0 text-uppercase text-danger">{{__('Errors')}}</h4>
                                            <p>{{count(getCardErrors($val->trace_id))}} @if(2>count(getCardErrors($val->trace_id))) {{__('error')}} @else {{__('errors')}} @endif</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="timeline timeline-one-side" data-timeline-content="axis" data-timeline-axis-style="dashed">
                                @foreach(getCardLogs($val->trace_id) as $log)
                                <div class="timeline-block">
                                    <span class="timeline-step @if($log->type=='error')castro-danger @else castro-success @endif"></span>
                                    <div class="timeline-content">
                                        <div class="row">
                                            <div class="col-12">
                                                <p class="text-default">{{$log->message}}</p>
                                                <h4><i class="fal fa-clock mr-1"></i>{{$log->created_at->format('h:i:s:A')}}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                    @endif
                    {{--
                    @if($val->status==1)
                    @if($val->mode==1)
                    <div class="tab-pane fade @if($type=='refund')show active @endif" id="tabs-icons-text-2" role="tabpanel" aria-labelledby="tabs-icons-text-2-tab">
                        <div class="card-body">
                            @if($val->refund_id==null)
                            <div class="row">
                                <div class="col-6">
                                    <h4 class="mb-0">{{__('No refund attempts')}}</h4>
                </div>
                <div class="col-6 text-right">
                    <a href="{{route('initiate.refund', ['id'=>$val->trans_id])}}" class="btn btn-neutral mb-3"><i class="fal fa-arrow-alt-circle-left"></i> {{__('Initiate Refund')}}</a>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif
    @endif
    --}}
    @if($user->receive_webhook==1)
    <div class="tab-pane fade @if($type=='webhook')show active @endif" id="tabs-icons-text-3" role="tabpanel" aria-labelledby="tabs-icons-text-3-tab">
        <div class="accordion" id="accordionExample">
            @foreach(getWebhook($val->ref_id) as $webhook)
            <div class="card-header border-bottom" style="padding: 1rem 1rem;" id="heading{{$webhook->id}}">
                <div data-toggle="collapse" data-target="#collapse{{$webhook->id}}" aria-expanded="true" aria-controls="collapse{{$webhook->id}}">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="text-default">{{$webhook->uuid}}</p>
                        </div>
                        <div class="col-md-6">
                            <h4>{{Carbon\Carbon::parse($webhook->created_at)->format('M j, Y h:i:A')}} {{config('app.timezone')}}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div id="collapse{{$webhook->id}}" class="collapse @if($loop->first)show @endif" aria-labelledby="heading{{$webhook->id}}" data-parent="#accordionExample">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-7">
                            <p class="text-default">{{__('Response Status Code')}}</p>
                        </div>
                        <div class="col-md-4 text-right">
                            <h4>{{$webhook->response_status_code}}</h4>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <p class="mb-1 text-default">{{__('Payload')}}</p>
                            <pre class="rounded">
                                <code class="language-json" data-lang="json">   
                                {{$webhook->payload}}
                                </code>
                            </pre>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <p class="mb-1 text-default">{{__('Response')}}</p>
                            <pre class="rounded">
                                <code class="language-json" data-lang="json">   
                                {{$webhook->response}}
                                </code>
                            </pre>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <p class="mb-1 text-default">{{__('Headers')}}</p>
                            <pre class="rounded">
                                <code class="language-json" data-lang="json">   
                                {{$webhook->headers}}
                                </code>
                            </pre>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <a href="{{route('webhook.resend', ['id' => $val->ref_id])}}" class="btn btn-block btn-neutral"><i class="fal fa-sync"></i> {{__('Resend Webhook')}}</a>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
</div>
@endif
</div>
@stop
@section('script')
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