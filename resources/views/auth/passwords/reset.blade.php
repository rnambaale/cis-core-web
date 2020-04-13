@extends('layouts.app-min')

@section('content')
<div class="auth">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4 offset-md-4">
                <div class="card login-card">
                    
                    <div class="login-heading">
                        <h2 class="text-center">CIS Core Web</h2>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                @include('flash::message')
                            </div>
                        </div>

                        <p class="">{{ __('Reset Password') }}</p>

                        <form method="POST" action="{{ route('password.update') }}" autocomplete="off">
                            @csrf

                            <input type="hidden" name="token" value="{{ $token }}">

                            <input type="hidden" name="email_verified_at" value="{{ date('Y-m-d H:i:s') }}">

                            <div class="form-group">
                                <input
                                    type="email"
                                    name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    placeholder="{{ __('E-Mail Address') }}"
                                    value="{{ $email ?? old('email') }}"
                                    required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <input
                                    id="password"
                                    type="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    name="password"
                                    placeholder="{{ __('Password') }}"
                                    required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <input
                                    id="password-confirm"
                                    type="password" class="form-control"
                                    name="password_confirmation"
                                    placeholder="{{ __('Confirm Password') }}"
                                    required autocomplete="new-password">
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-block">
                                {{ __('Reset Password') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
