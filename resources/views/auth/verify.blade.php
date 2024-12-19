@extends('layouts.auth')

@section('title', 'Verify Your Email Address')

@section('content')
<div class="col-lg-12 mx-auto mb-4">
    <div class="d-flex justify-content-center py-4">
        <h2>{{ __('Verify Your Email Address') }}</h2>
    </div>
    <div class="card mb-3">
        <div class="card-body p-5">

            @if (session('resent'))
            <div class="alert alert-success" role="alert">
                {{ __('A fresh verification link has been sent to your email address.') }}
            </div>
            @endif

            <p>{{ __('Before proceeding, please check your email for a verification link.') }}</p>
            <p>{{ __('If you did not receive the email') }},
                <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                    @csrf
                    <button type="submit" class="btn btn-link p-0 m-0 align-baseline">{{ __('click here to request another') }}</button>.
                </form>
            </p>

        </div>
    </div>
</div>
@endsection