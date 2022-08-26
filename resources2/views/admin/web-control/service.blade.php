@extends('master')

@section('content')
<div class="container-fluid mt--6">
    <div class="content-wrapper">
        <a data-toggle="modal" data-target="#create" href="" class="btn btn-sm btn-neutral mb-5"><i class="fal fa-plus"></i> {{__('Create service')}}</a>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="table-responsive py-4">
                        <table class="table table-flush" id="datatable-buttons">
                            <thead>
                                <tr>
                                    <th>{{__('S/N')}}</th>
                                    <th>{{__('Title')}}</th>
                                    <th>{{__('Created')}}</th>
                                    <th>{{__('Updated')}}</th>
                                    <th class="text-center">{{__('Action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($service as $k=>$val)
                                <tr>
                                    <td>{{++$k}}.</td>
                                    <td>{{$val->title}}</td>
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
                @foreach($service as $k=>$val)
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
                                        <a href="{{route('service.delete', ['id' => $val->id])}}" class="btn btn-danger btn-sm">{{ __('Proceed')}}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="update{{$val->id}}" class="modal fade" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="mb-0 fw-bold">{{__('Edit Service')}}</h3>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <form action="{{route('service.update')}}" method="post">
                                @csrf
                                <div class="modal-body">
                                    <div class="form-group row">
                                        <div class="col-lg-12">
                                            <input type="text" name="title" placeholder="{{__('Title')}}" class="form-control" value="{{$val->title}}">
                                            <input type="hidden" name="id" value="{{$val->id}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-lg-12">
                                            <textarea type="text" name="details" rows="5" placeholder="{{__('Details')}}" class="form-control">{{$val->details}}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-neutral" data-dismiss="modal">{{__('Close')}}</button>
                                    <button type="submit" class="btn btn-primary">{{__('Update')}}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
<div id="create" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="mb-0 fw-bold">{{__('Create Service')}}</h3>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{route('service.create')}}" method="post">
                @csrf
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <input type="text" name="title" placeholder="{{__('Title')}}" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <textarea type="text" name="details" rows="5" placeholder="{{__('Details')}}" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-neutral" data-dismiss="modal">{{__('Close')}}</button>
                    <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop