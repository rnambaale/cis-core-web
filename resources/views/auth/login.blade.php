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

                        <p class="">{{ __('Please login') }}</p>

                        <form method="POST" action="{{ route('login') }}" autocomplete="off">
                            @csrf
                            <div class="form-group">
                                <input
                                    type="email"
                                    name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    placeholder="{{ __('E-Mail Address') }}"
                                    value="{{ old('email') }}"
                                    required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <input
                                    type="password"
                                    name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="{{ __('Password') }}"
                                    required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="checkbox">
                                <input
                                    id="agreement"
                                    name="agreement"
                                    type="checkbox"
                                    {{ old('remember') ? 'checked' : '' }}
                                    >
                                <label for="agreement">{{ __('Remember Me') }}</label>
                            </div>

                            <button class="btn btn-info btn-block">{{ __('Login') }}</button>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}">{{ __('Forgot Password?') }}</a>
                            @endif                                
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
