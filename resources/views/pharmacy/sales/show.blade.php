@extends('layouts.app')

@push('extra-js')
    <script type="text/javascript">
        
        // var storeId = "{{ $storeId }}";

    </script>
@endpush

@section('extra-css')
<style>
    .receipt-header h3 {
        font-size: 1.6rem;
    }

    .receipt-header div {
        font-size: 0.8rem;
    }
    
</style>
@endsection

@section('content')

<div class="main-content">
    <div class="container-fluid">

        <div class="page-title d-flex justify-content-between mb-3">
            <div class="d-flex flex-nowrap">
                <h4 class="m-0">Sales</h4>
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
                <a href="{{ route('pharmacy.sales.index', $storeId) }}" class="btn btn-sm btn-primary m-0">
                    <i class="fa fa-eye"></i>&nbsp;View Sales
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                @include('flash::message')
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        @include('pharmacy.nav')
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">

                        <div class="receipt-header text-center">
                            <h3>Mulago Hospital</h3>
                            <div>Kampala, Uganda</div>
                            <div>03214827618</div>

                            <div>Sales Receipt</div>
                            <div>{{ $sale['created_at'] }}</div>
                        </div>

                        <div class="general-info">
                            <div>Employee: {{ $cashier->name }}</div>
                        </div>
                    
                        <table class="table table-hover table-cis">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th></th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $product)
                                    <tr>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->brand }}</td>
                                        <td>{{ number_format($product->pivot->quantity) }}</td>
                                        <td>{{ number_format($product->pivot->price * $product->pivot->quantity) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2"></th>
                                    <th>Total</th>
                                    <th>{{ number_format($sale['total']) }}</th>
                                </tr>
                            <tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
            </div>
        </div>
    </div>
</div>

@endsection
