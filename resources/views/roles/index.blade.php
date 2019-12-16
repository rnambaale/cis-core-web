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
            <h4>Roles</h4>
            <a href="{{ route('roles.create') }}" class="btn btn-primary">Create Role</a>
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
                                <caption>List of roles.</caption>
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($roles as $role)
                                        <tr class="@if($role->deleted_at) strike-through @endif">
                                            <td>
                                                <a href="{{ route('roles.show', $role->id) }}">
                                                    {{ $role->name }}
                                                </a>
                                            </td>
                                            <td>
                                                {{ $role->description }}
                                            </td>
                                            <td class="text-center">
                                                @if($role->deleted_at)
                                                    @if(auth_can('roles', 'restore'))
                                                        <a href="" class="text-success" data-toggle="modal"
                                                            data-id="{{ $role->id }}" data-name="{{ $role->name }}"
                                                            data-target="#restore-role-modal">
                                                            <i class="fa fa-refresh px-1" title="Restore"></i>
                                                        </a>
                                                    @endif
                                                    @if(auth_can('roles', 'force-delete'))
                                                        <a href="#" class="text-danger" data-toggle="modal"
                                                            data-id="{{ $role->id }}" data-name="{{ $role->name }}"
                                                            data-target="#destroy-role-modal">
                                                            <i class="fa fa-trash px-1" title="Delete"></i>
                                                        </a>
                                                    @endif
                                                @else
                                                    @if(auth_can('roles', 'update'))
                                                        <a href="{{ route('roles.edit', $role->id) }}" class="text-info">
                                                            <i class="fa fa-pencil px-1" title="Edit"></i>
                                                        </a>
                                                    @endif

                                                    @if(auth_can('roles', 'soft-delete'))
                                                        <a href="" class="text-warning" data-toggle="modal"
                                                            data-id="{{ $role->id }}" data-name="{{ $role->name }}"
                                                             data-target="#revoke-role-modal">
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
                            {{ $roles->links() }}
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
