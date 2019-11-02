@extends('layouts.app')

@section('content')
<div class="main-content">
    <div class="container-fluid">

        <div class="page-title">
            <h4>
                <a href="{{ route('facilities.index') }}">Facilities</a>
            </h4>
        </div>

        <div class="row">
            <div class="col-md-12">
                @include('flash::message')
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="card">
                    <div class="card-block pb-0">
                        <div class="row">
                            <p class="col-12 mb-0 small text-uppercase">
                                Name
                            </p>
                            <p class="col-12 bold">
                                {{ $facility->name }}
                            </p>
                        </div>
                        <div class="row">
                            <p class="col-12 mb-0 small text-uppercase">
                                Description
                            </p>
                            <p class="col-12 bold">
                                {{ $facility->description }}
                            </p>
                        </div>
                        <div class="row">
                            <p class="col-12 mb-0 small text-uppercase">
                                Address
                            </p>
                            <p class="col-12 bold">
                                {{ $facility->address }}
                            </p>
                        </div>
                        <div class="row">
                            <p class="col-12 mb-0 small text-uppercase">
                                Email
                            </p>
                            <p class="col-12 bold">
                                <a href="mailto:{{ $facility->email }}">
                                    {{ $facility->email }}
                                </a>
                            </p>
                        </div>
                        <div class="row">
                            <p class="col-12 mb-0 small text-uppercase">
                                Website
                            </p>
                            <p class="col-12 bold">
                                <a href="{{ $facility->website }}" target="_blank">
                                    {{ $facility->website }}
                                </a>
                            </p>
                        </div>
                        <div class="row">
                            <p class="col-12 mb-0 small text-uppercase">
                                Phone
                            </p>
                            <p class="col-12 bold">
                                <a href="tel:{{ $facility->phone }}">
                                    {{ $facility->phone }}
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="card">
                    <div class="card-block pb-0">
                        <div class="row">
                            <p class="col-12 mb-0 small text-uppercase">
                                Created At
                            </p>
                            <p class="col-12 bold">
                                {{ $facility->created_at }}
                            </p>
                        </div>
                        <div class="row">
                            <p class="col-12 mb-0 small text-uppercase">
                                Updated At
                            </p>
                            <p class="col-12 bold">
                                {{ $facility->updated_at }}
                            </p>
                        </div>
                        <div class="row">
                            <p class="col-12 mb-0 small text-uppercase">
                                Deleted At
                            </p>
                            <p class="col-12 bold">
                                {{ $facility->deleted_at }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-block pb-0">
                        <div class="row">
                            <p class="col">
                                <a href="{{ route('facilities.edit', $facility->id) }}" class="btn btn-info btn-sm btn-block">
                                    <i class="fa fa-pencil"></i>&nbsp;Edit
                                </a>
                            </p>
                            @if($facility->deleted_at)
                                <p class="col">
                                    <a href="" data-toggle="modal" data-target="#restore-facility-modal"
                                        class="btn btn-success btn-sm btn-block">
                                        <i class="fa fa-refresh"></i>&nbsp;Restore
                                    </a>
                                </p>
                                <p class="col">
                                    <a href="" data-toggle="modal" data-target="#destroy-facility-modal"
                                        class="btn btn-danger btn-sm btn-block">
                                        <i class="fa fa-trash"></i>&nbsp;Delete
                                    </a>
                                </p>
                            @else
                                <p class="col">
                                    <a href="" data-toggle="modal" data-target="#revoke-facility-modal"
                                        class="btn btn-warning btn-sm btn-block">
                                        <i class="fa fa-ban"></i>&nbsp;Revoke
                                    </a>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('facilities.modals.revoke')
@include('facilities.modals.restore')
@include('facilities.modals.destroy')

@endsection
