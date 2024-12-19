@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
<div class="col-lg-12 mx-auto">
    <div class="d-flex justify-content-center py-4">
        <h2 class="text-center">{{ __('Reset Password') }}</h2>
    </div>
    <div class="card mb-3">
        <div class="card-body p-5">
            @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
            @endif

            <form class="row g-3 needs-validation" method="POST" action="{{ route('password.update') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="col-12">
                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                    <div class="input-group has-validation">
                        <input id="email" type="email" name="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
                        <div class="invalid-feedback">Please enter a valid email address.</div>
                    </div>
                    @error('email')
                    <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-12 mt-2">
                    <label for="password" class="form-label">New Password <span class="text-danger">*</span></label>
                    <div class="input-group has-validation">
                        <input id="password" type="password" name="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               required autocomplete="new-password">
                        <div class="invalid-feedback">Please enter your new password.</div>
                    </div>
                    @error('password')
                    <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-12 mt-2">
                    <label for="password-confirm" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                    <div class="input-group has-validation">
                        <input id="password-confirm" type="password" name="password_confirmation" 
                               class="form-control" required autocomplete="new-password">
                        <div class="invalid-feedback">Please confirm your new password.</div>
                    </div>
                </div>

                <div class="col-12 text-center mt-4">
                    <button type="submit" class="btn btn-theme w-100">
                        {{ __('Reset Password') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection