@extends('master')

@section('content')
<div class="container-fluid mt--6">
    <div class="content-wrapper">
        <a href="{{url()->previous()}}" class="btn btn-neutral mb-3"><i class="fal fa-caret-left"></i> {{__('Go back')}}</a>
        <div class="card">
            <div class="card-body">
                <form action="{{route('user.email.send')}}" method="post">
                    @csrf
                    <div class="form-group row">
                        <label class="col-form-label col-lg-2">{{__('To')}}</label>
                        <div class="col-lg-10">
                            <input type="text" name="to" readonly maxlength="200" value="{{$email}}" class="form-control readonly" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-lg-2">{{__('Name')}}</label>
                        <div class="col-lg-10">
                            <input type="text" name="name" readonly maxlength="200" value="{{$name}}" class="form-control readonly" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-lg-2">{{__('Subject')}}</label>
                        <div class="col-lg-10">
                            <input type="text" name="subject" maxlength="200" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-lg-2">{{__('Message')}}</label>
                        <div class="col-lg-10">
                            <textarea type="text" name="message" rows="5" class="form-control tinymce"></textarea>
                        </div>
                    </div>
                    <div class="text-right">
                        <button type="submit" class="btn btn-success">{{__('Send')}}</button>
                    </div>
                </form>
            </div>
        </div>
        @stop