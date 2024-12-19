@extends('layouts.auth')

@section('title', 'Register')

@section('content')
<div class="col-lg-12 mx-auto mb-4">
    <div class="d-flex justify-content-center py-4">
        <h2>{{ __('Register') }}</h2>
    </div>
    <div class="card mb-3">
        <div class="card-body p-5">

            @if (session('message'))
            <div class="alert alert-danger">
                {{ session('message') }}
            </div>
            @endif

            <form class="row g-3 needs-validation" method="POST" action="{{ route('register') }}">
                @csrf

                <div class="col-12">
                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                    <div class="input-group has-validation">
                        <input id="name" type="text" name="name" 
                               class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name') }}" required autofocus>
                        <div class="invalid-feedback">Please enter your name.</div>
                    </div>
                    @error('name')
                    <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-12 mt-2">
                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                    <div class="input-group has-validation">
                        <input id="email" type="email" name="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email') }}" required>
                        <div class="invalid-feedback">Please enter a valid email address.</div>
                    </div>
                    @error('email')
                    <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-12 mt-2">
                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                    <div class="input-group has-validation">
                        <input id="password" type="password" name="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               required autocomplete="new-password">
                        <div class="invalid-feedback">Please enter a password.</div>
                    </div>
                    @error('password')
                    <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-12 mt-2">
                    <label for="password-confirm" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                    <div class="input-group has-validation">
                        <input id="password-confirm" type="password" name="password_confirmation" 
                               class="form-control" required autocomplete="new-password">
                        <div class="invalid-feedback">Please confirm your password.</div>
                    </div>
                </div>

                <div class="col-12 text-center mt-4">
                    <button type="submit" class="btn btn-theme w-100">
                        {{ __('Register') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection