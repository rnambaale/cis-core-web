@extends('layouts.app')

@push('extra-js')
    {{-- <script src="{{ asset('js/pages/modules-dt.js') }}"></script> --}}
    <script type="text/javascript">
        var can_update = {{ auth_can('modules', 'update') ? 1 : 0 }};
        var can_restore = {{ auth_can('modules', 'restore') ? 1 : 0 }};
        var can_soft_delete = {{ auth_can('modules', 'soft-delete') ? 1 : 0 }};
        var can_force_delete = {{ auth_can('modules', 'force-delete') ? 1 : 0 }};

        $(document).ready(function () {
            var modules_dt = $('table[id=modules]').DataTable({
                pageLength: 10,
                language: {
                    emptyTable: "No modules available",
                    info: "Showing _START_ to _END_ of _TOTAL_ modules",
                    infoEmpty: "Showing 0 to 0 of 0 modules",
                    infoFiltered: "(filtered from _MAX_ total modules)",
                    lengthMenu: "Show _MENU_ modules",
                    search: "Search modules:",
                    zeroRecords: "No modules match search criteria"
                },
                order: [[1, 'asc']],
                processing: true,
                serverSide: true,
                ajax: {
                    type: 'GET',
                    url: route('modules.dt'),
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log(response);
                    }
                },
                columnDefs: [
                    {
                        targets: 0,
                        name: 'name',
                        data: 'name',
                        render: function (data, type, row, meta) {
                            return '<a href="'+route('modules.show', row.name)+'">'+data+'</a>'
                        }
                    },
                    {
                        targets: 1,
                        name: 'category',
                        data: 'category'
                    },
                    {
                        targets: 2,
                        name: 'description',
                        data: 'description'
                    },
                    {
                        targets: 3,
                        name: null,
                        data: null,
                        orderable: false,
                        searchable: false,
                        class: 'text-center',
                        render: function (data, type, row, meta) {

                            var edit = '<a href="'+route('modules.edit', row.id)+'" class="text-info"><i class="fa fa-pencil px-1" title="Edit"></i></a>';

                            var softDelete = '<a href="" class="text-warning" data-toggle="modal"data-id="'+row.name+'" data-name="'+row.name+'"data-target="#revoke-module-modal"><i class="fa fa-ban px-1" title="Revoke"></i></a>';

                            var restore = '<a href="" class="text-success" data-toggle="modal"data-id="'+row.name+'" data-name="'+row.name+'"data-target="#restore-module-modal"><i class="fa fa-refresh px-1" title="Restore"></i></a>';

                            var forceDelete = '<a href="#" class="text-danger" data-toggle="modal"data-id="'+row.name+'" data-name="'+row.name+'"data-target="#destroy-module-modal"><i class="fa fa-trash px-1" title="Delete"></i></a>';

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
                            // modules_dt.search(searchTerm).draw();
                            // console.log({"value": searchTerm});
                            searchWait = 0;
                        }
                        searchWait++;
                    }, 300);
                });

            $("#revoke-module-modal").on("show.bs.modal", function (event) {
                var relatedTarget = $(event.relatedTarget);

                var id = relatedTarget.data("id");
                var name = relatedTarget.data("name");

                var form = $(this).find("form#revoke_module");

                form.attr('action', route('modules.revoke', id));

                form.find('span#name').text(name);
            });

            $("#restore-module-modal").on("show.bs.modal", function (event) {
                var relatedTarget = $(event.relatedTarget);

                var id = relatedTarget.data("id");
                var name = relatedTarget.data("name");

                var form = $(this).find("form#restore_module");

                form.attr('action', route('modules.restore', id));

                form.find('span#name').text(name);
            });

            $("#destroy-module-modal").on("show.bs.modal", function (event) {
                var relatedTarget = $(event.relatedTarget);

                var id = relatedTarget.data("id");
                var name = relatedTarget.data("name");

                var form = $(this).find("form#destroy_module");

                form.attr('action', route('modules.destroy', id));

                form.find('span#name').text(name);
            });
        });
    </script>
@endpush

@section('content')

<div class="main-content">
    <div class="container-fluid">

        <div class="page-title d-flex justify-content-between mb-3">
            <h4 class="m-0">Modules</h4>
            <a href="{{ route('modules.create') }}" class="btn btn-sm btn-primary m-0">
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
                    <div class="card-body">
                        <div class="table-overflow">
                            <table id="modules" class="table table-hover table-cis">
                                <caption>List of modules.</caption>
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Category</th>
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

@include('modules.modals.revoke')
@include('modules.modals.restore')
@include('modules.modals.destroy')

@endsection
