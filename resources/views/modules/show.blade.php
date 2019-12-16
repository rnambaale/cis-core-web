@extends('layouts.app')

@push('extra-js')
    <script type="text/javascript">
        $(document).ready(function () {
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

        <div class="page-title">
            <h4>
                <a href="{{ route('modules.index') }}">Modules</a>
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
                    <div class="card-block pb-0">
                        <div class="row">
                            <p class="col-12 mb-0 small text-uppercase">
                                Name
                            </p>
                            <p class="col-12 bold">
                                {{ $module->name }}
                            </p>
                        </div>
                        <div class="row">
                            <p class="col-12 mb-0 small text-uppercase">
                                Description
                            </p>
                            <p class="col-12 bold">
                                {{ $module->description }}
                            </p>
                        </div>
                        <div class="row">
                            <p class="col-12 mb-0 small text-uppercase">
                                Category
                            </p>
                            <p class="col-12 bold">
                                {{ $module->category }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="card">
                    <div class="card-block pb-0">
                        <div class="row">
                            <p class="col-12 mb-0 small text-uppercase">
                                Created At
                            </p>
                            <p class="col-12 bold">
                                {{ $module->created_at }}
                            </p>
                        </div>
                        <div class="row">
                            <p class="col-12 mb-0 small text-uppercase">
                                Updated At
                            </p>
                            <p class="col-12 bold">
                                {{ $module->updated_at }}
                            </p>
                        </div>
                        <div class="row">
                            <p class="col-12 mb-0 small text-uppercase">
                                Deleted At
                            </p>
                            <p class="col-12 bold">
                                {{ $module->deleted_at }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-block pb-0">
                        <div class="row">
                            @if($module->deleted_at)
                                <p class="col">
                                    <a href="" data-toggle="modal" data-target="#restore-module-modal"
                                    data-id="{{ $module->name }}" data-name="{{ $module->name }}"
                                        class="btn btn-success btn-sm btn-block">
                                        <i class="fa fa-refresh"></i>&nbsp;Restore
                                    </a>
                                </p>
                                <p class="col">
                                    <a href="" data-toggle="modal" data-target="#destroy-module-modal"
                                    data-id="{{ $module->name }}" data-name="{{ $module->name }}"
                                        class="btn btn-danger btn-sm btn-block">
                                        <i class="fa fa-trash"></i>&nbsp;Delete
                                    </a>
                                </p>
                            @else
                                @if(auth_can('modules', 'update'))
                                <p class="col">
                                    <a href="{{ route('modules.edit', $module->name) }}" class="btn btn-info btn-sm btn-block">
                                        <i class="fa fa-pencil"></i>&nbsp;Edit
                                    </a>
                                </p>
                                @endif

                                <p class="col">
                                    <a href="" data-toggle="modal" data-target="#revoke-module-modal"
                                    data-id="{{ $module->name }}" data-name="{{ $module->name }}"
                                        class="btn btn-warning btn-sm btn-block">
                                        <i class="fa fa-ban"></i>&nbsp;Revoke
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

@include('modules.modals.revoke')
@include('modules.modals.restore')
@include('modules.modals.destroy')

@endsection
