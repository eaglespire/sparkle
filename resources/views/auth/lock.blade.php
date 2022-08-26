@extends('auth.menu')

@section('content')
<div class="main-content">
    <!-- Header -->
    <div class="header py-9 pt-7">
      <div class="container">
        <div class="header-body text-center mb-7">
        </div>
      </div>
    </div>
    <!-- Page content -->
    <div class="container mt--8 pb-5">
      <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">
          <div class="card card-profile border-0 mb-0">
            <div class="card-body px-lg-5 py-lg-5">
                <div class="text-center text-dark">
                    <h2 class="text-dark font-weight-bolder">{{__('Unlock Script') }}</h2>
                    <p>How to unlock script, add a valid purchase code and domain name registered to your license on boomchart to core/.env</p>
                    <p>
                    <?php 
                    session_start();
                    echo $_SESSION["error"]; 
                    session_destroy()
                    ?></p>
                </div>
            </div>
          </div>
          <div class="row justify-content-center mt-5">
            <a href="{{url('/')}}"><i class="fal fa-sync"></i> Refresh</a>
          </div>
        </div>
      </div>
    </div>
@stop

<script>
window.history.replaceState({}, document.title, "/" + "zebra");
</script>