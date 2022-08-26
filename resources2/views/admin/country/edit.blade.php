@extends('master')

@section('content')
<div class="container-fluid mt--6">
    <div class="content-wrapper">
        <a href="{{route('admin.currency')}}" class="btn btn-neutral mb-3"><i class="fal fa-caret-left"></i> {{__('Go back')}}</a>
        <a data-toggle="modal" data-target="#create" href="" class="btn btn-neutral mb-3"><i class="fal fa-plus"></i> {{__('Add conversion rate ')}}</a>
        <div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="mb-0 h3 font-weight-bolder">{{__('Add Rates')}}</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-2">This is just to enable your clients convert this currency to other currencies at your defined rate</p>
                        <form action="{{route('create.currency.rate', ['id'=>$val->id])}}" method="post">
                            @csrf
                            <div class="form-group">
                                <select class="form-control select" name="to_currency" required>
                                    <option value="">{{__('Select currency')}}</option>
                                    @if(count(getAllCountryExcept($val->id))>0)
                                    @foreach(getAllCountryExcept($val->id) as $xal)
                                    <option value="{{$xal->id}}">{{$xal->real->currency}}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-12">{{__('Rate for 1')}}{{$val->real->currency}}</label>
                                <div class="col-lg-12">
                                    <input type="number" name="rate" steps="any" placeholder="{{__('Rate')}}" autocomplete="off" class="form-control" required>
                                </div>
                            </div>
                            <div class="text-left">
                                <button type="submit" class="btn btn-success btn-block">{{__('Save')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h3 class="mb-0 h3 font-weight-bolder">{{__('Edit Currency')}}</h3>
                <p>1. Yapily api only supports EUR, GBP & USD currencies ensure the country you are selecting falls under this. Ensure you add all european countries to 1 application, same with EUR and USD to seperate applications. preconfigured banks where used, to go live you have to contact yapily support, negotiate on fees, add live institutions and go live</p>
                <p>2. For Card payment, flutterwave api was used, it supports major currencies, but ensure the country you are adding is covered by flutterwave.
                    To have access to flutterwave api for processing card transactions ensure you have submitted your PCI certificate as you must be PCI-DSS certified.</p>
                <p>3. If funding of account is enabled, payment methods enabled will be used as a medium of funding account.</p>
            </div>
            <div class="card-body">
                <form action="{{route('update.currency', ['id'=>$val->id])}}" method="post">
                    @csrf
                    <div class="row">
                        <input type="hidden" id="secret" value="{{$val->real->currency}}">
                        <label class="col-form-label col-lg-12">{{__('Bank format for Payout')}}</label>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <select class="form-control select" name="bank_format" required>
                                    <option value="">{{__('Bank format')}}</option>
                                    <option value="us" @if($val->bank_format=="us") selected @endif>US - Routing number</option>
                                    <option value="uk" @if($val->bank_format=="uk") selected @endif>UK - Account number & sort code</option>
                                    <option value="eur" @if($val->bank_format=="eur") selected @endif>EUR - Iban</option>
                                    <option value="normal" @if($val->bank_format=="normal") selected @endif>Normal - Bank name, account no & acct name,(You have to add Banks for withdrawal to work)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-lg-12">{{__('Withdraw Settlement Date (System skips weekends)')}}</label>
                        <div class="col-lg-12">
                            <div class="input-group">
                                <input type="text" name="duration" autocomplete="off" value="{{$val->duration}}" placeholder="{{__('Withdraw Settlement Date (System skips weekends)')}}" class="form-control" required>
                                <span class="input-group-prepend">
                                    <span class="input-group-text">Day</span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <p>Swap amount is very important if converation rates are provided</p>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label class="col-form-label col-lg-12">{{__('Minimum swap amount')}}</label>
                                <div class="col-lg-12">
                                    <div class="input-group">
                                        <input type="number" id="swap_min_amount" name="swap_min_amount" placeholder="{{__('Minimum swap amount')}}" value="{{$val->swap_min_amount}}" autocomplete="off" class="form-control" required>
                                        <span class="input-group-append">
                                            <span class="input-group-text">{{$val->real->currency}}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label class="col-form-label col-lg-12">{{__('Maximum swap amount')}}</label>
                                <div class="col-lg-12">
                                    <div class="input-group">
                                        <input type="number" id="swap_max_amount" name="swap_max_amount" placeholder="{{__('Maximum swap amount')}}" value="{{$val->swap_max_amount}}" autocomplete="off" class="form-control" required>
                                        <span class="input-group-append">
                                            <span class="input-group-text">{{$val->real->currency}}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="charges">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group row">
                                    <label class="col-form-label col-lg-12">{{__('Minimum amount it can receive')}}</label>
                                    <div class="col-lg-12">
                                        <div class="input-group">
                                            <input type="number" name="min_amount" placeholder="{{__('Minimum amount it can receive')}}" value="{{$val->min_amount}}" autocomplete="off" class="form-control" required>
                                            <span class="input-group-append">
                                                <span class="input-group-text" id="min_currency">{{$val->real->currency}}</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-form-label col-lg-12">{{__('Payment Fiat Charge')}}</label>
                                    <div class="col-lg-12">
                                        <div class="input-group">
                                            <input type="number" step="any" name="fiat_charge" placeholder="{{__('Payment Fiat Charge')}}" value="{{$val->fiat_charge}}" autocomplete="off" class="form-control">
                                            <span class="input-group-append">
                                                <span class="input-group-text" id="payment_charge">{{$val->real->currency}}</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-form-label col-lg-12">{{__('Withdraw Fiat Charge')}}</label>
                                    <div class="col-lg-12">
                                        <div class="input-group">
                                            <input type="number" step="any" name="withdraw_fiat_charge" placeholder="{{__('Withdraw Fiat Charge')}}" value="{{$val->withdraw_fiat_charge}}" autocomplete="off" class="form-control">
                                            <span class="input-group-append">
                                                <span class="input-group-text" id="withdraw_charge">{{$val->real->currency}}</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group row">
                                    <label class="col-form-label col-lg-12">{{__('Maximum amount it can receive, leave empty for infinte')}}</label>
                                    <div class="col-lg-12">
                                        <div class="input-group">
                                            <input type="number" name="max_amount" placeholder="{{__('Maximum amount it can receive, leave empty for infinte')}}" value="{{$val->max_amount}}" autocomplete="off" class="form-control" placeholder="Leave empty for infinite amount">
                                            <span class="input-group-append">
                                                <span class="input-group-text" id="max_currency">{{$val->real->currency}}</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-form-label col-lg-12">{{__('Payment Percent Charge')}}</label>
                                    <div class="col-lg-12">
                                        <div class="input-group">
                                            <input type="number" step="any" name="percent_charge" placeholder="{{__('Payment Percent Charge')}}" value="{{$val->percent_charge}}" autocomplete="off" class="form-control" required>
                                            <span class="input-group-prepend">
                                                <span class="input-group-text">%</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-form-label col-lg-12">{{__('Withdraw Percent Charge')}}</label>
                                    <div class="col-lg-12">
                                        <div class="input-group">
                                            <input type="number" step="any" name="withdraw_percent_charge" placeholder="{{__('Withdraw Percent Charge')}}" value="{{$val->withdraw_percent_charge}}" autocomplete="off" class="form-control" required>
                                            <span class="input-group-prepend">
                                                <span class="input-group-text">%</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row" id="payment_method">
                        <label class="col-form-label col-lg-12">{{__('Payment Methods')}}<span class="text-danger">*</span></label>
                        <div class="col-lg-12">
                            <div class="custom-control custom-control-alternative custom-checkbox">
                                <input type="checkbox" name="card" id=" customCheckLogin5" class="custom-control-input" value="1" @if($val->card==1) checked @endif>
                                <label class="custom-control-label" for=" customCheckLogin5">
                                    <span class="text-dark">{{__('Card')}}</span>
                                </label>
                            </div>
                            @if ($val->real->currency == "EUR" || $val->real->currency == "USD" || $val->real->currency == "GBP")
                            <div class="custom-control custom-control-alternative custom-checkbox" id="bank_account">
                                <input type="checkbox" name="bank_account" id="customCheckLogin6" class="custom-control-input" value="1" @if($val->bank_account==1) checked @endif>
                                <label class="custom-control-label" for="customCheckLogin6">
                                    <span class="text-dark">{{__('Bank account')}}</span>
                                </label>
                            </div>
                            @if ($val->real->currency == "USD")
                            <div id="USD" style="display:none">
                                <div class="form-group mt-2">
                                    <label class="col-form-label">{{__('Route number')}}</label>
                                    <input type="text" name="routing_no" id="routing_no" value="{{$val->routing_no}}" pattern="\d*" maxlength="9" minlength="9" class="form-control" placeholder="{{__('Routing number')}}">
                                </div>
                            </div>
                            @endif
                            @if ($val->real->currency == "EUR")
                            <div id="EUR" style="display:none">
                                <div class="form-group mt-2">
                                    <label class="col-form-label">{{__('Iban')}}</label>
                                    <input type="text" name="iban" id="iban" value="{{$val->iban}}" maxlength="16" minlength="16" class="form-control" placeholder="{{__('Iban')}}">
                                </div>
                            </div>
                            @endif
                            @if ($val->real->currency == "GBP")
                            <div id="GBP" style="display:none">
                                <div class="form-group mt-2">
                                    <label class="col-form-label">{{__('Account Number')}}</label>
                                    <input type="text" name="acct_no" value="{{$val->acct_no}}" id="acct_no" pattern="\d*" maxlength="8" minlength="8" class="form-control" placeholder="{{__('Account number')}}">
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">{{__('Sort Code')}}</label>
                                    <input type="text" name="sort_code" value="{{$val->sort_code}}" id="sort_code" pattern="\d*" maxlength="6" minlength="6" class="form-control" placeholder="{{__('Sort code')}}">
                                </div>
                            </div>
                            @endif
                            <div id="Yapily" style="display:none">
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <label class="col-form-label">{{__('Yapily Application Key')}}</label>
                                        <input type="text" id="auth_key" name="auth_key" value="{{$val->auth_key}}" placeholder="{{__('Yapily Auth Key')}}" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <label class="col-form-label">{{__('Yapily Auth Secret')}}</label>
                                        <input type="password" id="auth_secret" name="auth_secret" value="{{$val->auth_secret}}" placeholder="{{__('Yapily Auth Secret')}}" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <label class="col-form-label">{{__('First Name')}}</label>
                                        <input type="text" id="first_name" name="first_name" value="{{$val->first_name}}" placeholder="{{__('First Name')}}" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <label class="col-form-label">{{__('Last Name')}}</label>
                                        <input type="password" id="last_name" name="last_name" value="{{$val->last_name}}" placeholder="{{__('Last Name')}}" class="form-control">
                                    </div>
                                </div>
                            </div>
                            @endif
                            @if ($val->real->currency == "KES" || $val->real->currency == "ZMW" || $val->real->currency == "RWF" || $val->real->currency == "UGX" || $val->real->currency == "ZAR" || $val->real->currency == "GHS" || $val->real->currency == "XAF" || $val->real->currency == "XOF")
                            <div class="custom-control custom-control-alternative custom-checkbox">
                                <input type="checkbox" name="mobile_money" id=" customCheckLogin7" class="custom-control-input" value="1" @if($val->mobile_money==1) checked @endif>
                                <label class="custom-control-label" for=" customCheckLogin7">
                                    <span class="text-dark">{{__('Mobile Money')}}</span>
                                </label>
                            </div>
                            @endif
                        </div>
                        <label class="col-form-label col-lg-12">{{__('Features')}}<span class="text-danger">*</span></label>
                        <div class="col-lg-12">
                            <div class="custom-control custom-control-alternative custom-checkbox">
                                <input type="checkbox" name="funding" id="funding" class="custom-control-input" value="1" @if($val->funding==1) checked @endif>
                                <label class="custom-control-label" for="funding">
                                    <span class="text-dark">{{__('Funding')}}</span>
                                </label>
                            </div>
                            @if ($val->real->currency == "USD" || $val->real->currency == "NGN")
                            <div class="custom-control custom-control-alternative custom-checkbox">
                                <input type="checkbox" name="virtual_card" id="virtual_card" class="custom-control-input" value="1" @if($val->virtual_card==1) checked @endif>
                                <label class="custom-control-label" for="virtual_card">
                                    <span class="text-dark">{{__('Virtual Card')}}</span>
                                </label>
                            </div>
                            <div id="Virtual" @if($val->virtual_card!=1) style="display:none" @endif>
                                <div class="form-group row">
                                    <label class="col-form-label col-lg-12">{{__('Virtual Fiat Charge (card creation fee)')}}</label>
                                    <div class="col-lg-12">
                                        <div class="input-group">
                                            <input type="number" step="any" id="virtual_fiat_charge" name="virtual_fiat_charge" placeholder="{{__('Virtual Fiat Charge')}}" value="{{$val->virtual_fiat_charge}}" autocomplete="off" class="form-control">
                                            <span class="input-group-append">
                                                <span class="input-group-text" id="virtual_charge">{{$val->real->currency}}</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-form-label col-lg-12">{{__('Virtual Percent Charge (card creation fee)')}}</label>
                                    <div class="col-lg-12">
                                        <div class="input-group">
                                            <input type="number" step="any" id="virtual_percent_charge" name="virtual_percent_charge" placeholder="{{__('Virtual Percent Charge')}}" value="{{$val->virtual_percent_charge}}" autocomplete="off" class="form-control">
                                            <span class="input-group-prepend">
                                                <span class="input-group-text">%</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-form-label col-lg-12">{{__('Minimum amount it can create')}}</label>
                                    <div class="col-lg-12">
                                        <div class="input-group">
                                            <input type="number" id="virtual_min_amount" name="virtual_min_amount" placeholder="{{__('Minimum amount it can create')}}" value="{{$val->virtual_min_amount}}" autocomplete="off" class="form-control">
                                            <span class="input-group-append">
                                                <span class="input-group-text">{{$val->real->currency}}</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-form-label col-lg-12">{{__('Maximum amount it can create')}}</label>
                                    <div class="col-lg-12">
                                        <div class="input-group">
                                            <input type="number" id="virtual_max_amount" name="virtual_max_amount" placeholder="{{__('Maximum amount it can create')}}" value="{{$val->virtual_max_amount}}" autocomplete="off" class="form-control">
                                            <span class="input-group-append">
                                                <span class="input-group-text">{{$val->real->currency}}</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="text-left">
                        <button type="submit" class="btn btn-success btn-block">{{__('Save')}}</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="table-responsive">
                <table class="table table-flush">
                    <thead>
                        <tr>
                            <th class="text-left">{{__('Currency')}}</th>
                            <th class="">{{__('Rate')}}</th>
                            <th class="text-right"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(getCountryRates($val->id) as $k=>$dal)
                        <tr>
                            <td class="text-left">{{$dal->getCurrency->real->emoji.' '.$dal->getCurrency->real->currency}}</td>
                            <td class="">{{$dal->getCurrency->real->currency_symbol.number_format($dal->rate,2)}}</td>
                            <td class="text-right">
                                <a data-toggle="modal" data-target="#delete{{$dal->id}}" href="" class="btn btn-sm btn-danger">{{ __('Delete')}}</a>
                                <a data-toggle="modal" data-target="#edit{{$dal->id}}" href="" class="btn btn-sm btn-primary">{{ __('Edit')}}</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @foreach(getCountryRates($val->id) as $k=>$dal)
                <div class="modal fade" id="delete{{$dal->id}}" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
                    <div class="modal-dialog modal- modal-dialog-centered modal-md" role="document">
                        <div class="modal-content">
                            <div class="modal-body p-0">
                                <div class="card bg-white border-0 mb-0">
                                    <div class="card-header">
                                        <h3 class="mb-0">{{__('Are you sure you want to delete this?')}}</h3>
                                    </div>
                                    <div class="card-body px-lg-5 py-lg-5 text-right">
                                        <button type="button" class="btn btn-neutral btn-sm" data-dismiss="modal">{{ __('Close')}}</button>
                                        <a href="{{route('delete.currency.rate', ['id'=>$dal->id])}}" class="btn btn-danger btn-sm">{{ __('Proceed')}}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="edit{{$dal->id}}" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="mb-0 h3 font-weight-bolder">{{__('Edit Rates')}}</h3>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p class="mb-2">This is just to enable your clients convert this currency to other currencies at your defined rate</p>
                                <form action="{{route('update.currency.rate', ['id'=>$dal->id])}}" method="post">
                                    @csrf
                                    <div class="form-group row">
                                        <label class="col-form-label col-lg-12">{{__('Rate for 1')}}{{$val->real->currency}}</label>
                                        <div class="col-lg-12">
                                            <input type="number" name="rate" value="{{$dal->rate}}" steps="any" placeholder="{{__('Rate')}}" autocomplete="off" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="text-left">
                                        <button type="submit" class="btn btn-success btn-block">{{__('Save')}}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @stop
        @section('script')
        <script>
            $(function() {
                $("#customCheckLogin6").click(function() {
                    var secret = $('#secret').val();
                    if ($(this).is(":checked")) {
                        if (secret == "USD") {
                            $("#USD").show();
                            $("#EUR").hide();
                            $("#GBP").hide();
                            $("#Yapily").show();
                            $('#routing_no').attr('required', '');
                            $('#iban').removeAttr('required', '');
                            $('#sort_code').removeAttr('required', '');
                            $('#acct_no').removeAttr('required', '');
                        } else if (secret == "EUR") {
                            $("#EUR").show();
                            $("#USD").hide();
                            $("#GBP").hide();
                            $("#Yapily").show();
                            $('#iban').attr('required', '');
                            $('#routing_no').removeAttr('required', '');
                            $('#sort_code').removeAttr('required', '');
                            $('#acct_no').removeAttr('required', '');
                        } else if (secret == "GBP") {
                            $("#EUR").hide();
                            $("#USD").hide();
                            $("#GBP").show();
                            $("#Yapily").show();
                            $('#iban').removeAttr('required', '');
                            $('#routing_no').removeAttr('required', '');
                            $('#sort_code').attr('required', '');
                            $('#acct_no').attr('required', '');
                        }
                        $('#auth_key').attr('required', '');
                        $('#auth_secret').attr('required', '');
                        $('#first_name').attr('required', '');
                        $('#last_name').attr('required', '');
                    } else {
                        $("#USD").hide();
                        $("#EUR").hide();
                        $("#GBP").hide();
                        $("#Yapily").hide();
                        $('#routing_no').removeAttr('required', '');
                        $('#iban').removeAttr('required', '');
                        $('#sort_code').removeAttr('required', '');
                        $('#acct_no').removeAttr('required', '');
                        $('#auth_key').removeAttr('required', '');
                        $('#auth_secret').removeAttr('required', '');
                        $('#first_name').removeAttr('required', '');
                        $('#last_name').removeAttr('required', '');
                    }
                });
                $("#virtual_card").click(function() {
                    if ($("#virtual_card").is(":checked")) {
                        $("#Virtual").show();
                        $('#virtual_fiat_charge').attr('required', '');
                        $('#virtual_percent_charge').attr('required', '');
                        $('#virtual_min_amount').attr('required', '');
                        $('#virtual_max_amount').attr('required', '');
                    } else {
                        $("#Virtual").hide();
                        $('#virtual_fiat_charge').removeAttr('required', '');
                        $('#virtual_percent_charge').removeAttr('required', '');
                        $('#virtual_min_amount').removeAttr('required', '');
                        $('#virtual_max_amount').removeAttr('required', '');
                    }
                });
            });
        </script>
        @endsection