@extends('master')

@section('content')
<div class="container-fluid mt--6">
    <div class="content-wrapper">
        <a data-toggle="modal" data-target="#create" href="" class="btn btn-sm btn-neutral mb-5"><i class="fal fa-plus"></i> {{__('Add Country')}}</a>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="table-responsive py-4">
                        <table class="table table-flush" id="datatable-basic">
                            <thead>
                                <tr>
                                    <th>{{ __('S/N')}}</th>
                                    <th class="scope"></th>
                                    <th>{{ __('Country')}}</th>
                                    <th>{{__('Currency')}}</th>
                                    <th>{{__('Status')}}</th>
                                    <th>{{ __('Created')}}</th>
                                    <th>{{ __('Updated')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(getRegisteredCountry() as $k=>$val)
                                <tr>
                                    <td>{{++$k}}.</td>
                                    <td class="text-center">
                                        <div class="">
                                            <div class="dropdown">
                                                <a class="text-dark" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fal fa-chevron-circle-down"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    <a data-toggle="modal" data-target="#delete{{$val->id}}" href="" class="dropdown-item"><i class="fal fa-trash"></i> {{ __('Delete')}}</a>
                                                    @if($val->status==1)
                                                    <a class='dropdown-item' href="{{route('country.unpublish', ['id' => $val->id])}}"><i class="fal fa-ban"></i> {{__('Disable')}}</a>
                                                    @else
                                                    <a class='dropdown-item' href="{{route('country.publish', ['id' => $val->id])}}"><i class="fal fa-check"></i> {{__('Enable')}}</a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{$val->real->emoji.' '.$val->real->name}}</td>
                                    <td><span class="badge badge-pill badge-primary">{{$val->real->currency}}</span></td>
                                    <td>
                                        @if($val->status==1)
                                        <span class="badge badge-pill badge-primary"><i class="fal fa-check"></i> {{__('Active')}}</span>
                                        @else
                                        <span class="badge badge-pill badge-danger"><i class="fal fa-ban"></i> {{__('Disabled')}}</span>
                                        @endif
                                    </td>
                                    <td>{{date("Y/m/d h:i:A", strtotime($val->created_at))}}</td>
                                    <td>{{date("Y/m/d h:i:A", strtotime($val->updated_at))}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @foreach(getRegisteredCountry() as $k=>$val)
        <div class="modal fade" id="delete{{$val->id}}" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
            <div class="modal-dialog modal- modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-body p-0">
                        <div class="card bg-white border-0 mb-0">
                            <div class="card-header">
                                <h3 class="mb-0">{{__('Are you sure you want to delete this?, by doing this, any currency associated with this table will be deleted, user account balance associated with this currency will also be deleted')}}</h3>
                            </div>
                            <div class="card-body px-lg-5 py-lg-5 text-right">
                                <button type="button" class="btn btn-neutral btn-sm" data-dismiss="modal">{{ __('Close')}}</button>
                                <a href="{{route('delete.country', ['id' => $val->id])}}" class="btn btn-danger btn-sm">{{ __('Proceed')}}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        <div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="mb-0 h3 font-weight-bolder">{{__('Add Country')}}</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-2">After creating a country you will be redirected to a page to add important features necessary for the country currency. if you add a country that shares thesame currency with another country you won't be redirected.</p>
                        <form action="{{route('create.country')}}" method="post">
                            @csrf
                            <div class="form-group">
                                <select class="form-control select" name="id" id="country" required>
                                    <option value="">{{__('Select Country')}}</option>
                                    @if(count(getAllCountry())>0)
                                    @foreach(getAllCountry() as $val)
                                    <option value="{{$val->id}}">{{$val->emoji.' '.$val->name}}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="text-left">
                                <button type="submit" class="btn btn-success btn-block">{{__('Save')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @stop