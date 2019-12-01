@extends('layouts.app')

@push('extra-js')
    {{-- <script src="{{ asset('js/pages/users-dt.js') }}"></script> --}}
    <script type="text/javascript">
        var can_update = {{ auth_can('users', 'update') ? 1 : 0 }};
        var can_restore = {{ auth_can('users', 'restore') ? 1 : 0 }};
        var can_soft_delete = {{ auth_can('users', 'soft-delete') ? 1 : 0 }};
        var can_force_delete = {{ auth_can('users', 'force-delete') ? 1 : 0 }};

        $(document).ready(function () {
            var users_dt = $('table[id=users]').DataTable({
                pageLength: 10,
                language: {
                    emptyTable: "No users available",
                    info: "Showing _START_ to _END_ of _TOTAL_ users",
                    infoEmpty: "Showing 0 to 0 of 0 users",
                    infoFiltered: "(filtered from _MAX_ total users)",
                    lengthMenu: "Show _MENU_ users",
                    search: "Search users:",
                    zeroRecords: "No users match search criteria"
                },
                order: [[1, 'asc']],
                processing: true,
                serverSide: true,
                ajax: {
                    type: 'GET',
                    url: route('users.dt'),
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
                        name: 'users.id',
                        data: 'id',
                        visible: false
                    },
                    {
                        targets: 1,
                        name: 'users.alias',
                        data: 'alias',
                        render: function (data, type, row, meta) {
                            return '<a href="'+route('users.show', row.id)+'">'+data+'</a>'
                        }
                    },
                    {
                        targets: 2,
                        name: 'users.name',
                        data: 'name'
                    },
                    {
                        targets: 3,
                        name: 'users.email',
                        data: 'email'
                    },
                    {
                        targets: 4,
                        name: 'roles.name',
                        data: 'role.name'
                    },
                    {
                        targets: 5,
                        name: 'users.deleted_at',
                        data: 'deleted_at',
                        visible: false
                    },
                    {
                        targets: 6,
                        name: null,
                        data: null,
                        orderable: false,
                        searchable: false,
                        class: 'text-center',
                        render: function (data, type, row, meta) {

                            var edit = '<a href="'+route('users.edit', row.id)+'" class="text-info"><i class="fa fa-pencil px-1" title="Edit"></i></a>';

                            var softDelete = '<a href="" class="text-warning" data-toggle="modal"data-id="'+row.id+'" data-name="'+row.name+'"data-target="#revoke-user-modal"><i class="fa fa-ban px-1" title="Revoke"></i></a>';

                            var restore = '<a href="" class="text-success" data-toggle="modal"data-id="'+row.id+'" data-name="'+row.name+'"data-target="#restore-user-modal"><i class="fa fa-refresh px-1" title="Restore"></i></a>';

                            var forceDelete = '<a href="#" class="text-danger" data-toggle="modal"data-id="'+row.id+'" data-name="'+row.name+'"data-target="#destroy-user-modal"><i class="fa fa-trash px-1" title="Delete"></i></a>';

                            // ...

                            var action = '';

                            if(can_update) {
                                action = edit;
                            }

                            if(row.deleted_at) {
                                if(can_restore) {
                                    action += restore;
                                }

                                if(can_force_delete) {
                                    action += forceDelete;
                                }
                            } else {
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
                            // users_dt.search(searchTerm).draw();
                            // console.log({"value": searchTerm});
                            searchWait = 0;
                        }
                        searchWait++;
                    }, 300);
                });

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
            <h4>Users</h4>
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
                            <table id="users" class="table table-striped table-hover no-wrap" style="width: 100%;">
                                <caption>List of users.</caption>
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Alias</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
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

{{-- @include('users.modals.revoke') --}}
{{-- @include('users.modals.restore') --}}
{{-- @include('users.modals.destroy') --}}

@endsection
