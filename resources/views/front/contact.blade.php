@extends('front.menu')
@section('css')

@stop
@section('content')
<section class="parallax effect-section gray-bg">
    <div class="container position-relative">
        <div class="row screen-50 align-items-center justify-content-center p-100px-t">
            <div class="col-lg-10 text-center">
                <h1 class="display-4 black-color m-20px-b">{{__('Need a hand?')}}</h1>
                <h6 class="white-color-black font-w-500">{{__('We are always open and we welcome and questions you have for our team. If you wish to get in touch, please fill out the form below. Someone from our team will get back to you shortly.')}}</h6>
            </div>
        </div>
    </div>
</section>
<section class="section gray-bg" id="contact">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 m-15px-tb">
                <form class="rd-mailform" method="post" action="{{route('contact-submit')}}">
                    @csrf
                    <div class="form-row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input  type="text" name="name" placeholder="Rachel Roth" class="form-control">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input  type="email" name="email" placeholder="name@example.com"  class="form-control">
                            </div>
                        </div>                        
                        <div class="col-sm-12">
                            <div class="form-group">
                                <input  type="number" name="mobile" placeholder="12345678987"  class="form-control">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <textarea class="form-control" name="message" rows="3" placeholder="Hi there, I would like to ..."></textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <button class="m-btn m-btn-dark m-btn-radius" type="submit" name="send">{{__('Get Started')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@stop