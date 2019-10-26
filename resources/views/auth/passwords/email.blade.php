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

                                                <div class="mrg-btm-30">
                                                    <img class="img-responsive inline-block" src="{{ asset('espire/images/logo/logo.png') }}" alt="">
                                                    <h2 class="inline-block float-right no-mrg-vertical pdd-top-15">{{ __('Reset Password') }}</h2>
                                                </div>

                                                <form method="POST" action="{{ route('password.email') }}">
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
                                                            <button type="submit" class="btn btn-primary">
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
            </div>
        </div>
    </div>
</div>
@endsection
