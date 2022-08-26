@extends('userlayout')

@section('content')
<div class="container-fluid mt--6">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12">
        <div class="nav-wrapper2">
          <ul class="nav nav-line-tabs nav-line-tabs-2x nav-stretch nav-trans b-b" id="tabs-icons-text" role="tablist">
            <li class="nav-item">
              <a class="nav-link mb-sm-3 mb-md-0 @if(route('user.documentation')==url()->current()) active @endif" id="tabs-icons-text-1-tab" href="{{route('user.documentation')}}" role="tab" aria-controls="tabs-icons-text-1" aria-selected="true">{{__('API')}}</a>
            </li>
            <li class="nav-item">
              <a class="nav-link mb-sm-3 mb-md-0 @if(route('documentation.html')==url()->current()) active @endif" id="tabs-icons-text-2-tab" href="{{route('documentation.html')}}" role="tab" aria-controls="tabs-icons-text-2" aria-selected="false">{{__('HTML Checkout')}}</a>
            </li>
            <li class="nav-item">
              <a class="nav-link mb-sm-3 mb-md-0 @if(route('documentation.js')==url()->current()) active @endif" id="tabs-icons-text-3-tab" href="{{route('documentation.js')}}" role="tab" aria-controls="tabs-icons-text-3" aria-selected="false">{{__('Inline Js')}}</a>
            </li>
            <li class="nav-item">
              <a class="nav-link mb-sm-3 mb-md-0 @if(route('documentation.plugin')==url()->current()) active @endif" id="tabs-icons-text-3-tab" href="{{route('documentation.plugin')}}" role="tab" aria-controls="tabs-icons-text-3" aria-selected="false">{{__('Plugins')}}</a>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="tab-content" id="myTabContent">
      <div class="tab-pane fade @if(route('user.documentation')==url()->current())show active @endif" id="tabs-icons-text-1" role="tabpanel" aria-labelledby="tabs-icons-text-1-tab">
        <div class="card border-top-0">
          <div class="card-body p-10 p-lg-15">
            <div class="pxb-10">
              <h2 class="anchor fw-bolder">{{__('Overview')}}</h2>
              <p>
                <strong>{{$set->site_name}}</strong>&nbsp; {{__('provides you access to your resources through RESTful endpoints.')}} {{__('so you can test the API. You would also be able to access your test API credential and keys from')}} <a href="{{route('user.api')}}">{{__('here')}}</a>
              </p>
            </div>
            <div class="pxb-10">
              <h2 class="anchor fw-bolder">{{__('HTTP Request Sample')}}</h2>
              <p>
                {{__('We would provide cURL request sample, just so you can quickly test each endpoint on your terminal or command line. Need a quick how-to for making cURL requests? just use an HTTP client such as Postman, like the rest of us!')}}
              </p>
            </div>
            <div class="pxb-10">
              <h2 class="anchor fw-bolder">{{__('Requests and Responses')}}</h2>
              <p>
                {{__('Both request body data and response data are formatted as JSON. Content type for responses are always of the type application/json. You can use the Tryba API in test mode, which does not affect your live data or interact with the banking networks. The API key you use to authenticate the request determines whether the request is live mode or test mode')}}
              </p>
            </div>
            <div class="pxb-10">
              <h2 class="anchor fw-bolder">{{__('Errors')}}</h2>
              <p class="mb-3">
                {{__('Errors are returned when one or more validation rules fail or unauthorized access to API. Examples include not passing required parameters e.g. not passing the transaction/provider ref during a re-query call will result in a validation error. Here\'s a sample below:')}}
              </p>
              <pre class="rounded">
              <code class="language-json" data-lang="json">
              {
                  "status": "failed",
                  "message": "tx_ref is required",
                  "data": "null"
              }
              </code>
            </pre>
            </div>
            <div class="pxb-10">
              <h2 class="anchor fw-bolder">{{__('Authentication')}}</h2>
              <p class="mb-3">
                {{__('Authentication to the API is performed via passing email and password through request body')}}
              </p>
              <pre class="rounded mb-3">
                <code class="language-json" data-lang="json">
                curl -X POST "{{url('/')}}/api/generate_token" 
                    -H "Accept: application/json" 
                    -d "{
                        "email": "your email",
                        "password": "your password"
                    }""
                </code>
              </pre>
              <p class="mb-3">{{__('Response')}}</p>
              <pre class="rounded">
              <code class="language-json" data-lang="json">
              {
                  "token": "6|82SUtz7Hs8qU0S72Mu6MiUGrKr8FZGZDU2GbnnLa"
              }
              </code>
            </pre>
            </div>
            <div class="pxb-10">
              <h2 class="anchor fw-bolder">{{__('Initiate Transaction')}}</h2>
              <div class="pxy-5">
                <div class="rounded border p-10 bg-light">
                  <div class="mb-3">
                    <label class="form-label">secret_key <span class="text-gray-600">string</span> <span class="text-danger">required</span></label>
                    <p>{{__('This is important for creating payment links')}}</p>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">callback_url <span class="text-default">url</span></label>
                    <p>{{__('This is your IPN url, it is important for receiving payment notification. Successful transactions redirects to this url after payment. {tx_ref} is returned, so you don\'t need to pass it with your url')}}</p>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">return_url <span class="text-default">url</span></label>
                    <p>{{__('URL to redirect to when a transaction is completed. This is useful for 3DSecure payments so we can redirect your customer back to a custom page you want to show them.')}}</p>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">tx_ref <span class="text-default">string</span> </label>
                    <p>{{__('Your transaction reference. This MUST be unique for every transaction')}}.</p>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">first_name <span class="text-default">string</span> <span class="text-danger">required</span></label>
                    <p>{{__('This is the first_name of your customer')}}.</p>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">last_name <span class="text-default">string</span> <span class="text-danger">required</span></label>
                    <p>{{__('This is the last_name of your customer')}}.</p>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">email <span class="text-default">string</span> <span class="text-danger">required</span></label>
                    <p>{{__('This is the email address of your customer. Transaction notification will be sent to this email address')}}</p>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">currency <span class="text-default">string</span> <span class="text-danger">required</span></label>
                    <p>{{__('Currency to charge in.')}} [@foreach(getAcceptedCountry() as $val) '{{$val->real->currency}}'@if(!$loop->last),@endif @endforeach]</p>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">amount <span class="text-default">int32</span> <span class="text-danger">required</span></label>
                    <p>{{__('Amount to charge the customer.')}}</p>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">customization <span class="text-default">array</span> <span class="text-danger">required</span></label>
                    <p>
                      {<br>
                      "title":"Title of payment",<br>
                      "description":"Description of payment",<br>
                      "logo":"https://assets.piedpiper.com/logo.png"<br>
                      }
                    </p>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">meta <span class="text-default">array</span> </label>
                    <p>{{__('You can pass extra information here.')}}</p>
                  </div>
                </div>
              </div>
              <pre class="rounded mb-3">
                <code class="language-json" data-lang="json">
                  curl -X POST "{{url('/')}}/api/payment" 
                  -H "Accept: application/json" 
                  -H "Authorization: Bearer {token}"
                  -d "{
                      "secret_key": "{secret_key}",
                      "amount": "100",
                      "currency": "NGN",
                      "email": "yourmail@example.com",
                      "first_name":"John",
                      "last_name":"Doe",
                      "callback_url": "https://webhook.site/9d0b00ba-9a69-44fa-a43d-a82c33c36fdc",
                      "return_url": "https://webhook.site",
                      "tx_ref": "2346vrcd",
                      "customization": {
                        "title": "Test Payment",
                        "description": "Payment Description",
                        "logo": "https://assets.piedpiper.com/logo.png"
                      },
                      "meta": {
                        "uuid": "uuid",
                        "response": "Response"
                      }
                  }"
                </code>
              </pre>
              <p class="mb-3">{{__('Response')}}</p>
              <pre class="rounded">
                <code class="language-json" data-lang="json">
                {
                    "message": "Payment link created",
                    "status": "success",
                    "data": {
                        "checkout_url": "{{route('checkout.url', ['id'=>'09229936784'])}}"
                    }
                }
                </code>
            </pre>
            </div>
            <div class="pxb-10">
              <h2 class="anchor fw-bolder">{{__('Validate a Transaction')}}</h2>
              <p class="mb-3">
                {{__('This shows you how to validate a transaction')}}
              </p>
              <pre class="rounded mb-3">
                  <code class="language-json" data-lang="json">
                  curl -X GET "{{url("/")}}/api/transaction/{tx_ref}/{public_key}"
                  -H "Accept: application/json" 
                  -H "Authorization: Bearer {token}"
                  </code>
              </pre>
              <p class="mb-3">{{__('Response')}}</p>
              <pre class="rounded">
                <code class="language-json" data-lang="json">
                {
                    "message": "Payment details",
                    "status": "success",
                    "data": {
                        "first_name": "John",
                        "last_name": "Doe",
                        "email": "yourmail@example.com",
                        "currency": "NGN",
                        "amount": "10,000.00",
                        "charge": "400.00",
                        "mode": "test",
                        "type": "API",
                        "status": "success",
                        "reference": "20193542126",
                        "tx_ref": "2346vrcdssdadffx",
                        "customization": {
                            "title": "Test Payment",
                            "description": "Payment Description",
                            "logo": "https://logo.png"
                        },
                        "meta": {
                            "uuid": "uuid",
                            "response": "Response"
                        },
                        "created_at": "2022-01-12T10:43:09.000000Z",
                        "updated_at": "2022-01-12T10:43:09.000000Z"
                    }
                }
                </code>
            </pre>
            </div>
          </div>
        </div>
      </div>
      <div class="tab-pane fade @if(route('documentation.html')==url()->current())show active @endif" id="tabs-icons-text-1" role="tabpanel" aria-labelledby="tabs-icons-text-1-tab">
        <div class="card border-top-0">
          <div class="card-body p-10 p-lg-15">
            <div class="pb-10">
              <h2 class="anchor fw-bolder">{{__('Integrating Website Payment')}}</h2>
              <p class="mb-3">
                {{__(' Receiving money on your website is now easy')}}. {{__('All you need to do is copy the html form code below to your website page')}}
              </p>
              <pre class="rounded mb-3">
                <code class="language-html" data-lang="html">
                &lt;form method="POST" action="{{route('submit.pay')}}" &gt;
                    &lt;input type="hidden" name="secret_key" value="{secret_key}" /&gt;
                    &lt;input type="hidden" name="callback_url" value="https://example.com/callbackurl" /&gt;
                    &lt;input type="hidden" name="return_url" value="https://example.com/returnurl" /&gt;
                    &lt;input type="hidden" name="tx_ref" value="2346vrcd" /&gt;
                    &lt;input type="hidden" name="amount" value="100" /&gt;
                    &lt;input type="hidden" name="currency" value="{{$user->getFirstBalance()->getCurrency->real->currency}}" /&gt;
                    &lt;input type="hidden" name="email" value="yourmail@example.com" /&gt;
                    &lt;input type="hidden" name="first_name" value="John" /&gt;
                    &lt;input type="hidden" name="last_name" value="Doe" /&gt;
                    &lt;input type="hidden" name="title" value="Test Payment" /&gt;
                    &lt;input type="hidden" name="description" value="Payment Description" /&gt;
                    &lt;input type="hidden" name="logo" value="https://example.com/logo.png" /&gt;
                    &lt;input type="hidden" name="meta" value="" /&gt;
                    &lt;input type="submit" value="submit" /&gt;
                &lt;/form&gt;
                </code>
            </pre>
            </div>
            <div class="pb-10">
              <h2 class="anchor fw-bolder">{{__('Initiate Transaction')}}</h2>
              <div class="pxy-5">
                <div class="rounded border p-10 bg-light">
                  <div class="mb-3">
                    <label class="form-label">secret_key <span class="text-gray-600">string</span> <span class="text-danger">required</span></label>
                    <p>{{__('This is important for creating payment links')}}</p>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">callback_url <span class="text-default">url</span></label>
                    <p>{{__('This is your IPN url, it is important for receiving payment notification. Successful transactions redirects to this url after payment. {tx_ref} is returned, so you don\'t need to pass it with your url')}}</p>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">return_url <span class="text-default">url</span></label>
                    <p>{{__('URL to redirect to when a transaction is completed. This is useful for 3DSecure payments so we can redirect your customer back to a custom page you want to show them.')}}</p>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">tx_ref <span class="text-default">string</span> </label>
                    <p>{{__('Your transaction reference. This MUST be unique for every transaction')}}.</p>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">first_name <span class="text-default">string</span> <span class="text-danger">required</span></label>
                    <p>{{__('This is the first_name of your customer')}}.</p>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">last_name <span class="text-default">string</span> <span class="text-danger">required</span></label>
                    <p>{{__('This is the last_name of your customer')}}.</p>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">email <span class="text-default">string</span> <span class="text-danger">required</span></label>
                    <p>{{__('This is the email address of your customer. Transaction notification will be sent to this email address')}}</p>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">currency <span class="text-default">string</span> <span class="text-danger">required</span></label>
                    <p>{{__('Currency to charge in.')}} [@foreach(getAcceptedCountry() as $val) '{{$val->real->currency}}'@if(!$loop->last),@endif @endforeach]</p>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">amount <span class="text-default">int32</span> <span class="text-danger">required</span></label>
                    <p>{{__('Amount to charge the customer.')}}</p>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">customization <span class="text-default">array</span> <span class="text-danger">required</span></label>
                    <p>
                      {<br>
                      "title":"Title of payment",<br>
                      "description":"Description of payment",<br>
                      "logo":"https://assets.piedpiper.com/logo.png"<br>
                      }
                    </p>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">meta <span class="text-default">array</span> </label>
                    <p>{{__('You can pass extra information here.')}}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="tab-pane fade @if(route('documentation.js')==url()->current())show active @endif" id="tabs-icons-text-1" role="tabpanel" aria-labelledby="tabs-icons-text-1-tab">
        <div class="card border-top-0">
          <div class="card-body p-10 p-lg-15">
            <div class="pb-10">
              <h2 class="anchor fw-bolder">{{__('Sample Inline Redirect Implementation')}}</h2>
              <p class="mb-3">
                You can embed {{$set->site_name}} on your page using our SparkleCheckout() JavaScript function. The function responds to your request in accordance with your request configurations. If you specify a callback_url in your request, the function will redirect your users to the provided callback URL when they complete the payment.
              <pre class="rounded mb-3">
                <code class="language-html" data-lang="html">
                &lt;form&gt;
                  &lt;script src="{{url('/')}}/asset/js/payment.js">&lt;/script&gt;
                  &lt;div id="message"&gt;&lt;/div&gt;
                  &lt;button type="button" onClick="makePayment()"&gt;Pay Now&lt;/button&gt;
                  &lt;/form&gt;
                &lt;script&gt;
                    function makePayment(){
                      SparkleCheckout({
                        "secret_key": "SEC-TEST-OtbJJZgchqInA8mYSfZby8ZS7ff7WD9i",
                        "tx_ref": "7657863465874",
                        "amount": "10000",
                        "currency": "{{$user->getFirstBalance()->getCurrency->real->currency}}",
                        "callback_url": "https://webhook.site/7657863465874",
                        "return_url": "https://webhook.site",
                        "customer":{
                          "email": "yourmail@example.com",
                          "first_name":"John",
                          "last_name":"Doe",
                        },
                        "customization": {
                          "title": "Test Payment",
                          "description": "Payment Description",
                          "logo": "https://assets.piedpiper.com/logo.png"
                        },
                        "meta": {
                          "uuid": "uuid",
                          "response": "Response"
                        }
                      });
                    }
                    &lt;/script&gt;
                </code>
              </pre>
            </div>
          </div>
        </div>
      </div>
      <div class="tab-pane fade @if(route('documentation.plugin')==url()->current())show active @endif" id="tabs-icons-text-1" role="tabpanel" aria-labelledby="tabs-icons-text-1-tab">
        <div class="mt-3">
          @foreach(getPlugins() as $val)
          <div class="card">
            <div class="card-header mb-0">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="fw-bold mb-0">{{$val->title}}</h2>
                  <p class="text-sm">{{$val->description}}</p>
                </div>
                <div class="col text-right">
                  <a href="{{route('plugin.download', ['id'=>$val->id])}}" class="text-default"><i class="fal fa-arrow-down"></i> {{__('Download Plugin')}}</a>
                </div>
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
  @stop