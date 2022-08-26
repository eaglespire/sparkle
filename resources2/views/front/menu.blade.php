<!doctype html>
<html class="no-js" lang="en">

<head>
    <base href="{{url('/')}}" />
    <title>{{ $title }} - {{$set->site_name}}</title>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="robots" content="index, follow">
    <meta name="apple-mobile-web-app-title" content="{{$set->site_name}}" />
    <meta name="application-name" content="{{$set->site_name}}" />
    <meta name="msapplication-TileColor" content="#ffffff" />
    <meta name="description" content="{{$set->site_desc}}" />
    <link rel="shortcut icon" href="{{asset('asset/'.$logo->image_link2)}}" />
    <link href="{{asset('asset/fonts/fontawesome/css/all.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('asset/static/plugin/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('asset/static/plugin/font-awesome/css/all.min.css')}}" rel="stylesheet">
    <link href="{{asset('asset/static/plugin/et-line/style.css')}}" rel="stylesheet">
    <link href="{{asset('asset/static/plugin/themify-icons/themify-icons.css')}}" rel="stylesheet">
    <link href="{{asset('asset/static/plugin/ionicons/css/ionicons.min.css')}}" rel="stylesheet">
    <link href="{{asset('asset/static/plugin/owl-carousel/css/owl.carousel.min.css')}}" rel="stylesheet">
    <link href="{{asset('asset/static/plugin/magnific/magnific-popup.css')}}" rel="stylesheet">
    <link href="{{asset('asset/static/style/master.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('asset/dashboard/vendor/prism/prism.css')}}">
    <link rel="stylesheet" href="{{asset('asset/dashboard/css/docs.css')}}" type="text/css">
    <link rel="stylesheet" href="{{asset('asset/css/toast.css')}}" type="text/css">
    @yield('css')
    @include('partials.font')
</head>

<body data-spy="scroll" data-target="#navbar-collapse-toggle" data-offset="98">
    <!-- Header -->
    <header class="header-nav header-dark">
        <div class="fixed-header-bar">
            <!-- Header Nav -->
            <div class="navbar navbar-main navbar-expand-lg">
                <div class="container">
                    <a class="navbar-brand" href="{{url('/')}}">
                        <img class="nav-img" alt="logo" src="{{asset('asset/'.$logo->dark)}}">
                    </a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-main-collapse" aria-controls="navbar-main-collapse" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse navbar-collapse-overlay collaspe show" id="navbar-main-collapse">
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('pricing')}}">{{__('Pricing')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('developers')}}">{{__('Developers')}}</a>
                            </li>
                            <li class="nav-item mm-in px-dropdown">
                                <a class="nav-link">{{__('Help')}}</a>
                                <i class="fa fa-angle-down px-nav-toggle"></i>
                                <ul class="px-dropdown-menu mm-dorp-in">
                                    <li><a href="{{route('faq')}}">{{__('Knowledge base')}}</a></li>
                                    <li><a href="{{route('contact')}}">{{__('Contact us')}}</a></li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('blog')}}">{{__('News & Articles')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('about')}}">{{__('Why')}} {{$set->site_name}}</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- End Header Nav -->
        </div>
    </header>
    <!-- Header End -->
    <!-- Main -->
    <main>
        @yield('content')
        <footer class="dark-bg footer effect-section">
            <div class="footer-top">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="row">
                                <div class="col-lg-12">
                                    <p class="text-white">{{$set->site_desc}}</p>
                                    <ul class="list-unstyled links-white footer-link-1">
                                        @if($set->mobile!=null)
                                        <li><a href="javascript:void;"><i class="fal fa-phone-alt"></i> {{$set->mobile}}</a></li>
                                        @endif
                                        @if($set->email!=null)
                                        <li><a href="javascript:void;"><i class="fal fa-envelope"></i> {{$set->email}}</a></li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-lg-3 m-15px-tb">
                                    <h5 class="footer-title text-white">
                                        {{__('Quick link')}}
                                    </h5>
                                    <ul class="list-unstyled links-white footer-link-1">
                                        <li><a href="{{route('developers')}}">{{__('Developers')}}</a></li>
                                        <li><a href="{{route('pricing')}}">{{__('Pricing')}}</a></li>
                                        <li><a href="{{route('blog')}}">{{__('News & Articles')}}</a></li>
                                        <li><a href="{{route('about')}}">{{__('Why')}} {{$set->site_name}}</a></li>
                                    </ul>
                                </div>                              
                                <div class="col-lg-3 m-15px-tb">
                                    <h5 class="footer-title text-white">
                                        {{__('Help')}}
                                    </h5>
                                    <ul class="list-unstyled links-white footer-link-1">
                                        <li><a href="{{route('contact')}}">{{__('Contact us')}}</a></li>
                                        <li><a href="{{route('faq')}}">{{__('Knowledge base')}}</a></li>
                                        <li><a href="{{route('terms')}}">{{__('Terms of Use')}}</a></li>
                                        <li><a href="{{route('privacy')}}">{{__('Privacy Policy')}}</a></li>
                                    </ul>
                                </div>
                                <div class="col-lg-3 m-15px-tb">
                                    <h5 class="footer-title text-white">
                                        {{__('More')}}
                                    </h5>
                                    <ul class="list-unstyled links-white footer-link-1">
                                        @foreach($pages as $vpages)
                                        @if(!empty($vpages))
                                        <li><a href="{{asset('')}}page/{{$vpages->id}}">{{$vpages->title}}</a></li>
                                        @endif
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="col-lg-3 m-15px-tb">
                                    <h5 class="footer-title text-white">
                                        {{__('Social Media')}}
                                    </h5>
                                    <ul class="list-unstyled links-white footer-link-1">
                                        @foreach($social as $socials)
                                        @if(!empty($socials->value))
                                        <li><a href="{{$socials->value}}">{{ucwords($socials->type)}}</a></li>
                                        @endif
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-bottom footer-border-dark">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6 text-center text-md-right m-5px-tb">
                            <ul class="nav justify-content-center justify-content-md-start links-dark font-small footer-link-1">
                            </ul>
                        </div>
                        <div class="col-md-6 text-center text-md-right m-5px-tb">
                            <p class="m-0px font-small text-white">{{$set->site_name}} &copy; {{date('Y')}}. {{__('All Rights Reserved')}}.</p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        </div>
        {!!$set->livechat!!}
        {!!$set->analytic_snippet!!}
        <script>
            var urx = "{{asset('/')}}";
        </script>
        <script src="{{asset('asset/static/js/jquery-3.2.1.min.js')}}"></script>
        <script src="{{asset('asset/static/js/jquery-migrate-3.0.0.min.js')}}"></script>
        <script src="{{asset('asset/static/plugin/appear/jquery.appear.js')}}"></script>
        <script src="{{asset('asset/static/plugin/bootstrap/js/popper.min.js')}}"></script>
        <script src="{{asset('asset/static/plugin/bootstrap/js/bootstrap.js')}}"></script>
        <script src="{{asset('asset/static/js/custom.js')}}"></script>
        <script src="{{asset('asset/js/toast.js')}}"></script>
        <script src="{{asset('asset/dashboard/vendor/prism/prism.js')}}"></script>
        @yield('script')
        @if (session('success'))
        <script>
            "use strict";
            toastr.success("{{ session('success') }}");
        </script>
        @endif

        @if (session('alert'))
        <script>
            "use strict";
            toastr.warning("{{ session('alert') }}");
        </script>
        @endif

</body>

</html>