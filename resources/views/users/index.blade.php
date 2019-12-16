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

        <div class="page-title d-flex justify-content-between mb-3">
            <h4 class="m-0">Users</h4>
            <a href="{{ route('users.create') }}" class="btn btn-sm btn-primary m-0">
                <i class="fa fa-plus"></i>&nbsp;Register
            </a>
        </div>

        <div class="row">
            <div class="col-md-12">
                @include('flash::message')
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-block">
                        <div class="table-overflow">
                            <table id="users" class="table table-striped table-hover no-wrap">
                                <caption>List of users.</caption>
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Website</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        <tr class="@if($user->deleted_at) strike-through @endif">
                                            <td>
                                                <a href="{{ route('users.show', $user->id) }}">
                                                    {{ $user->alias }}
                                                </a>
                                            </td>
                                            <td>
                                                {{ $user->name }}
                                            </td>
                                            <td>
                                                <a href="mailto:{{ $user->email }}">
                                                    {{ $user->email }}
                                                </a>
                                            </td>
                                            <td class="text-center">
                                                @if(auth_can('users', 'update'))
                                                    <a href="{{ route('users.edit', $user->id) }}" class="text-info">
                                                        <i class="fa fa-pencil px-1" title="Edit"></i>
                                                    </a>
                                                @endif
                                                @if($user->deleted_at)
                                                    @if(auth_can('users', 'restore'))
                                                        <a href="" class="text-success" data-toggle="modal"
                                                            data-id="{{ $user->id }}" data-name="{{ $user->name }}"
                                                            data-target="#restore-user-modal">
                                                            <i class="fa fa-refresh px-1" title="Restore"></i>
                                                        </a>
                                                    @endif
                                                    @if(auth_can('users', 'force-delete'))
                                                        <a href="#" class="text-danger" data-toggle="modal"
                                                            data-id="{{ $user->id }}" data-name="{{ $user->name }}"
                                                            data-target="#destroy-user-modal">
                                                            <i class="fa fa-trash px-1" title="Delete"></i>
                                                        </a>
                                                    @endif
                                                @else
                                                    @if(auth_can('users', 'soft-delete'))
                                                        <a href="" class="text-warning" data-toggle="modal"
                                                            data-id="{{ $user->id }}" data-name="{{ $user->name }}"
                                                             data-target="#revoke-user-modal">
                                                            <i class="fa fa-ban px-1" title="Revoke"></i>
                                                        </a>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center">
                            {{ $users->links() }}
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
