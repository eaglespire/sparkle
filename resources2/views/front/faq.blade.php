@extends('front.menu')
@section('css')

@stop
@section('content')
<section class="parallax effect-section gray-bg">
    <div class="container position-relative">
        <div class="row screen-40 align-items-center justify-content-center p-150px-t">
            <div class="col-lg-10 text-center">
                <h6 class="white-color-black font-w-500">{{__('How can we help?')}}</h6>
                <h1 class="display-4 black-color m-20px-b">{{$title}}</h1>
                <form method="post" action="{{route('faq-submit')}}">
                    @csrf
                    <div class="form-row">
                        <div class="col-md-12 text-center">
                            <div class="form-group">
                                <input type="text" name="search" placeholder="Search the knowledge base..." class="form-control">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<section class="section gray-bg">
    <div class="container">
        <div class="row">
            @foreach(getFaqCategory() as $val)
            <div class="col-lg-6 mb-5">
                <div class="p-10px-b" id="basics">
                    <h3>{{$val->name}}</h3>
                </div>
                <ul class="list-type-05">
                    @foreach(getFaq($val->id) as $vals)
                    <li><a href="{{route('answer', ['id'=>$vals->id, 'slug'=>$vals->slug])}}"><span class="d-block dark-color">{{$vals->question}}</span></a></li>
                    @endforeach
                </ul>
                @if(count(getFaq($val->id))>4)
                <a href="{{route('faq.all', ['id'=>$val->id, 'slug'=>$val->slug])}}"><span class="d-block dark-color font-w-600">{{__('See all')}}</span></a>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</section>
@stop