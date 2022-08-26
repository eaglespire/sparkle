@extends('userlayout')

@section('content')
<div class="container-fluid mt--6">
  <div class="content-wrapper mt-3">
    <a href="{{route('open.ticket')}}" class="btn btn-neutral mb-5">{{__('New dispute')}}</a>
    <div class="row">
      @if(count($ticket)>0)
      @foreach($ticket as $k=>$val)
      <div class="col-md-6">
        <div class="card">
          <!-- Card body -->
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-7">
                <!-- Title -->
                <h4 class="mb-0 font-weight-bolder">{{$val->ticket_id}}</h4>
              </div>
              <div class="col-5 text-right">
                <a href="{{route('ticket.reply.user', ['id'=>$val->id])}}" class="btn btn-sm btn-neutral"><i class="fal fa-comment-dots"></i> {{__('Reply')}}</a>
                <a data-toggle="modal" data-target="#delete{{$val->id}}" href="" class="btn btn-sm btn-danger"><i class="fal fa-trash-alt"></i> {{__('Delete')}}</a>
              </div>
            </div>
            <div class="row">
              <div class="col">
                <p class="text-sm mb-0">{{__('Subject')}}: {{$val->subject}}</p>
                <p class="text-sm mb-0">{{__('Transaction Reference')}}: @if($val->ref_no==null){{__('Null')}} @else {{$val->ref_no}} @endif</p>
                <p class="text-sm mb-0">{{__('Priority')}}: {{$val->priority}}</p>
                <p class="text-sm mb-0">{{__('Status')}}: @if($val->status==0){{__('Open')}} @elseif($val->status==1){{__('Closed')}} @elseif($val->status==2){{__('Resolved')}} @endif</p>
                <p class="text-sm mb-0">{{__('Created')}}: {{date("Y/m/d h:i:A", strtotime($val->created_at))}}</p>
                <p class="text-sm mb-2">{{__('Updated')}}: {{date("Y/m/d h:i:A", strtotime($val->updated_at))}}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal fale" id="delete{{$val->id}}" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="mb-0 font-weight-bolder">{{__('Delete Ticket')}}</h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <span class="mb-0 text-xs">{{__('Are you sure you want to delete this?, all replies to this ticket will be deleted')}}</span>
            </div>
            <div class="modal-footer">
              <a href="{{route('ticket.delete', ['id' => $val->id])}}" class="btn btn-danger btn-block">{{__('Proceed')}}</a>
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
              <span class=""><i class="fal fa-flag fa-4x text-info"></i></span>
            </a>
          </div>
          <h3 class="text-dark">{{__('No Transaction Dispute Found')}}</h3>
          <p class="text-dark card-text">{{__('We couldn\'t find any dispute to this account')}}</p>
        </div>
      </div>
      @endif
    </div>
    <div class="row">
      <div class="col-md-12">
        {{ $ticket->links('pagination::bootstrap-4') }}
      </div>
    </div>
    @stop