@extends('userlayout')

@section('content')
<div class="container-fluid mt--6">
    <div class="content-wrapper">
        <div class="row align-items-center py-4">
            <div class="col-lg-6 col-5 text-left">
                <a href="{{route('user.ticket')}}" class="btn btn-neutral"><i class="fal fa-caret-left"></i> {{__('Go back')}}</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card border-0 mb-0">
                    <div class="card-header">
                        <h3 class="mb-0 font-weight-bolder">{{__('New Dispute')}}</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{route('submit-ticket')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group row">
                                <label class="col-form-label col-lg-2">{{__('Subject')}} <span class="text-danger">*</span></label>
                                <div class="col-lg-10">
                                    <input type="text" name="subject" class="form-control" placeholder="Title of complaint" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-2">{{__('Reference')}} <span class="text-danger">*</span></label>
                                <div class="col-lg-10">
                                    <input type="text" name="ref_no" class="form-control" placeholder="Transaction reference" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-2">{{__('Priority')}}</label>
                                <div class="col-lg-10">
                                    <select class="form-control select" name="priority" required>
                                        <option value="Low">{{__('Low')}}</option>
                                        <option value="Medium">{{__('Medium')}}</option>
                                        <option value="High">{{__('High')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-2">{{__('Description')}} <span class="text-danger">*</span></label>
                                <div class="col-lg-10">
                                    <textarea name="details" class="form-control" rows="3" required placeholder="Whats your complaint?"></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-2">{{__('Select a file')}}</label>
                                <div class="col-lg-10">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="customFileLang" name="image[]" multiple>
                                        <label class="custom-file-label" for="customFileLang">{{__('Choose Media')}}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <button type="submit" class="btn btn-neutral btn-block">{{__('Submit dispute')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @stop