@extends('master')

@section('content')
<div class="container-fluid mt--6">
    <div class="content-wrapper">
        <a data-toggle="modal" data-target="#create" href="" class="btn btn-sm btn-neutral mb-5"><i class="fal fa-plus"></i> {{__('Add Bank')}}</a>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="table-responsive py-4">
                        <table class="table table-flush" id="datatable-basic">
                            <thead>
                                <tr>
                                    <th>{{ __('S/N')}}</th>
                                    <th>{{ __('Name')}}</th>
                                    <th>{{ __('Created')}}</th>
                                    <th>{{ __('Updated')}}</th>
                                    <th class="scope"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bank as $k=>$val)
                                <tr>
                                    <td>{{++$k}}.</td>
                                    <td>{{$val->name}}</td>
                                    <td>{{date("Y/m/d h:i:A", strtotime($val->created_at))}}</td>
                                    <td>{{date("Y/m/d h:i:A", strtotime($val->updated_at))}}</td>
                                    <td class="text-center">
                                        <a data-toggle="modal" data-target="#delete{{$val->id}}" href="" class="btn btn-sm btn-danger">{{ __('Delete')}}</a>
                                        <a data-toggle="modal" data-target="#update{{$val->id}}" href="" class="btn btn-sm btn-primary">{{ __('Edit')}}</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @foreach($bank as $k=>$val)
        <div class="modal fade" id="delete{{$val->id}}" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
            <div class="modal-dialog modal- modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-body p-0">
                        <div class="card bg-white border-0 mb-0">
                            <div class="card-header">
                                <h3 class="mb-0">{{__('Are you sure you want to delete this?')}}</h3>
                            </div>
                            <div class="card-body px-lg-5 py-lg-5 text-right">
                                <button type="button" class="btn btn-neutral btn-sm" data-dismiss="modal">{{ __('Close')}}</button>
                                <a href="{{route('bank.delete', ['id' => $val->id])}}" class="btn btn-danger btn-sm">{{ __('Proceed')}}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="update{{$val->id}}" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="mb-0 h3 font-weight-bolder">{{__('Edit Bank')}}</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{route('bank.update')}}" method="post">
                            @csrf
                            <div class="form-group row">
                                <div class="col-lg-12">
                                    <input type="text" name="name" class="form-control" placeholder="Name" value="{{$val->name}}" required>
                                </div>
                            </div>
                            <input type="hidden" name="id" value="{{$val->id}}">
                            <div class="text-right">
                                <button type="submit" class="btn btn-success btn-block">{{__('Save')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        <div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="mb-0 h3 font-weight-bolder">{{__('Add Bank')}}</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{route('admin.createbank')}}" method="post">
                            @csrf
                            <div class="form-group row">
                                <div class="col-lg-12">
                                    <input type="text" name="name" class="form-control" placeholder="Name" required>
                                </div>
                            </div>
                            <input name="country" type="hidden" value="{{$country}}">
                            <div class="text-left">
                                <button type="submit" class="btn btn-success btn-block">{{__('Save')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @stop