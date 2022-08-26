@extends('master')

@section('content')
<div class="container-fluid mt--6">
    <div class="content-wrapper">
        <a href="{{url()->previous()}}" class="btn btn-neutral mb-3"><i class="fal fa-caret-left"></i> {{__('Go back')}}</a>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-8">
                                <h3 class="mb-0">{{__('Update Account Information')}}</h3>
                            </div>
                            <div class="col-md-4 text-right">
                                <div class="dropdown">
                                    <a class="text-dark" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fal fa-chevron-circle-down"></i> Options
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                        <a href="{{route('admin.email', ['email' => $client->email, 'name' => $client->first_name.' '.$client->last_name])}}" class="dropdown-item">{{__('Send email')}}</a>
                                        @if($client->status==0)
                                        <a class='dropdown-item' href="{{route('user.block', ['id' => $client->id])}}">{{__('Block')}}</a>
                                        @else
                                        <a class='dropdown-item' href="{{route('user.unblock', ['id' => $client->id])}}">{{__('Unblock')}}</a>
                                        @endif
                                        <a data-toggle="modal" data-target="#delete" href="" class="dropdown-item">{{__('Delete')}}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
                        <div class="modal-dialog modal- modal-dialog-centered modal-md" role="document">
                            <div class="modal-content">
                                <div class="modal-body p-0">
                                    <div class="card bg-white border-0 mb-0">
                                        <div class="card-header">
                                            <h3 class="mb-0">{{__('Are you sure you want to delete this?')}}</h3>
                                        </div>
                                        <div class="card-body px-lg-5 py-lg-5 text-right">
                                            <button type="button" class="btn btn-neutral btn-sm" data-dismiss="modal">{{__('Close')}}</button>
                                            <a href="{{route('user.delete', ['id' => $client->id])}}" class="btn btn-danger btn-sm">{{__('Proceed')}}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{route('profile.update', ['id'=>$client->id])}}" method="post">
                            @csrf
                            <div class="form-group row">
                                <label class="col-form-label col-lg-2">{{__('First Name')}}</label>
                                <div class="col-lg-10">
                                    <input type="text" name="first_name" class="form-control" value="{{$client->first_name}}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-2">{{__('Last Name')}}</label>
                                <div class="col-lg-10">
                                    <input type="text" name="last_name" class="form-control" value="{{$client->last_name}}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-2">{{__('Email')}}</label>
                                <div class="col-lg-10">
                                    <input type="email" name="email" class="form-control" readonly value="{{$client->email}}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-2">{{__('Status')}}<span class="text-danger">*</span></label>
                                <div class="col-lg-10">
                                    <div class="custom-control custom-control-alternative custom-checkbox">
                                        @if($client->email_verify==1)
                                        <input type="checkbox" name="email_verify" id=" customCheckLogin5" class="custom-control-input" value="1" checked>
                                        @else
                                        <input type="checkbox" name="email_verify" id=" customCheckLogin5" class="custom-control-input" value="1">
                                        @endif
                                        <label class="custom-control-label" for=" customCheckLogin5">
                                            <span class="text-muted">{{__('Email verification')}}</span>
                                        </label>
                                    </div>
                                    <div class="custom-control custom-control-alternative custom-checkbox">
                                        @if($client->fa_status==1)
                                        <input type="checkbox" name="fa_status" id=" customCheckLogin6" class="custom-control-input" value="1" checked>
                                        @else
                                        <input type="checkbox" name="fa_status" id=" customCheckLogin6" class="custom-control-input" value="1">
                                        @endif
                                        <label class="custom-control-label" for=" customCheckLogin6">
                                            <span class="text-muted">{{__('2fa security')}}</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <button type="submit" class="btn btn-success">{{__('Save')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            @foreach(App\Models\Business::whereuser_id($client->id)->get() as $val)
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">{{$val->name}}</h3>
                    </div>
                    <div class="card-body">
                        @if($val->kyc_status!=null || $val->kyc_status=="PROCESSING")
                        <p>{{__('Industry')}}: {{$val->industry}}</p>
                        <p>{{__('Category')}}: {{$val->category}}</p>
                        <p>{{__('Staff size')}}: {{$val->staff_size}}</p>
                        <p>{{__('Type')}}: @if($val->type==1) Unregistered @else Registered @endif</p>
                        @if($val->type==2)
                        <h4 class="mt-3 mb-0">Business Details</h4>
                        <p>{{__('Legal name')}}: {{$val->legal_name}}</p>
                        <p>{{__('Tax Id')}}: {{$val->tax_id}}</p>
                        <p>{{__('Vat Id')}}: {{$val->vat_id}}</p>
                        <p>{{__('Registration No')}}: {{$val->reg_no}}</p>
                        <p>{{__('Registration Type')}}: {{$val->registration_type}}</p>
                        <p>{{__('Line 1')}}: {{$val->business_line_1}}</p>
                        <p>{{__('Line 2')}}: {{$val->business_line_2}}</p>
                        @if($val->state!=null)
                        <p>{{__('State')}}: {{$val->myBusinessState()->name}}</p>
                        @endif
                        @if($val->city!=null)
                        <p>{{__('City')}}: {{$val->myBusinessCity()->name}}</p>
                        @endif
                        <p>{{__('Postal code')}}: {{$val->business_postal_code}}</p>
                        <a href="{{asset('asset/profile/'.$val->business_proof_of_address)}}">{{__('Business Proof of Address')}}</a>,
                        @if($val->business_document!=null)
                        <a href="{{asset('asset/profile/'.$business_document)}}">{{__('Business Document')}}</a></br></br>
                        @endif
                        @endif
                        <h4 class="mt-3 mb-0">Owner Details</h4>
                        <p>{{__('Full name')}}: {{$val->first_name}} {{$val->last_name}}</p>
                        <p>{{__('DOB')}}: {{$val->b_day}}/{{$val->b_month}}/{{$val->b_year}}</p>
                        <p>{{__('ID Document')}}: {{$val->doc_type}}</p>
                        <p>{{__('Line 1')}}: {{$val->line_1}}</p>
                        <p>{{__('Line 2')}}: {{$val->line_2}}</p>
                        @if($val->state!=null)
                        <p>{{__('State')}}: {{$val->myState()->name}}</p>
                        @endif
                        @if($val->city!=null)
                        <p>{{__('City')}}: {{$val->myCity()->name}}</p>
                        @endif
                        <p>{{__('Postal code')}}: {{$val->postal_code}}</p>
                        <p>{{__('Gender')}}: {{$val->gender}}</p>
                        <a href="{{asset('asset/profile/'.$val->proof_of_address)}}">{{__('Proof of Address')}}</a>,
                        @if($val->document!=null)
                        <a href="{{asset('asset/profile/'.$val->document)}}">{{__('Identity Document')}}</a></br></br>
                        @endif
                        <p class="mb-2">{{__('Compliance Status')}}: {{$val->kyc_status}}</p>
                        @if($val->kyc_status=="PROCESSING")
                        <a class="btn btn-sm btn-neutral" href="{{route('admin.approve.kyc', ['id'=>$val->id])}}"><i class="fal fa-check"></i> {{__('Approve')}}</a>
                        <a class="btn btn-sm btn-danger" href="{{route('admin.reject.kyc', ['id'=>$val->id])}}"><i class="fal fa-ban"></i> {{__('Reject')}}</a>
                        <a class='btn btn-sm btn-primary' data-toggle="modal" data-target="#decline" href=""><i class="fal fa-ban"></i> {{__('Resubmit Compliance')}}</a>
                        @elseif($val->kyc_status=="APPROVED")
                        <a class="btn btn-sm btn-danger" href="{{route('admin.reject.kyc', ['id'=>$val->id])}}"><i class="fal fa-ban"></i> {{__('Reject')}}</a>
                        <a class='btn btn-sm btn-primary' data-toggle="modal" data-target="#decline" href=""><i class="fal fa-ban"></i> {{__('Resubmit Compliance')}}</a>
                        @elseif($val->kyc_status=="DECLINED")
                        <a class="btn btn-sm btn-neutral" href="{{route('admin.approve.kyc', ['id'=>$val->id])}}"><i class="fal fa-check"></i> {{__('Approve')}}</a>
                        <a class='btn btn-sm btn-primary' data-toggle="modal" data-target="#decline" href=""><i class="fal fa-ban"></i> {{__('Resubmit Compliance')}}</a>
                        @elseif($val->kyc_status=="RESUBMIT")
                        <a class="btn btn-sm btn-neutral" href="{{route('admin.approve.kyc', ['id'=>$val->id])}}"><i class="fal fa-check"></i> {{__('Approve')}}</a>
                        <a class="btn btn-sm btn-danger" href="{{route('admin.reject.kyc', ['id'=>$val->id])}}"><i class="fal fa-ban"></i> {{__('Reject')}}</a>
                        <a class='btn btn-sm btn-primary' data-toggle="modal" data-target="#decline" href=""><i class="fal fa-ban"></i> {{__('Resubmit Compliance')}}</a>
                        @endif
                        @else
                        <p>{{__('Not yet uploaded')}}</p>
                        @endif
                        <div class="modal fade" id="decline" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
                            <div class="modal-dialog modal- modal-dialog-centered modal-md" role="document">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <form action="{{route('admin.resubmit.kyc', ['id'=>$val->id])}}" method="post">
                                            @csrf
                                            <div class="form-group row">
                                                <div class="col-lg-12">
                                                    <input name="id" value="{{$val->id}}" type="hidden">
                                                    <textarea type="text" name="reason" class="form-control" rows="5" placeholder="{{__('Provide reason for decline')}}" required></textarea>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <button type="submit" class="btn btn-success btn-block">{{__('Decline')}}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <p>{{__('Joined:')}} {{date("Y/m/d h:i:A", strtotime($client->created_at))}}</p>
                        <p>{{__('Last Login:')}} {{date("Y/m/d h:i:A", strtotime($client->last_login))}}</p>
                        <p>{{__('Last Updated:')}} {{date("Y/m/d h:i:A", strtotime($client->updated_at))}}</p>
                        <p>{{__('IP Address:')}} {{$client->ip_address}}</p>
                        <p>{{__('Country')}}: {{$client->getCountry()->name}}</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">{{__('Audit Logs')}}</h3>
                    </div>
                    <div class="table-responsive py-4">
                        <table class="table table-flush" id="datatable-buttons">
                            <thead>
                                <tr>
                                    <th>{{__('S / N')}}</th>
                                    <th>{{__('Reference ID')}}</th>
                                    <th>{{__('Log')}}</th>
                                    <th>{{__('Created')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($audit as $k=>$val)
                                <tr>
                                    <td>{{++$k}}.</td>
                                    <td>#{{$val->trx}}</td>
                                    <td>{{$val->log}}</td>
                                    <td>{{date("Y/m/d h:i:A", strtotime($val->created_at))}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @stop