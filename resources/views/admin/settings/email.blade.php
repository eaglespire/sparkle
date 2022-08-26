@extends('master')

@section('content')
<div class="container-fluid mt--6">
    <div class="content-wrapper">
        <div class="card">
            <div class="card-header">
                <h3>{{__('Email SMTP Credentials')}}</h3>
                <p class="mb-1">Table shows value of your env email configuration, ensure what is dispalyed here matches your email service smtp configuration, if it doesn't, navigate to core/.env file to edit this</p>
                <div class="table-responsive mb-5">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>PARAMETER</th>
                                <th>VALUE</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td> 1 </td>
                                <td> MAIL_HOST</td>
                                <td> {{env('MAIL_HOST')}}</td>
                            </tr>
                            <tr>
                                <td> 2 </td>
                                <td> MAIL_PORT </td>
                                <td> {{env('MAIL_PORT')}}</td>
                            </tr>
                            <tr>
                                <td> 3 </td>
                                <td> MAIL_USERNAME </td>
                                <td> {{env('MAIL_USERNAME')}}</td>
                            </tr>
                            <tr>
                                <td> 4 </td>
                                <td> MAIL_PASSWORD </td>
                                <td> {{env('MAIL_PASSWORD')}}</td>
                            </tr>
                            <tr>
                                <td> 5 </td>
                                <td> MAIL_ENCRYPTION </td>
                                <td> {{env('MAIL_ENCRYPTION')}}</td>
                            </tr>
                            <tr>
                                <td> 6 </td>
                                <td> MAIL_FROM_ADDRESS </td>
                                <td> {{env('MAIL_FROM_ADDRESS')}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-body">
                <form action="{{route('admin.settings.update')}}" method="post">
                    @csrf
                    <h3>Template Configuration</h3>
                    <div class="table-responsive mb-5">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>CODE</th>
                                    <th>DESCRIPTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td> 1 </td>
                                    <td> &#123;&#123;message&#125;&#125;</td>
                                    <td> Details Text From the Script</td>
                                </tr>
                                <tr>
                                    <td> 2 </td>
                                    <td> &#123;&#123;logo&#125;&#125; </td>
                                    <td> Platform logo. Will be Pulled From Database</td>
                                </tr>
                                <tr>
                                    <td> 3 </td>
                                    <td> &#123;&#123;site_name&#125;&#125; </td>
                                    <td> Website Name. Will be Pulled From Database</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <textarea type="text" name="email_template" rows="4" class="form-control tinymce">{{$set->email_template}}</textarea>
                        </div>
                    </div>
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
                    </div>
                </form>
            </div>
        </div>
        @stop