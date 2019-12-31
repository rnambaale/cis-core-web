@extends('layouts.app')

@push('extra-js')
    {{-- <script src="{{ asset('js/pages/facilities-dt.js') }}"></script> --}}
    <script type="text/javascript">
        var can_update = {{ auth_can('roles', 'update') ? 1 : 0 }};
        var can_restore = {{ auth_can('roles', 'restore') ? 1 : 0 }};
        var can_soft_delete = {{ auth_can('roles', 'soft-delete') ? 1 : 0 }};
        var can_force_delete = {{ auth_can('roles', 'force-delete') ? 1 : 0 }};
        var can_assign_permissions = {{ auth_can('permissions', 'assign-permissions') ? 1 : 0 }};

        $(document).ready(function () {
            var roles_dt = $('table[id=roles]').DataTable({
                pageLength: 10,
                language: {
                    emptyTable: "No roles available",
                    info: "Showing _START_ to _END_ of _TOTAL_ roles",
                    infoEmpty: "Showing 0 to 0 of 0 roles",
                    infoFiltered: "(filtered from _MAX_ total roles)",
                    lengthMenu: "Show _MENU_ roles",
                    search: "Search roles:",
                    zeroRecords: "No roles match search criteria"
                },
                order: [[1, 'asc']],
                processing: true,
                serverSide: true,
                ajax: {
                    type: 'GET',
                    url: route('roles.dt'),
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    dataType: 'json'
                    // success: function(response) {
                    //     console.log(response);
                    // }
                },
                columnDefs: [
                    {
                        targets: 0,
                        name: 'roles.id',
                        data: 'id',
                        visible: false
                    },
                    {
                        targets: 1,
                        name: 'roles.name',
                        data: 'name',
                        render: function (data, type, row, meta) {
                            return '<a href="'+route('roles.show', row.id)+'">'+data+'</a>'
                        }
                    },
                    {
                        targets: 2,
                        name: 'roles.description',
                        data: 'description'
                    },
                    {
                        targets: 3,
                        name: 'roles.deleted_at',
                        data: 'deleted_at',
                        visible: false
                    },
                    {
                        targets: 4,
                        name: null,
                        data: null,
                        orderable: false,
                        searchable: false,
                        class: 'text-center',
                        render: function (data, type, row, meta) {

                            var edit = '<a href="'+route('roles.edit', row.id)+'" class="text-info"><i class="fa fa-pencil px-1" title="Edit"></i></a>';

                            var assignPermissions = '<a href="'+route('roles.permissions.update', row.id)+'" class="text-success"><i class="fa fa-pencil px-1" title="Permissions"></i></a>';

                            var softDelete = '<a href="" class="text-warning" data-toggle="modal"data-id="'+row.id+'" data-name="'+row.name+'"data-target="#revoke-facility-modal"><i class="fa fa-ban px-1" title="Revoke"></i></a>';

                            var restore = '<a href="" class="text-success" data-toggle="modal"data-id="'+row.id+'" data-name="'+row.name+'"data-target="#restore-facility-modal"><i class="fa fa-refresh px-1" title="Restore"></i></a>';

                            var forceDelete = '<a href="#" class="text-danger" data-toggle="modal"data-id="'+row.id+'" data-name="'+row.name+'"data-target="#destroy-facility-modal"><i class="fa fa-trash px-1" title="Delete"></i></a>';

                            // ...

                            var action = '';

                            if(row.deleted_at) {
                                if(can_restore) {
                                    action += restore;
                                }

                                if(can_force_delete) {
                                    action += forceDelete;
                                }
                            } else {
                                if(can_update) {
                                    action = edit;
                                }

                                if(can_assign_permissions) {
                                    action += assignPermissions;
                                }

                                if(can_soft_delete) {
                                    action += softDelete;
                                }
                            }

                            return action;
                        }
                    }
                ]
            });

            // https://stackoverflow.com/a/10318763/2732184

            var searchWait = 0;
            var searchWaitInterval;

            $('div.dataTables_filter input')
                .unbind('keypress keyup')
                .bind('keypress keyup', function(e) {
                    var item = $(this);
                    searchWait = 0;
                    if (!searchWaitInterval) searchWaitInterval = setInterval(function() {
                        // console.log({"delay": searchWait});
                        if (searchWait >= 3) {
                            clearInterval(searchWaitInterval);
                            searchWaitInterval = '';
                            searchTerm = $(item).val();
                            // facilities_dt.search(searchTerm).draw();
                            // console.log({"value": searchTerm});
                            searchWait = 0;
                        }
                        searchWait++;
                    }, 300);
                });

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

        <div class="page-title d-flex justify-content-between mb-3">
            <h4 class="m-0">Roles</h4>
            <a href="{{ route('roles.create') }}" class="btn btn-sm btn-primary m-0">
                <i class="fa fa-plus"></i>&nbsp;Register
            </a>
        </div>

        <div class="row mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Roles
                    </li>
                </ol>
            </nav>
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
                            <table id="roles" class="table table-striped table-hover no-wrap" style="width: 100%;">
                                <caption>List of roles.</caption>
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Deleted At</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
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
