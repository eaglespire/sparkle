
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
              <th>{{__('Reference ID')}}</th>
              <th>{{__('Created')}}</th>
            </tr>
          </thead>
          <tbody>  
            @foreach($user->getChargeBacks() as $k=>$val)
              <tr>
                <td>{{++$k}}.</td>
                <td>{{$currency->symbol.number_format($val->amount+$val->charge, 2, '.', '')}}</td>
                <td>#{{$val->ref}}</td>
                <td>{{date("Y/m/d h:i:A", strtotime($val->created_at))}}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

@stop