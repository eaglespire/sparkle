@extends('master')

@section('content')
<div class="container-fluid mt--6">
    <div class="content-wrapper">
        <a href="{{url()->previous()}}" class="btn btn-neutral mb-3"><i class="fal fa-caret-left"></i> {{__('Go back')}}</a>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <p class="text-danger"></p>
                        <form action="{{route('plugin.update')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group row">
                                <div class="col-lg-12">
                                    <input type="text" name="title" class="form-control" placeholder="Title" value="{{$val->title}}">
                                    <input type="hidden" name="id" value="{{$val->id}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="customFileLang2" name="file" lang="en" required>
                                    <label class="custom-file-label" for="customFileLang2">{{__('Zip file')}}</label>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-12">
                                    <textarea type="text" name="description" placeholder="Description" class="form-control">{{$val->description}}</textarea>
                                </div>
                            </div>
                            <div class="text-right">
                                <button type="submit" class="btn btn-success btn-sm">{{__('Save')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop