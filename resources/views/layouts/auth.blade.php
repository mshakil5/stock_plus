<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>  {{ config('app.name') }} </title>

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('admin/css/font-awesome.min.css')}}">
    <link rel="stylesheet" href="{{ asset('admin/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{ asset('css/login.css')}}">
    {{-- <link href="{{ elixir('css/app.css') }}" rel="stylesheet"> --}}

    <style>
        body {
            font-family: 'Lato';
        }

        .fa-btn {
            margin-right: 6px;
        }
    </style>
</head>
<body id="app-layout">
<nav class="navbar navbar-default navbar-static-top">

</nav>
    <style>
        .input-group-lg>.form-control, .input-group-lg>.input-group-addon, .input-group-lg>.input-group-btn>.btn{
            font-size: 15px;
        }
    </style>
    <div class="container">
        <div class="row">
            <div class="well col-md-5 center col-md-offset-3">

                <div class="panel panel-info">
                     {{-- <div class="panel-heading">Login</div>  --}}
                     @if(Session::has('error'))
                     <div class="alert alert-danger">
                       {{ Session::get('error')}}
                     </div>
                     @endif

                    <div class="panel-body">
                        
                        <div class="login-page">  
                            
                            @yield('content')
                        </div>
                    </div>
                </div>
                <div>Copyright © <?php echo date("Y"); ?> Next Link. All Rights Reserved.</div>
            </div>
        </div>
    </div>
    {{-- <link rel="stylesheet" href="{{ asset('js/jquery.min.js')}}"> --}}
    <link rel="stylesheet" href="{{ asset('js/bootstrap.min.js')}}">

{{-- <script src="{{ elixir('js/app.js') }}"></script> --}}

<script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script>
  //   $('#password').focusin(function(){
  //     $('form').addClass('up')
  //   });
  //   $('#password').focusout(function(){
  //     $('form').removeClass('up')
  //   });
  
  // // Panda Eye move
  // $(document).on( "mousemove", function( event ) {
  //   var dw = $(document).width() / 15;
  //   var dh = $(document).height() / 15;
  //   var x = event.pageX/ dw;
  //   var y = event.pageY/ dh;
  //   $('.eye-ball').css({
  //     width : x,
  //     height : y
  //   });
  // });
  
  // // validation
  
  
  // $('.btn').click(function(){
  //   $('form').addClass('wrong-entry');
  //     setTimeout(function(){ 
  //        $('form').removeClass('wrong-entry');
  //      },3000 );
  // });
  
  </script>
</body>
</html>
