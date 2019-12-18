@extends('layouts.app')

@push('extra-js')
    {{-- <script src="{{ asset('js/pages/facilities-dt.js') }}"></script> --}}
    <script type="text/javascript">
        var can_update = {{ auth_can('permissions', 'update') ? 1 : 0 }};
        var can_delete = {{ auth_can('permissions', 'delete') ? 1 : 0 }};

        $(document).ready(function () {
            var permissions_dt = $('table[id=permissions]').DataTable({
                pageLength: 10,
                language: {
                    emptyTable: "No permissions available",
                    info: "Showing _START_ to _END_ of _TOTAL_ permissions",
                    infoEmpty: "Showing 0 to 0 of 0 permissions",
                    infoFiltered: "(filtered from _MAX_ total permissions)",
                    lengthMenu: "Show _MENU_ permissions",
                    search: "Search permissions:",
                    zeroRecords: "No permissions match search criteria"
                },
                order: [[1, 'asc']],
                processing: true,
                serverSide: true,
                ajax: {
                    type: 'GET',
                    url: route('permissions.dt'),
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
                        name: 'permissions.id',
                        data: 'id',
                        visible: false
                    },
                    {
                        targets: 1,
                        name: 'permissions.name',
                        data: 'name',
                        render: function (data, type, row, meta) {
                            return '<a href="'+route('permissions.show', row.id)+'">'+data+'</a>'
                        }
                    },
                    {
                        targets: 2,
                        name: 'permissions.module_name',
                        data: 'module_name'
                    },
                    {
                        targets: 3,
                        name: 'permissions.description',
                        data: 'description'
                    },
                    {
                        targets: 4,
                        name: null,
                        data: null,
                        orderable: false,
                        searchable: false,
                        class: 'text-center',
                        render: function (data, type, row, meta) {

                            var edit = '<a href="'+route('permissions.edit', row.id)+'" class="text-info"><i class="fa fa-pencil px-1" title="Edit"></i></a>';

                            var softDelete = '<a href="" class="text-warning" data-toggle="modal"data-id="'+row.id+'" data-name="'+row.name+'"data-target="#revoke-permission-modal"><i class="fa fa-ban px-1" title="Revoke"></i></a>';

                            var restore = '<a href="" class="text-success" data-toggle="modal"data-id="'+row.id+'" data-name="'+row.name+'"data-target="#restore-permission-modal"><i class="fa fa-refresh px-1" title="Restore"></i></a>';

                            var Delete = '<a href="#" class="text-danger" data-toggle="modal"data-id="'+row.id+'" data-name="'+row.name+'"data-target="#destroy-permission-modal"><i class="fa fa-trash px-1" title="Delete"></i></a>';

                            // ...

                            var action = '';

                            if(can_update) {
                                action = edit;
                            }

                            if(can_delete) {
                                action += Delete;
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
                            // permissions_dt.search(searchTerm).draw();
                            // console.log({"value": searchTerm});
                            searchWait = 0;
                        }
                        searchWait++;
                    }, 300);
                });

            $("#revoke-permission-modal").on("show.bs.modal", function (event) {
                var relatedTarget = $(event.relatedTarget);

                var id = relatedTarget.data("id");
                var name = relatedTarget.data("name");

                var form = $(this).find("form#revoke_permission");

                form.attr('action', route('permissions.revoke', id));

                form.find('span#name').text(name);
            });

            $("#restore-permission-modal").on("show.bs.modal", function (event) {
                var relatedTarget = $(event.relatedTarget);

                var id = relatedTarget.data("id");
                var name = relatedTarget.data("name");

                var form = $(this).find("form#restore_permission");

                form.attr('action', route('permissions.restore', id));

                form.find('span#name').text(name);
            });

            $("#destroy-permission-modal").on("show.bs.modal", function (event) {
                var relatedTarget = $(event.relatedTarget);

                var id = relatedTarget.data("id");
                var name = relatedTarget.data("name");

                var form = $(this).find("form#destroy_permission");

                form.attr('action', route('permissions.destroy', id));

                form.find('span#name').text(name);
            });
        });
    </script>
@endpush

@section('content')

<div class="main-content">
    <div class="container-fluid">

        <div class="page-title d-flex justify-content-between mb-3">
            <h4 class="m-0">Permissions</h4>
            <a href="{{ route('permissions.create') }}" class="btn btn-sm btn-primary m-0">
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
                            <table id="permissions" class="table table-striped table-hover no-wrap" style="width: 100%;">
                                <caption>List of permissions.</caption>
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Module Name</th>
                                    <th>Description</th>
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

@include('permissions.modals.destroy')

@endsection
