@extends('layouts.app')

@push('extra-js')
    <script type="text/javascript">
        $(document).ready(function () {
            $("#revoke-role-modal").on("show.bs.modal", function (event) {
                var relatedTarget = $(event.relatedTarget);

                var id = relatedTarget.data("id");
                var name = relatedTarget.data("name");

                var form = $(this).find("form#revoke_role");

                form.attr('action', route('roles.revoke', id));

                form.find('span#name').text(name);
            });

            $("#restore-role-modal").on("show.bs.modal", function (event) {
                var relatedTarget = $(event.relatedTarget);

                var id = relatedTarget.data("id");
                var name = relatedTarget.data("name");

                var form = $(this).find("form#restore_role");

                form.attr('action', route('roles.restore', id));

                form.find('span#name').text(name);
            });

            $("#destroy-role-modal").on("show.bs.modal", function (event) {
                var relatedTarget = $(event.relatedTarget);

                var id = relatedTarget.data("id");
                var name = relatedTarget.data("name");

                var form = $(this).find("form#destroy_role");

                form.attr('action', route('roles.destroy', id));

                form.find('span#name').text(name);
            });
        });
    </script>
@endpush

@section('content')
<div class="main-content">
    <div class="container-fluid">

        <div class="page-title">
            <h4>
                <a href="{{ route('roles.index') }}">Roles</a>
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
                                {{ $role->name }}
                            </p>
                        </div>
                        <div class="row">
                            <p class="col-12 mb-0 small text-uppercase">
                                Description
                            </p>
                            <p class="col-12 bold">
                                {{ $role->description }}
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
                                {{ $role->created_at }}
                            </p>
                        </div>
                        <div class="row">
                            <p class="col-12 mb-0 small text-uppercase">
                                Updated At
                            </p>
                            <p class="col-12 bold">
                                {{ $role->updated_at }}
                            </p>
                        </div>
                        <div class="row">
                            <p class="col-12 mb-0 small text-uppercase">
                                Deleted At
                            </p>
                            <p class="col-12 bold">
                                {{ $role->deleted_at }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-block pb-0">
                        <div class="row">
                            @if($role->deleted_at)
                                <p class="col">
                                    <a href="" data-toggle="modal" data-target="#restore-role-modal"
                                    data-id="{{ $role->id }}" data-name="{{ $role->name }}"
                                        class="btn btn-success btn-sm btn-block">
                                        <i class="fa fa-refresh"></i>&nbsp;Restore
                                    </a>
                                </p>
                                <p class="col">
                                    <a href="" data-toggle="modal" data-target="#destroy-role-modal"
                                    data-id="{{ $role->id }}" data-name="{{ $role->name }}"
                                        class="btn btn-danger btn-sm btn-block">
                                        <i class="fa fa-trash"></i>&nbsp;Delete
                                    </a>
                                </p>
                            @else
                                @if(auth_can('roles', 'update'))
                                <p class="col">
                                    <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-info btn-sm btn-block">
                                        <i class="fa fa-pencil"></i>&nbsp;Edit
                                    </a>
                                </p>
                                @endif

                                <p class="col">
                                    <a href="" data-toggle="modal" data-target="#revoke-role-modal"
                                    data-id="{{ $role->id }}" data-name="{{ $role->name }}"
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

@include('roles.modals.revoke')
@include('roles.modals.restore')
@include('roles.modals.destroy')

@endsection
