@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')

<div class="col-lg-12 mx-auto mb-4">
    <div class="d-flex justify-content-center py-4">
        <h2>{{ __('Login') }}</h2>
    </div>
    <div class="card mb-3">
        <div class="card-body p-5">

            @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
            @endif

            <form class="row g-3 needs-validation" method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="col-12">
                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                    <div class="input-group has-validation">
                        <input type="email" name="email" id="email"
                            class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required
                            autocomplete="email" autofocus>
                        <div class="invalid-feedback">Please enter your email address.</div>
                    </div>
                    @error('email')
                    <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-12 text-center mt-4">
                    <button class="btn btn-theme w-100" type="submit">Send Password Reset Link</button>
                </div>

                <div class="col-12 text-center mt-2">
                    <p class="small mb-0">
                        <a href="{{ route('login') }}" class="text-dark fw-bold">
                            Back to Login
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection