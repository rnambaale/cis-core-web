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
                                            @if (session('resent'))
                                                <div class="alert alert-success" role="alert">
                                                    {{ __('A fresh verification link has been sent to your email address.') }}
                                                </div>
                                            @endif

                                            <div class="pdd-horizon-30 pdd-vertical-30">
                                                <div class="mrg-btm-30">
                                                    <img class="img-responsive inline-block" src="{{ asset('espire/images/logo/logo.png') }}" alt="">
                                                    <h2 class="inline-block float-right no-mrg-vertical pdd-top-15">{{ __('Verify Your Email Address') }}</h2>
                                                </div>

                                                <p>{{ __('Before proceeding, please check your email for a verification link.') }}</p>

                                                <p>{{ __('If you did not receive the email') }},</p>

                                                <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-link p-0 m-0 align-baseline">{{ __('click here to request another') }}</button>.
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
