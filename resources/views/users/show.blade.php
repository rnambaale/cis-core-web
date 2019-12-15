@extends('layouts.app')

@push('extra-js')
    <script type="text/javascript">
        $(document).ready(function () {
            $("#revoke-user-modal").on("show.bs.modal", function (event) {
                var relatedTarget = $(event.relatedTarget);

                var id = relatedTarget.data("id");
                var name = relatedTarget.data("name");

                var form = $(this).find("form#revoke_user");

                form.attr('action', route('users.revoke', id));

                form.find('span#name').text(name);
            });

            $("#restore-user-modal").on("show.bs.modal", function (event) {
                var relatedTarget = $(event.relatedTarget);

                var id = relatedTarget.data("id");
                var name = relatedTarget.data("name");

                var form = $(this).find("form#restore_user");

                form.attr('action', route('users.restore', id));

                form.find('span#name').text(name);
            });

            $("#destroy-user-modal").on("show.bs.modal", function (event) {
                var relatedTarget = $(event.relatedTarget);

                var id = relatedTarget.data("id");
                var name = relatedTarget.data("name");

                var form = $(this).find("form#destroy_user");

                form.attr('action', route('users.destroy', id));

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
                <a href="{{ route('users.index') }}">Users</a>
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
                                {{ $user->name }}
                            </p>
                        </div>
                        <div class="row">
                            <p class="col-12 mb-0 small text-uppercase">
                                Alias
                            </p>
                            <p class="col-12 bold">
                                {{ $user->alias }}
                            </p>
                        </div>
                        <div class="row">
                            <p class="col-12 mb-0 small text-uppercase">
                                Email
                            </p>
                            <p class="col-12 bold">
                                {{ $user->email }}
                            </p>
                        </div>

                        <div class="row">
                            <p class="col-12 mb-0 small text-uppercase">
                                Role
                            </p>
                            <p class="col-12 bold">
                                {{ $user->role->name }}
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
                                {{ $user->created_at }}
                            </p>
                        </div>
                        <div class="row">
                            <p class="col-12 mb-0 small text-uppercase">
                                Updated At
                            </p>
                            <p class="col-12 bold">
                                {{ $user->updated_at }}
                            </p>
                        </div>
                        <div class="row">
                            <p class="col-12 mb-0 small text-uppercase">
                                Deleted At
                            </p>
                            <p class="col-12 bold">
                                {{ $user->deleted_at }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-block pb-0">
                        <div class="row">
                            @if($user->deleted_at)
                                <p class="col">
                                    <a href="" data-toggle="modal" data-target="#restore-user-modal"
                                    data-id="{{ $user->id }}" data-name="{{ $user->name }}"
                                        class="btn btn-success btn-sm btn-block">
                                        <i class="fa fa-refresh"></i>&nbsp;Restore
                                    </a>
                                </p>
                                <p class="col">
                                    <a href="" data-toggle="modal" data-target="#destroy-user-modal"
                                    data-id="{{ $user->id }}" data-name="{{ $user->name }}"
                                        class="btn btn-danger btn-sm btn-block">
                                        <i class="fa fa-trash"></i>&nbsp;Delete
                                    </a>
                                </p>
                            @else
                                @if(auth_can('users', 'update'))
                                <p class="col">
                                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-info btn-sm btn-block">
                                        <i class="fa fa-pencil"></i>&nbsp;Edit
                                    </a>
                                </p>
                                @endif

                                <p class="col">
                                    <a href="" data-toggle="modal" data-target="#revoke-user-modal"
                                    data-id="{{ $user->id }}" data-name="{{ $user->name }}"
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

@include('users.modals.revoke')
@include('users.modals.restore')
@include('users.modals.destroy')

@endsection
