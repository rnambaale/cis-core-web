@extends('layouts.app')

@push('extra-js')
    <script type="text/javascript">

        var storeId = "{{ $storeId }}";

        $(document).ready(function () {
            $('table[id=inventories]').DataTable({
                pageLength: 10,
                language: {
                    emptyTable: "No items available",
                    info: "Showing _START_ to _END_ of _TOTAL_ items",
                    infoEmpty: "Showing 0 to 0 of 0 items",
                    infoFiltered: "(filtered from _MAX_ total items)",
                    lengthMenu: "Show _MENU_ items",
                    search: "Search items:",
                    zeroRecords: "No items match search criteria"
                },
                order: [[1, 'asc']],
                processing: true,
                serverSide: true,
                ajax: {
                    type: 'GET',
                    url: route('pharmacy.inventories.index.dt', storeId),
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    dataType: 'json',
                    // success: function(response) {
                    //     console.log(response);
                    // }
                },
                columnDefs: [
                    {
                        targets: 0,
                        name: 'product.name',
                        data: 'product.name',
                        render: function (data, type, row, meta) {
                            return '<a href="'+route('users.show', row.id)+'">'+data+'</a>'
                        }
                    },
                    {
                        targets: 1,
                        name: 'product.concentration',
                        data: 'product.concentration'
                    },
                    {
                        targets: 2,
                        name: 'product.package',
                        data: 'product.package'
                    },
                    {
                        targets: 3,
                        name: 'unit_price',
                        data: 'unit_price'
                    },
                    {
                        targets: 4,
                        name: 'quantity',
                        data: 'quantity'
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
                        if (searchWait >= 3) {
                            clearInterval(searchWaitInterval);
                            searchWaitInterval = '';
                            searchTerm = $(item).val();
                            searchWait = 0;
                        }
                        searchWait++;
                    }, 300);
                });
        });
    </script>
@endpush

@section('extra-css')
<style>

</style>
@endsection

@section('content')

<div class="main-content">
    <div class="container-fluid">

        <div class="page-title d-flex justify-content-between mb-3">
            <div class="d-flex flex-nowrap">
                <h4 class="m-0">Inventory</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 bg-transparent py-0" style="font-size: 0.95rem;">
                        <li class="breadcrumb-item">
                            <a href="{{ route('home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('pharmacy.stores.index') }}">Stores</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            {{ $storeName }}
                        </li>
                    </ol>
                </nav>
            </div>

            <div class="d-flex flex-nowrap">
                {{-- <a href="#" class="btn btn-sm btn-primary m-0">
                    <i class="fa fa-plus"></i>&nbsp;Add Item
                </a> --}}
            </div>
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

                        @include('pharmacy.nav')

                        <div class="table-overflow">
                            <table id="inventories" class="table table-hover table-cis">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Concn</th>
                                        <th>Packaging</th>
                                        <th>Selling Price</th>
                                        <th>Quantity</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>

                            {{-- <table id="users" class="table table-hover table-cis">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Concn</th>
                                    <th>Packaging</th>
                                    <th>Selling Price</th>
                                    <th>Quantity</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach ($inventories as $inventory)
                                        <tr>
                                            <td><a href="#">Product Name</a></td>
                                            <td>100mg</td>
                                            <td>Tablets</td>
                                            <td>{{ $inventory->unit_price }}</td>
                                            <td>{{ $inventory->quantity }}</td>
                                            <td class="text-center">
                                                <a href="#" class="btn btn-primary btn-sm"><i class="far fa-edit"></i></a>
                                                <a href="#" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
