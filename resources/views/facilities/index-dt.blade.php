@extends('layouts.app')

@push('extra-js')
    {{-- <script src="{{ asset('js/pages/facilities-dt.js') }}"></script> --}}
    <script type="text/javascript">
        var can_update = {{ auth_can('facilities', 'update') ? 1 : 0 }};
        var can_restore = {{ auth_can('facilities', 'restore') ? 1 : 0 }};
        var can_soft_delete = {{ auth_can('facilities', 'soft-delete') ? 1 : 0 }};
        var can_force_delete = {{ auth_can('facilities', 'force-delete') ? 1 : 0 }};
        var can_assign_modules = {{ auth_can('modules', 'assign-modules') ? 1 : 0 }};

        $(document).ready(function () {
            var facilities_dt = $('table[id=facilities]').DataTable({
                pageLength: 10,
                language: {
                    emptyTable: "No facilities available",
                    info: "Showing _START_ to _END_ of _TOTAL_ facilities",
                    infoEmpty: "Showing 0 to 0 of 0 facilities",
                    infoFiltered: "(filtered from _MAX_ total facilities)",
                    lengthMenu: "Show _MENU_ facilities",
                    search: "Search facilities:",
                    zeroRecords: "No facilities match search criteria"
                },
                order: [[1, 'asc']],
                processing: true,
                serverSide: true,
                ajax: {
                    type: 'GET',
                    url: route('facilities.dt'),
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
                        name: 'id',
                        data: 'id',
                        visible: false
                    },
                    {
                        targets: 1,
                        name: 'name',
                        data: 'name',
                        render: function (data, type, row, meta) {
                            return '<a href="'+route('facilities.show', row.id)+'">'+data+'</a>'
                        }
                    },
                    {
                        targets: 2,
                        name: 'email',
                        data: 'email'
                    },
                    {
                        targets: 3,
                        name: 'website',
                        data: 'website'
                    },
                    {
                        targets: 4,
                        name: 'deleted_at',
                        data: 'deleted_at',
                        visible: false
                    },
                    {
                        targets: 5,
                        name: null,
                        data: null,
                        orderable: false,
                        searchable: false,
                        class: 'text-center',
                        render: function (data, type, row, meta) {

                            var edit = '<a href="'+route('facilities.edit', row.id)+'" class="text-info"><i class="fas fa-pencil-alt px-1" title="Edit">E</i></a>';

                            var assignModules = '<a href="'+route('facilities.modules.update', row.id)+'" class="text-success"><i class="fa fa-key" title="Modules"></i></a>';

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

                                if(can_assign_modules) {
                                    action = assignModules;
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

            $("#revoke-facility-modal").on("show.bs.modal", function (event) {
                var relatedTarget = $(event.relatedTarget);

                var id = relatedTarget.data("id");
                var name = relatedTarget.data("name");

                var form = $(this).find("form#revoke_facility");

                form.attr('action', route('facilities.revoke', id));

                form.find('span#name').text(name);
            });

            $("#restore-facility-modal").on("show.bs.modal", function (event) {
                var relatedTarget = $(event.relatedTarget);

                var id = relatedTarget.data("id");
                var name = relatedTarget.data("name");

                var form = $(this).find("form#restore_facility");

                form.attr('action', route('facilities.restore', id));

                form.find('span#name').text(name);
            });

            $("#destroy-facility-modal").on("show.bs.modal", function (event) {
                var relatedTarget = $(event.relatedTarget);

                var id = relatedTarget.data("id");
                var name = relatedTarget.data("name");

                var form = $(this).find("form#destroy_facility");

                form.attr('action', route('facilities.destroy', id));

                form.find('span#name').text(name);
            });
        });
    </script>
@endpush

@section('content')

<div class="main-content">
    <div class="container-fluid">

        <div class="page-title d-flex justify-content-between mb-3">
            <h4 class="m-0">Facilities</h4>
            <a href="{{ route('facilities.create') }}" class="btn btn-sm btn-primary m-0">
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
                            <table id="facilities" class="table table-hover table-cis">
                                <caption>List of facilities.</caption>
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Website</th>
                                    {{-- <th>Created At</th> --}}
                                    {{-- <th>Updated At</th> --}}
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

@include('facilities.modals.revoke')
@include('facilities.modals.restore')
@include('facilities.modals.destroy')

@endsection
