
@extends('userlayout')

@section('content')
<!-- Page content -->
<div class="container-fluid mt--6">
  <div class="content-wrapper mt-3">
    <div class="card">
      <div class="table-responsive py-4">
        <table class="table table-flush" id="datatable-buttons">
          <thead>
            <tr>
              <th>{{__('S / N')}}</th>
              <th>{{__('Amount')}}</th>
              <th>{{__('Description')}}</th>
              <th>{{__('Reference')}}</th>
              <th>{{__('Status')}}</th>
              <th>{{__('Type')}}</th>
              <th>{{__('Created')}}</th>
            </tr>
          </thead>
          <tbody>  
            @foreach($log as $k=>$val)
              <tr>
                <td>{{++$k}}.</td>
                <td>{{$val->card->getCurrency->real->currency.number_format($val->amount, 2)}}</td>
                <td>{{$val->description}}</td>
                <td>{{$val->ref_id}}</td>
                <td>{{$val->status}}</td>
                <td>{{$val->type}}</td>
                <td>{{date("Y/m/d h:i:A", strtotime($val->created_at))}}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

@stop