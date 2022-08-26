@extends('front.menu')
@section('css')

@stop
@section('content')
<section class="parallax effect-section gray-bg">
    <div class="container position-relative">
        <div class="row screen-50 align-items-center justify-content-center p-100px-t">
            <div class="col-lg-10 text-center">
                <h6 class="white-color-black font-w-500">{{$set->title}}</h6>
                <h1 class="display-4 black-color m-20px-b">{{$title}}</h1>
            </div>
        </div>
    </div>
</section>
<section class="section gray-bg">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 blog-listing p-40px-r lg-p-15px-r">
                <div class="row">
                    @foreach($posts as $vblog)
                    <div class="col-sm-6">
                        <div class="card blog-grid-1 box-shadow-hover">
                            <div class="blog-img">
                                <a href="{{url('/')}}/single/{{$vblog->id}}/{{str_slug($vblog->title)}}">
                                    <img src="{{asset('asset/thumbnails/'.$vblog->image)}}" title="" alt="">
                                </a>
                                <span class="date">{{date("j", strtotime($vblog->created_at))}}<span>{{date("M", strtotime($vblog->created_at))}}</span></span>
                            </div>
                            <div class="card-body blog-info">
                                <h5>
                                    <a href="{{url('/')}}/single/{{$vblog->id}}/{{str_slug($vblog->title)}}">{!! str_limit($vblog->title, 40);!!}..</a>
                                </h5>
                                <p class="m-0px">{!! str_limit($vblog->details, 80);!!}</p>
                                <div class="btn-bar">
                                    <a class="m-link-theme" href="{{url('/')}}/single/{{$vblog->id}}/{{str_slug($vblog->title)}}">Read more</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    {{$posts->render()}}
                </div>
            </div>
            @include('partials.sidebar')
        </div>
    </div>
</section>
@stop