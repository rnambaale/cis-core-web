@extends('layouts.app-min')

@section('content')
<div class="auth">
    <div class="sign-in-2">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4 offset-md-4">
                    <div class="card login-card">

                        <div class="login-heading">
                            <h2 class="text-center">CIS Core Web</h2>
                        </div>

                        <div class="card-body">

                            <div class="pdd-horizon-30 pdd-vertical-30">

                                @if (session('status'))
                                    <div class="alert alert-success" role="alert">
                                        {{ session('status') }}
                                    </div>
                                @endif

                                <div class="row">
                                    <div class="col-md-12">
                                        @include('flash::message')
                                    </div>
                                </div>

                                <p class="">{{ __('Reset Password') }}</p>

                                <form method="POST" action="{{ route('password.email') }}" autocomplete="off">
                                    @csrf

                                    <div class="form-group">
                                        <input
                                            id="email"
                                            type="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            name="email"
                                            placeholder="{{ __('E-Mail Address') }}"
                                            value="{{ old('email') }}" required autocomplete="email" autofocus>

                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group row mb-0">
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-primary btn-block">
                                                {{ __('Send Password Reset Link') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
