<!doctype html>
<html class="no-js" lang="en">

<head>
  <base href="{{url('/')}}" />
  <title>{{ $title }} | {{$set->site_name}}</title>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1" />
  <meta name="robots" content="index, follow">
  <meta name="apple-mobile-web-app-title" content="{{$set->site_name}}" />
  <meta name="application-name" content="{{$set->site_name}}" />
  <meta name="msapplication-TileColor" content="#ffffff" />
  <meta name="description" content="{{$set->site_desc}}" />
  <link rel="shortcut icon" href="{{asset('asset/'.$logo->image_link2)}}" />
  <link rel="stylesheet" href="{{asset('asset/css/toast.css')}}" type="text/css">
  <link rel="stylesheet" href="{{asset('asset/dashboard/vendor/@fortawesome/fontawesome-free/css/all.min.css')}}" type="text/css">
  <link rel="stylesheet" href="{{asset('asset/dashboard/vendor/datatables.net-bs4/css/dataTables.bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{asset('asset/dashboard/vendor/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{asset('asset/dashboard/vendor/datatables.net-select-bs4/css/select.bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{asset('asset/dashboard/css/argon.css?v=1.1.0')}}" type="text/css">
  <link href="{{asset('asset/fonts/fontawesome/css/all.css')}}" rel="stylesheet" type="text/css">
  <link href="{{asset('asset/fonts/fontawesome/styles.min.css')}}" rel="stylesheet" type="text/css">
  <style type="text/css">
    .preloader {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 9999;
      background-image: url("{{asset('asset/'.$logo->preloader)}}");
      background-repeat: no-repeat;
      background-color: #FFF;
      background-position: center;
    }
  </style>
  @yield('css')
  @include('partials.font')
</head>
<!-- header begin-->

<body style="background-color:{{$set->default_color}};">
@if($set->preloader==1)
<div class="preloader"></div>
@endif
  <nav id="navbar-main" class="navbar navbar-horizontal navbar-transparent navbar-main navbar-expand-lg navbar-dark">
    <div class="container">
      <div class="navbar-collapse navbar-custom-collapse collapse" id="navbar-collapse">
        <div class="navbar-collapse-header">
        </div>
      </div>
    </div>
  </nav>
  <!-- header end -->

  @yield('content')
  {!!$set->livechat!!}
  {!!$set->analytic_snippet!!}
  <!-- Argon Scripts -->
  <!-- Core -->
  <script src="{{asset('asset/dashboard/vendor/jquery/dist/jquery.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/bootstrap/dist/js/bootstrap.bundle.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/js-cookie/js.cookie.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/jquery.scrollbar/jquery.scrollbar.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/jquery-scroll-lock/dist/jquery-scrollLock.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/jvectormap-next/jquery-jvectormap.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/js/vendor/jvectormap/jquery-jvectormap-world-mill.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/datatables.net/js/jquery.dataTables.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/datatables.net-buttons/js/dataTables.buttons.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/datatables.net-buttons/js/buttons.html5.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/datatables.net-buttons/js/buttons.flash.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/datatables.net-buttons/js/buttons.print.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/datatables.net-select/js/dataTables.select.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/clipboard/dist/clipboard.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/select2/dist/js/select2.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/nouislider/distribute/nouislider.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/quill/dist/quill.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/dropzone/dist/min/dropzone.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/js/argon.js?v=1.1.0')}}"></script>
  <script src="{{asset('asset/tinymce/tinymce.min.js')}}"></script>
  <script src="{{asset('asset/tinymce/init-tinymce.js')}}"></script>
  <script src="{{asset('asset/js/toast.js')}}"></script>
</body>

</html>
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
<script type="text/javascript">
  $('.preloader').fadeOut(1000);
</script>