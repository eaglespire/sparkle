@extends('front.menu')
@section('css')

@stop
@section('content')
<section class="parallax effect-section gray-bg">
    <div class="container position-relative">
        <div class="row screen-40 align-items-center justify-content-center p-150px-t">
            <div class="col-lg-10 text-center">
                <h6 class="white-color-black font-w-500">{{$set->title}}</h6>
                <h1 class="display-4 black-color m-20px-b">{{$faq->question}}</h1>
            </div>
        </div>
    </div>
</section>
<section class="section gray-bg">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 p-40px-r lg-p-15px-r md-m-15px-tb">
                <div class="article box-shadow">
                    <div class="article-content">
                        <p>{!!$faq->answer!!}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@stop