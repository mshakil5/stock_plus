<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>@yield('title', 'Auth Page')</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <link href="{{ asset('admin/css/style1.css') }}" rel="stylesheet">
</head>

<body>
    <section class="section register min-vh-100">
        <div class="min-vh-100">
            <div class="row min-vh-100 mx-0">
                <div class="col-md-8 mx-auto">
                    <div class="row h-100 d-flex align-items-center justify-content-center">
                        <div class="col-lg-6 mx-auto mb-4">
                            <div class="d-flex justify-content-center py-4">
                                @yield('header')
                            </div>
                            <div class="card mb-3">
                                <div class="card-body p-5">
                                    @yield('content')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>

</html>