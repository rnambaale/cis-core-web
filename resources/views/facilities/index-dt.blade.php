@extends('layouts.app')

@push('extra-js')
    {{-- Excel; html5 + jszip --}}
    <script src="{{ asset('vendor/dataTables.net-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('vendor/jszip/dist/jszip.min.js') }}"></script>
    {{-- <script src="{{ asset('js/pages/facilities.js') }}"></script> --}}
    <script type="text/javascript">
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
                buttons: [{
                    extend: 'excel',
                    className: 'btn btn-sm',
                    text: '<i class="fa fa-file-excel-o"></i> Excel',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    }
                }],
                processing: true,
                serverSide: true,
                ajax: {
                    type: 'GET',
                    url: route('facilities.dt')
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
                        data: 'name'
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
                        name: null,
                        data: null,
                        orderable: false,
                        searchable: false,
                        class: 'text-center',
                        render: function (data, type, row, meta) {

                            // if($facility->deleted_at) {}
                            // if(can(restore))

                            // var actions = '';

                            // if (can_edit) {
                            //     var url = app + '/admin/account/user/' + row.c_user_rid + '/edit';
                            //     actions += '<a href="' + url + '"><i class="fa fa-1x fa-pencil text-info" title="Edit"></i></a>';
                            // }

                            // return actions;

                            return '...';
                        }
                    }
                ]
            });

            facilities_dt.buttons().container().appendTo('.export-btns');

            // https://stackoverflow.com/a/10318763/2732184

            var searchWait = 0;
            var searchWaitInterval;

            $('div.dataTables_filter input')
                .unbind('keypress keyup');
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

        <div class="page-title">
            <h4>facilities</h4>
        </div>

        <div class="row">
            <div class="col-md-12">
                @include('flash::message')
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    {{--
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <div class="">
                                facilities
                            </div>
                            <div class="export-btns">
                            </div>
                        </div>
                    </div>
                    --}}
                    <div class="card-block">
                        <div class="table-overflow">
                            <table id="facilities" class="table table-striped table-hover no-wrap">
                                <caption>List of facilities.</caption>
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Website</th>
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

{{--
@include('facilities.modals.revoke')
@include('facilities.modals.restore')
@include('facilities.modals.destroy')
--}}

@endsection
