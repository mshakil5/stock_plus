@extends('layouts.auth')

@section('title', 'Confirm Password')

@section('content')
<div class="col-lg-12 mx-auto mb-4">
    <div class="d-flex justify-content-center py-4">
        <h2>{{ __('Confirm Password') }}</h2>
    </div>
    <div class="card mb-3">
        <div class="card-body p-5">
            <p class="text-muted text-center mb-4">{{ __('Please confirm your password before continuing.') }}</p>

            @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
            @endif

            <form class="row g-3 needs-validation" method="POST" action="{{ route('password.confirm') }}">
                @csrf

                <div class="col-12">
                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                    <div class="input-group has-validation">
                        <input id="password" type="password" name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            required autocomplete="current-password">
                        <div class="invalid-feedback">Please enter your password.</div>
                    </div>
                    @error('password')
                    <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-12 text-center mt-4">
                    <button type="submit" class="btn btn-theme w-100">Confirm Password</button>
                </div>

                <div class="col-12 text-center mt-2">
                    @if (Route::has('password.request'))
                    <p class="small mb-0">
                        <a href="{{ route('password.request') }}" class="text-dark fw-bold">
                            {{ __('Forgot Your Password?') }}
                        </a>
                    </p>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection