@extends('master')

@section('content')
<div class="container-fluid mt--6">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="table-responsive py-4">
                        <table class="table table-flush" id="datatable-basic">
                            <thead>
                                <tr>
                                    <th>{{__('S/N')}}</th>
                                    <th></th>
                                    <th>{{__('Currency')}}</th>
                                    <th>{{__('Bank format')}}</th>
                                    <th>{{__('Percent Charge')}}</th>
                                    <th>{{__('Withdraw Charge')}}</th>
                                    <th>{{__('Min amount')}}</th>
                                    <th>{{__('Max amount')}}</th>
                                    <th>{{__('Status')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(getAllAcceptedCountry() as $k=>$val)
                                <tr>
                                    <td>{{$loop->iteration}}.</td>
                                    <td class="text-center">
                                        <div class="">
                                            <div class="dropdown">
                                                <a class="text-dark" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fal fa-chevron-circle-down"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    @if($val->bank_format=="normal")
                                                    <a class='dropdown-item' href="{{route('admin.bank', ['id' => $val->id])}}"><i class="fal fa-university"></i> {{ __('Banks')}}</a>
                                                    @endif
                                                    <a class='dropdown-item' href="{{route('currency.users', ['id' => $val->id])}}"><i class="fal fa-user"></i> {{ __('Customers')}}</a>
                                                    @if($val->status==1)
                                                    <a class='dropdown-item' href="{{route('currency.unpublish', ['id' => $val->id])}}"><i class="fal fa-ban"></i> {{__('Disable')}}</a>
                                                    @else
                                                    <a class='dropdown-item' href="{{route('currency.publish', ['id' => $val->id])}}"><i class="fal fa-check"></i> {{__('Enable')}}</a>
                                                    @endif
                                                    <a class="dropdown-item" href="{{route('admin.edit.currency', ['id'=>$val->id])}}"><i class="fal fa-pencil"></i> Edit</a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{$val->real->currency}}</td>
                                    <td>{{$val->bank_format}}</td>
                                    <td>{{$val->percent_charge.'%'.'+'.$val->fiat_charge.$val->real->currency}}</td>
                                    <td>{{$val->withdraw_percent_charge.'%'.'+'.$val->withdraw_fiat_charge.$val->real->currency}}</td>
                                    <td>{{$val->min_amount.$val->real->currency}}</td>
                                    <td>@if($val->max_amount!=null){{$val->max_amount.$val->real->currency}}@else Infinite @endif</td>
                                    <td>
                                        @if($val->status==1)
                                        <span class="badge badge-pill badge-primary"><i class="fad fa-check"></i> {{__('Active')}}</span>
                                        @else
                                        <span class="badge badge-pill badge-danger"><i class="fad fa-ban"></i> {{__('Disabled')}}</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @stop