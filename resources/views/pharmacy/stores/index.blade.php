@extends('layouts.app')

@push('extra-js')
    <script type="text/javascript">
    </script>
@endpush

@section('content')

<div class="main-content">
    <div class="container-fluid">

        <div class="page-title d-flex justify-content-between mb-3">
            <div class="d-flex flex-nowrap">
                <h4 class="m-0">Stores</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 bg-transparent py-0" style="font-size: 0.95rem;">
                        <li class="breadcrumb-item">
                            <a href="{{ route('home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Stores
                        </li>
                    </ol>
                </nav>
            </div>

            <div class="d-flex flex-nowrap">
                
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
                        <div class="table-overflow">
                            <table id="users" class="table table-hover table-cis">
                                <thead>
                                <tr>
                                    <th>Store Name</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach ($stores as $store)
                                    <tr>
                                        <td><a href="{{ route('pharmacy.inventories.index', $store->id) }}">{{ $store->name }}</a></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
