@extends('master')

@section('content')
<div class="container-fluid mt--6">
    <div class="content-wrapper">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <form class="navbar-search navbar-search-light" action="{{route('admin.user.search')}}" method="post" id="navbar-search-main">
                            @csrf
                            <div class="form-group mb-0">
                                <div class="input-group input-group-merge">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fal fa-search"></i></span>
                                    </div>
                                    <input class="form-control" placeholder="Email, name" name="search" type="text">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="table-responsive py-4">
                <table class="table table-flush" id="">
                    <thead>
                        <tr>
                            <th>{{__('S/N')}}</th>
                            <th class="scope"></th>
                            <th>@sortablelink('first_name', 'Name')</th>
                            <th>@sortablelink('email', 'Email')</th>
                            <th>@sortablelink('status', 'Status')</th>
                            <th>@sortablelink('created_at', 'Date')</th>
                            <th>{{__('Updated')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $k=>$val)
                        <tr>
                            <td>{{++$k}}.</td>
                            <td class="">
                                <div class="dropdown">
                                    <a class="text-dark" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fal fa-chevron-circle-down"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                        <a href="{{route('user.manage', ['id' => $val->id])}}" class="dropdown-item">{{__('Manage customer')}}</a>
                                        <a href="{{route('user.transaction.admin', ['id' => $val->id])}}" class="dropdown-item">{{__('Transactions')}}</a>
                                        <a href="{{route('user.payment.admin', ['id' => $val->id])}}" class="dropdown-item">{{__('Payment')}}</a>
                                        <a href="{{route('user.payout.admin', ['id' => $val->id])}}" class="dropdown-item">{{__('Payout')}}</a>
                                        <a href="{{route('user.card.transactions', ['id' => $val->id])}}" class="dropdown-item">{{__('Cards')}}</a>
                                    </div>
                                </div>
                            </td>
                            <td>{{$val->first_name.' '.$val->last_name}}</td>
                            <td>{{$val->email}}</td>
                            <td>
                                @if($val->status==0)
                                <span class="badge badge-pill badge-primary">{{__('Active')}}</span>
                                @elseif($val->status==1)
                                <span class="badge badge-pill badge-danger">{{__('Blocked')}}</span>
                                @endif
                            </td>
                            <td>{{date("Y/m/d h:i:A", strtotime($val->created_at))}}</td>
                            <td>{{date("Y/m/d h:i:A", strtotime($val->updated_at))}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <p>Showing 1 to {{$users->count()}} of {{ $users->total() }} entries</p>
                        </div>
                        <div class="col-md-12 text-right">
                            {{ $users->onEachSide(1)->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @stop
        @section('script')
        @endsection