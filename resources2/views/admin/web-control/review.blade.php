@extends('master')

@section('content')
<div class="container-fluid mt--6">
    <div class="content-wrapper">
        <a data-toggle="modal" data-target="#create" href="" class="btn btn-sm btn-neutral mb-5"><i class="fal fa-plus"></i> {{__('Create Review')}}</a>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="table-responsive py-4">
                        <table class="table table-flush" id="datatable-buttons">
                            <thead>
                                <tr>
                                    <th>{{__('S/N')}}</th>
                                    <th>{{__('Name')}}</th>
                                    <th>{{__('Occupation')}}</th>
                                    <th>{{__('Status')}}</th>
                                    <th>{{__('Created')}}</th>
                                    <th>{{__('Updated')}}</th>
                                    <th class="scope"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($review as $k=>$val)
                                <tr>
                                    <td>{{++$k}}.</td>
                                    <td>{{$val->name}}</td>
                                    <td>{{$val->occupation}}</td>
                                    <td>
                                        @if($val->status==1)
                                        <span class="badge badge-success">{{__('Published')}}</span>
                                        @else
                                        <span class="badge badge-danger">{{__('Pending')}}</span>
                                        @endif
                                    </td>
                                    <td>{{date("Y/m/d h:i:A", strtotime($val->created_at))}}</td>
                                    <td>{{date("Y/m/d h:i:A", strtotime($val->updated_at))}}</td>
                                    <td class="text-center">
                                        <a data-toggle="modal" data-target="#delete{{$val->id}}" href="" class="btn btn-sm btn-danger">{{__('Delete')}}</a>
                                        @if($val->status==1)
                                        <a class="btn btn-sm btn-success" href="{{route('review.unpublish', ['id' => $val->id])}}">{{__('Unpublish')}}</a>
                                        @else
                                        <a class="btn btn-sm btn-success" href="{{route('review.publish', ['id' => $val->id])}}">{{__('Publish')}}</a>
                                        @endif
                                        <a href="{{route('review.edit', ['id' => $val->id])}}" class="btn btn-sm btn-primary">{{__('Edit')}}</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @foreach($review as $k=>$val)
                <div class="modal fade" id="delete{{$val->id}}" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
                    <div class="modal-dialog modal- modal-dialog-centered modal-md" role="document">
                        <div class="modal-content">
                            <div class="modal-body p-0">
                                <div class="card bg-white border-0 mb-0">
                                    <div class="card-header">
                                        <h3 class="mb-0">{{__('Are you sure you want to delete this?')}}</h3>
                                    </div>
                                    <div class="card-body px-lg-5 py-lg-5 text-right">
                                        <button type="button" class="btn btn-neutral btn-sm" data-dismiss="modal">{{__('Close')}}</button>
                                        <a href="{{route('review.delete', ['id' => $val->id])}}" class="btn btn-danger btn-sm">{{__('Proceed')}}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <div id="create" class="modal fade" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="mb-0 fw-bold">{{__('Create Review')}}</h3>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <form action="{{route('review.create')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group row">
                                <div class="col-lg-12">
                                    <input type="text" name="name" class="form-control" placeholder="{{__('Name')}}" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-12">
                                    <input type="text" name="occupation" class="form-control" placeholder="{{__('Occupation')}}" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-12">
                                    <textarea type="text" name="review" class="form-control" placeholder="{{__('Review')}}" rows="5" required></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-12">
                                    <input type="file" class="custom-file-input" id="customFileLang" name="image" lang="en" required>
                                    <label class="custom-file-label" for="customFileLang">{{__('Choose Image')}}</label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @stop