@extends('layouts.app')

@section('content')
<div class="main-content">
    <div class="container-fluid">
        <div class="page-title">
            <h4>Dashboard</h4>
        </div>
        <div class="row">
            <div class="col-md-12">
                @include('flash::message')
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <p>You are logged in!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
