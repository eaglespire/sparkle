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
</div>
<div class="container mt--8 pb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header mb-0">
                    <h3>{{__('Your payment link is ready')}}</h3>
                    <p>{{__('Share your link to start receiving payments')}}</p>
                    <p>{{$link->title}}</p>
                    <p>@if($link->amount==null) Not fixed @else {{$link->amount.' '.$link->getCurrency->real->currency}} @endif</p>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        {!! QrCode::eye('circle')->style('round')->size(250)->generate(route('payment.link', ['id' => $link->ref_id])); !!}
                    </div>
                    <div class="text-center mb-3 mt-3">
                        <p>{{__('Scan QR code or Share using:')}}</p>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" class="form-control" value="{{route('payment.link', ['id' => $link->ref_id])}}">
                            <div class="input-group-append">
                                <span class="input-group-text castro-copy" data-clipboard-text="{{route('payment.link', ['id' => $link->ref_id])}}" title="Copy to clipboard"><span><i class="fal fa-copy"></i> {{__(' Copy link')}}</span></span>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <a href="https://wa.me/?text={{route('payment.link', ['id' => $link->ref_id])}}" target="_blank" class="btn btn-neutral btn-icon-only">
                            <span class="btn-inner--icon"><i class="fab fa-whatsapp"></i></span>
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{route('payment.link', ['id' => $link->ref_id])}}" target="_blank" class="btn btn-neutral btn-icon-only">
                            <span class="btn-inner--icon"><i class="fab fa-facebook"></i></span>
                        </a>
                        <a href="https://twitter.com/intent/tweet?text={{route('payment.link', ['id' => $link->ref_id])}}" target="_blank" class="btn btn-neutral btn-icon-only">
                            <span class="btn-inner--icon"><i class="fab fa-twitter"></i></span>
                        </a>
                        <a href="mailto:?body={{route('payment.link', ['id' => $link->ref_id])}}" class="btn btn-neutral btn-icon-only">
                            <span class="btn-inner--icon"><i class="fal fa-envelope"></i></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row align-items-center py-4">
            <div class="col-lg-6 col-5 text-left">
                <a href="{{route('user.payment')}}" class="btn btn-neutral-2"><i class="fal fa-caret-left"></i> {{__('Go back')}}</a>
            </div>
        </div>
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