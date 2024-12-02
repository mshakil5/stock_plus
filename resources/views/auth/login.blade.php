{{--  
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title> Login </title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <link rel="icon" type="image/x-icon" href="{{ asset('hsc.png')}}">


  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link
    href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
    rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link rel="stylesheet" href="{{ asset('admin/css/bootstrap.min.css')}}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.4/css/boxicons.min.css" rel="stylesheet">
  <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
  <link href="https://cdn.quilljs.com/1.3.7/quill.bubble.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">


  <!-- Template Main CSS File -->
  <link href="{{ asset('admin/css/style1.css')}}" rel="stylesheet">
</head>

<body>

  <section class="section register min-vh-100  ">
    <div class="min-vh-100">
      <div class="row  min-vh-100 mx-0">
        <div class="col-md-8 mx-auto">

          <div class="row h-100 d-flex align-items-center justify-content-center">
            <div class="col-lg-6 mx-auto mb-4">
              <div class="d-flex justify-content-center py-4">
                <a href="index.html" class="logo d-flex align-items-center w-auto">
                  <img src="assets/img/logo.svg" width="300">
                </a>
              </div>
              <div class="card mb-3">
                <div class="card-body p-5">
                  <form class="row g-3 needs-validation">

                    <div class="col-12">
                      <label for="yourUsername" class="form-label">Username</label>
                      <div class="input-group has-validation">
                        <input type="text" name="username" class="form-control" id="yourUsername" required>
                        <div class="invalid-feedback">Please enter your username.</div>
                      </div>
                    </div>

                    <div class="col-12">
                      <label for="yourPassword" class="form-label">Password</label>
                      <input type="password" name="password" class="form-control" id="yourPassword" required>
                      <div class="invalid-feedback">Please enter your password!</div>
                    </div>

                    <div class="col-12 text-center">
                      <button class="btn  w-100 btn-theme " type="submit">Login</button>
                    </div>
                    <div class="col-12">
                      <p class="small mb-0"> <a href="#" class="text-dark fw-bold">Reset Password</a></p>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>

        </div>
        <div class="d-none d-lg-flex col-lg-4 right-login-bg position-relative d-flex align-items-center">

          <div class="card shadow-sm rounded-4 overflow-hidden">
            <img src="https://picsum.photos/seed/picsum/300/100" class="img-fluid" alt="">

            <div class="card-body py-4">
              <h3 class="text-center text-capitalize">levelup your skill</h3>
              <h6 class="text-center ">sub title goes here </h6>
              <small class="card-text">This is a wider card with supporting text below as a natural lead-in to additional
                content. This content is a little bit longer.</small>
              <div class="d-flex justify-content-between align-items-center mt-4">
                <button class="btn btn-theme mx-auto text-uppercase"><small>visit the center</small></button>
              </div>
            </div>
          </div>

          <div class="p-4 position-absolute bottom-0 text-center start-0 end-0  fs-2">
            <a href="" class="text-white"><i class='bx bxl-facebook-circle'></i></a>
            <a href="" class="text-white"><i class='bx bxl-instagram-alt'></i> </a>
            <a href="" class="text-white"><i class='bx bxl-reddit'></i></a>
          </div>
        </div>
      </div>
    </div>

  </section>
  <!-- End #main -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
      class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>
  <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest"></script>
  <script src="https://cdn.jsdelivr.net/gh/posabsolute/php-email-form/validate.js"></script>


  <!-- Template Main JS File -->
  <script src="{{ asset('admin/js/main1.js')}}"></script>

</body>

</html>

--}}

@extends('layouts.auth')

@section('content')
<form method="POST" action="{{ route('login') }}">
    @csrf

        <div class="hand"></div>
        <div class="hand rgt"></div>
        <h1>Login</h1>

        @if(Session::has('error'))
        <div class="alert alert-danger">
        {{ Session::get('error')}}
        </div>
        @endif
        @if (isset($message))
        <span class="invalid-feedback" role="alert">
            <strong><p style="color: red">{{ $message }}</p></strong>
        </span>
        @endif

        <div class="form-group">

                <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="Email" required autocomplete="email" autofocus>

                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror



        </div>
        <div class="form-group">


            <input id="password" type="password" placeholder="Password" class="form-control @error('password') is-invalid @enderror" name="password" value="" required autocomplete="current-password">

            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror



        </div>
        <div class="form-group">
            {{-- <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember"
                       id="remember" {{ old('remember') ? 'checked' : '' }}>

                <label class="form-check-label" for="remember">
                    {{ __('Remember Me') }}
                </label>
            </div> --}}
        </div>
        <button type="submit" class="btn btn-primary">
            {{ __('Login') }}
        </button>

    </form>

    @if (Route::has('password.request'))
        <a class="btn btn-link" href="{{ route('password.request') }}">
        {{ __('Forgot Your Password?') }}
        </a>
    @endif

@endsection