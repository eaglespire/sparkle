@extends('master')

@section('content')
<div class="container-fluid mt--6">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.update.language')}}" method="post">
                            <div class="text-right mb-5">
                                <button type="submit" class="btn btn-success btn-sm">{{__('Save')}}</button>
                            </div>
                            @csrf
                            <input type="hidden" value="{{$castro->id}}" name="id">
                            @foreach ($json as $key => $value)
                            <div class="form-group row">
                                <label class="col-form-label col-lg-4">{{$key}}</label>
                                <div class="col-lg-8">
                                    <input type="text" name="keys[{{$key}}]" class="form-control" value="{{$value}}" required>
                                </div>
                            </div>
                            @endforeach
                            <div class="text-right">
                                <button type="submit" class="btn btn-success btn-sm">{{__('Save')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @stop