@extends('front.menu')
@section('css')

@stop
@section('content')
<section class="parallax effect-section gray-bg">
    <div class="container position-relative">
        <div class="row screen-40 align-items-center justify-content-center p-150px-t">
            <div class="col-lg-10 text-center">
                <h6 class="white-color-black font-w-500">{{$set->title}}</h6>
                <h1 class="display-4 black-color m-20px-b">{{$post->title}}</h1>
            </div>
        </div>
    </div>
</section>
<section class="section gray-bg">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 p-40px-r lg-p-15px-r md-m-15px-tb">
                <div class="article-img">
                    <img src="{{asset('asset/thumbnails/'.$post->image)}}" title="{{$post->title}}" alt="{{$post->title}}">
                </div>
                <div class="article box-shadow">
                    <div class="article-content">
                        <p>{!!$post->details!!}</p>
                    </div>
                </div>
            </div>
            @include('partials.sidebar')
        </div>
    </div>
</section>
@stop