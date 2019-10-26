@extends('layouts.app-min')

@section('content')
<div class="authentication">
    <div class="sign-in-2">
        <div class="container-fluid no-pdd-horizon bg" style="background-image: url('{{ asset('espire/images/others/img-30.jpg') }}')">
            <div class="row">
                <div class="col-md-10 mr-auto ml-auto">
                    <div class="row">
                        <div class="mr-auto ml-auto full-height height-100 d-flex align-items-center">
                            <div class="vertical-align full-height">
                                <div class="table-cell">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    @include('flash::message')
                                                </div>
                                            </div>

                                            <div class="pdd-horizon-30 pdd-vertical-30">
                                                <div class="mrg-btm-30">
                                                    <img class="img-responsive inline-block" src="{{ asset('espire/images/logo/logo.png') }}" alt="">
                                                    <h2 class="inline-block float-right no-mrg-vertical pdd-top-15">{{ __('Login') }}</h2>
                                                </div>
                                                <p class="mrg-btm-15 font-size-13">Please enter your email and password to login</p>
                                                <form method="POST" action="{{ route('login') }}">
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

                                                    <div class="checkbox font-size-13 inline-block no-mrg-vertical no-pdd-vertical">
                                                        <input
                                                            id="agreement"
                                                            name="agreement"
                                                            type="checkbox"
                                                            {{ old('remember') ? 'checked' : '' }}
                                                            >
                                                        <label for="agreement">{{ __('Remember Me') }}</label>
                                                    </div>
                                                    <div class="float-right">
                                                        @if (Route::has('password.request'))
                                                            <a href="{{ route('password.request') }}">{{ __('Forgot Password?') }}</a>
                                                        @endif
                                                    </div>
                                                    <div class="mrg-top-20 text-right">
                                                        <button class="btn btn-info">{{ __('Login') }}</button>
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
            </div>
        </div>
    </div>
</div>
@endsection
