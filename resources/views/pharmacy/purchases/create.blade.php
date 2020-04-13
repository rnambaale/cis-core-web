@extends('layouts.app')

@push('extra-js')
    <script type="text/javascript">
        var storeId = "{{ $storeId }}";

        $('select[id=item]').select2({
            width: '100%',
            allowClear: true,
            minimumInputLength: 3,
            placeholder: "Choose item...",
            ajax: {
                type: 'GET',
                url: route('pharmacy.inventories.index_suggest', storeId),
                dataType: 'json',
                delay: 250,
                cache: true,
                data: function (params) {
                    return {
                        // store_id: "17136ea562b",
                        filters: {
                            select: [
                                // "id",
                                // "quantity",
                                // "unit_price",
                                // "deleted_at",
                                "product.id",
                                "product.name"
                            ],
                            where: [
                                {
                                    column: "product.name",
                                    value: "%"+params.term+"%",// <--
                                    operator: "ilike",
                                    boolean: "and"
                                }
                            ],
                            offset: 0,
                            limit: 5
                        }
                    };
                },
                processResults: function (data) {
                    // console.log(data);
                    return {
                        results: data.data.map(function(item) {
                            return {
                                id: item.product.id,
                                text: item.product.name
                            };
                        })
                    };
                },
                error: function (xhr) {
                    console.error(xhr);
                }
            }
        });

        $('select[id=item]').on('select2:select', function (event) {
            var data = event.params.data;

            $("#add_item_form").submit();

            console.log({selected: data});
        });
    </script>
    
@endpush

@section('extra-css')
    @parent
    <style>
        #sales-table td{
            background-color: #EEE;
            padding: 3px;
            text-align: center;
        }

        #sales-table th {
            background-color: #999;
            padding: 5px;
            text-align: center;
            color: #FFF;
        }

        .payment-totals{
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            width: 100%;
        }
    </style>
@endsection

@section('content')

<div class="main-content">
    <div class="container-fluid">

        <div class="page-title d-flex justify-content-between mb-0">

            <div class="mb-4 d-flex flex-nowrap">
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

            <div class="col-md-8">
                <div class="card text-white bg-secondary">
                    <div class="card-body form-group mb-0">
                        <form method="POST" action="{{ route('pharmacy.purchasesCart.store', $storeId) }}" id="add_item_form">
                            @csrf
                            <select name="product_id" id="item" class="form-control" autocomplete="off"></select>
                        </form>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-overflow">
                            <table id="sales-table" class="table table-hover table-cis">
                                <thead>
                                <tr>
                                    <th width="5%">Delete</th>
                                    <th width="30%">Name</th>
                                    <th width="17%">Cost</th>
                                    <th width="13%">Quantity</th>
                                    <th width="17%">Unit Retail Price</th>
                                    <th width="20%">Sub Total</th>
                                    <th width="5%" class="text-center">Update</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @forelse ($cartItems as $key => $item)

                                        <form 
                                            action="{{ route('pharmacy.purchasesCart.destroy', [$storeId, $item->id]) }}"
                                            id="cart_delete_form_{{ $item->id }}"
                                            method="POST"
                                            style="display: none;">

                                            @method('DELETE')
                                            @csrf
                                        </form>
                                        
                                        <form
                                            method="POST"
                                            action="{{ route('pharmacy.purchasesCart.update', [$storeId, $item->id]) }}"
                                            id="cart_update_form_{{ $item->id }}"
                                            >
                                            @csrf
                                            @method('PUT')

                                            <tr>
                                                <td>
                                                    <a
                                                        href="{{ route('pharmacy.purchasesCart.destroy', [$storeId, $item->id]) }}"
                                                        onclick="event.preventDefault(); document.getElementById('cart_delete_form_{{ $item->id }}').submit();">
                                                            <span class="text-danger"><i class="fas fa-trash-alt"></i></span>
                                                    </a>
                                                </td>
                                                <td>{{ $item->name }}</td>
                                                <td>
                                                    <div class="form-group">
                                                        <input
                                                            type="text"
                                                            name="price"
                                                            class="form-control input-sm @error('products.'.$key.'.cost_price') is-invalid @enderror"
                                                            value="{{ $item->price }}">
    
                                                            @error('products.'.$key.'.cost_price')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                    <input
                                                        type="text"
                                                        name="quantity"
                                                        class="form-control input-sm @error('products.'.$key.'.quantity') is-invalid @enderror"
                                                        value="{{ $item->quantity }}">

                                                        @error('products.'.$key.'.quantity')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <input
                                                            type="text"
                                                            name="unit_retail_price"
                                                            class="form-control input-sm @error('products.'.$key.'.unit_retail_price') is-invalid @enderror"
                                                            value="{{ $item->attributes->unit_retail_price }}">
    
                                                            @error('products.'.$key.'.unit_retail_price')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                    </div>
                                                </td>
                                                <td>{{ number_format($item->price * $item->quantity) }}</td>
                                                <td class="text-center">
                                                    <a href="#"
                                                    onclick="event.preventDefault(); document.getElementById('cart_update_form_{{ $item->id }}').submit();"
                                                    class="" title="Update"><i class="fas fa-sync-alt"></i></a>
                                                </td>
                                            </tr>
                                        </form>
                                    @empty
                                    <tr>
                                        <td colspan="7">
                                            <div class="alert alert-info" role="alert">There are no items in the cart</div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-secondary">
                    <div class="card-header">Purchase Cart</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('pharmacy.purchases.store', $storeId) }}">
                            <table class="payment-totals mb-4">
                                <tbody>
                                    <tr>
                                        <td style="width: 55%;">Amount Paid</td>
                                        <td style="width: 45%; text-align: right;">{{ number_format($cartTotal) }}</td>
                                    </tr>
                                </tbody>
                            </table>

                            @csrf

                            @foreach ($cartItems as $key => $item)
                                <input type="hidden" name="products[{{ $key }}][id]" value="{{ $item->id }}"/>
                                <input type="hidden" name="products[{{ $key }}][quantity]" value="{{ $item->quantity }}"/>
                                <input type="hidden" name="products[{{ $key }}][cost_price]" value="{{ $item->price }}"/>
                                <input type="hidden" name="products[{{ $key }}][unit_retail_price]" value="{{ $item->attributes->unit_retail_price }}"/>
                            @endforeach

                            <button type="submit" class="btn btn-success">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
