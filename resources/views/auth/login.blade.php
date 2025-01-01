@extends('layouts.auth')

@section('title', 'Login')

@section('content')

<div class="col-lg-12 mx-auto mb-4">
    <div class="d-flex justify-content-center py-4">
        <h2>{{ __('Login') }}</h2>
    </div>
    <div class="card mb-3">
        <div class="card-body p-5">

            @if (session('message'))
            <div class="alert alert-danger">
                {{ session('message') }}
            </div>
            @endif

            <form class="row g-3 needs-validation" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="col-12">
                    <label for="email" class="form-label">Email Or Username<span class="text-danger">*</span></label>
                    <div class="input-group has-validation">
                        <input type="text" name="email"
                            class="form-control @error('email') is-invalid @enderror" id="email" value="{{ old('email') }}"
                            required>
                        <div class="invalid-feedback">Please enter your email or username.</div>
                    </div>
                    @error('email')
                    <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-12 mt-2">
                    <label for="yourPassword" class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password"
                        class="form-control @error('password') is-invalid @enderror" id="yourPassword" required>
                    <div class="invalid-feedback">Please enter your password!</div>
                    @error('password')
                    <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-12 text-center mt-2">
                    <button class="btn btn-theme w-100" type="submit">Login</button>
                </div>

                <div class="col-12 text-center mt-2">
                    <p class="small mb-0">
                        <a href="{{ route('password.request') }}" class="text-dark fw-bold">
                            Reset Password
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection