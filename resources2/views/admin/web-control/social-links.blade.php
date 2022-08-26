@extends('master')

@section('content')
<div class="container-fluid mt--6">
    <div class="content-wrapper">
        <div class="card">
            <table class="table datatable-show-all">
                <thead>
                    <tr>
                        <th>{{__('S/N')}}</th>
                        <th>{{__('Name')}}</th>
                        <th>{{__('Link')}}</th>
                        <th class="scope"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($links as $k=>$val)
                    <tr>
                        <td>{{++$k}}.</td>
                        <td>{{$val->type}}</td>
                        <td>{{$val->value}}</td>
                        <td class="text-right">
                            <a class="btn btn-sm btn-success" data-toggle="modal" data-target="#update{{$val->id}}" href="">{{__('Edit')}}</a>
                        </td>
                    </tr>
                    <div id="update{{$val->id}}" class="modal fade" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h3 class="mb-0 fw-bold">{{$val->type}}</h3>
                                </div>
                                <form action="{{route('social-links.update')}}" method="post">
                                    <div class="modal-body">
                                        @csrf
                                        <div class="form-group row">
                                            <div class="col-lg-12">
                                                <input type="url" name="link" class="form-control" placeholder="link" value="{{$val->value}}">
                                                <input type="hidden" name="id" value="{{$val->id}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">{{__('Update')}}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
        @stop