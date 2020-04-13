@extends('layouts.app')

@push('extra-js')
    <script type="text/javascript">
        $(document).ready(function () {
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

        <div class="page-title">
            <h4>
                <a href="{{ route('permissions.index') }}">Permissions</a>
            </h4>
        </div>

        <div class="row">
            <div class="col-md-12">
                @include('flash::message')
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="card">
                    <div class="card-body pb-0">
                        <div class="row">
                            <p class="col-12 mb-0 small text-uppercase">
                                Name
                            </p>
                            <p class="col-12 bold">
                                {{ $permission->name }}
                            </p>
                        </div>
                        <div class="row">
                            <p class="col-12 mb-0 small text-uppercase">
                                Module
                            </p>
                            <p class="col-12 bold">
                                {{ $permission->module_name }}
                            </p>
                        </div>
                        <div class="row">
                            <p class="col-12 mb-0 small text-uppercase">
                                Description
                            </p>
                            <p class="col-12 bold">
                                {{ $permission->description }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="card">
                    <div class="card-body pb-0">
                        <div class="row">
                            @if(auth_can('permissions', 'update'))
                                <p class="col">
                                    <a href="{{ route('permissions.edit', $permission->id) }}" class="btn btn-info btn-sm btn-block">
                                        <i class="fa fa-pencil-alt"></i>&nbsp;Edit
                                    </a>
                                </p>
                            @endif

                            @if(auth_can('permissions', 'delete'))
                                <p class="col">
                                    <a href="" data-toggle="modal" data-target="#destroy-permission-modal"
                                    data-id="{{ $permission->id }}" data-name="{{ $permission->name }}"
                                        class="btn btn-danger btn-sm btn-block">
                                        <i class="fa fa-trash"></i>&nbsp;Delete
                                    </a>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- @include('permissions.modals.revoke')
@include('permissions.modals.restore') --}}
@include('permissions.modals.destroy')

@endsection
