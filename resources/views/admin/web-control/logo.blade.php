@extends('master')

@section('content')
<div class="container-fluid mt--6">
    <div class="content-wrapper">       
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">{{ __('Logo')}}</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{route('dark.logo')}}" enctype="multipart/form-data" method="post">
                        @csrf
                            <div class="form-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="customFileLang2" name="logo" lang="en" required>
                                    <label class="custom-file-label" for="customFileLang2">{{__('Choose Media')}}</label>
                                </div>
                            </div>              
                            <div class="text-right">
                                <button type="submit" class="btn btn-success btn-sm">{{ __('Upload')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="card-img-actions d-inline-block mb-3">
                            <img class="img-fluid" src="{{asset('asset/'.$logo->dark)}}" style="max-width:50%;height:auto;">
                        </div>
                    </div>
                </div>
            </div>
        </div>    
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Favicon')}}</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{url('admin/updatefavicon')}}" enctype="multipart/form-data" method="post">
                        @csrf
                            <div class="form-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="customFileLang1" name="favicon" lang="en" required>
                                    <label class="custom-file-label sdsd" for="customFileLang1">{{__('Choose Media')}}</label>
                                </div>
                            </div>              
                            <div class="text-right">
                                <button type="submit" class="btn btn-success btn-sm">{{ __('Upload')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="card-img-actions d-inline-block mb-3">
                            <img class="img-fluid" src="{{asset('asset/'.$logo->image_link2)}}/" style="max-width:50%;height:auto;">
                        </div>
                    </div>
                </div>
            </div>
        </div>        
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Preloader')}}</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{url('admin/updatepreloader')}}" enctype="multipart/form-data" method="post">
                        @csrf
                            <div class="form-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="customFileLang1" name="preloader" lang="en" required>
                                    <label class="custom-file-label sdsc" for="customFileLang1">{{__('Choose Media')}}</label>
                                </div>
                            </div>              
                            <div class="text-right">
                                <button type="submit" class="btn btn-success btn-sm">{{ __('Upload')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="card-img-actions d-inline-block mb-3">
                            <img class="img-fluid" src="{{asset('asset/'.$logo->preloader)}}" style="max-width:50%;height:auto;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>  
@stop