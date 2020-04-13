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

        <div class="page-title d-flex justify-content-between mb-3">
            <h4 class="m-0">Modules</h4>
            <a href="{{ route('modules.create') }}" class="btn btn-sm btn-primary m-0">
                <i class="fa fa-plus"></i>&nbsp;Create
            </a>
        </div>

        <div class="row mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent py-0" style="font-size: 0.95rem;">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Modules
                    </li>
                </ol>
            </nav>
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
                                    @foreach($modules as $module)
                                        <tr class="@if($module->deleted_at) strike-through @endif">
                                            <td>
                                                <a href="{{ route('modules.show', $module->name) }}">
                                                    {{ $module->name }}
                                                </a>
                                            </td>
                                            <td>{{ $module->category }}</td>
                                            <td>{{ $module->description }}</td>
                                            <td class="text-center">
                                                @if(auth_can('modules', 'update'))
                                                    <a href="{{ route('modules.edit', $module->name) }}" class="text-info">
                                                        <i class="fa fa-pencil-alt px-1" title="Edit"></i>
                                                    </a>
                                                @endif
                                                @if($module->deleted_at)
                                                    @if(auth_can('modules', 'restore'))
                                                        <a href="" class="text-success" data-toggle="modal"
                                                            data-id="{{ $module->name }}" data-name="{{ $module->name }}"
                                                            data-target="#restore-module-modal">
                                                            <i class="fa fa-refresh px-1" title="Restore"></i>
                                                        </a>
                                                    @endif
                                                    @if(auth_can('modules', 'force-delete'))
                                                        <a href="#" class="text-danger" data-toggle="modal"
                                                            data-id="{{ $module->name }}" data-name="{{ $module->name }}"
                                                            data-target="#destroy-module-modal">
                                                            <i class="fa fa-trash px-1" title="Delete"></i>
                                                        </a>
                                                    @endif
                                                @else
                                                    @if(auth_can('modules', 'soft-delete'))
                                                        <a href="" class="text-warning" data-toggle="modal"
                                                            data-id="{{ $module->name }}" data-name="{{ $module->name }}"
                                                             data-target="#revoke-module-modal">
                                                            <i class="fa fa-ban px-1" title="Revoke"></i>
                                                        </a>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end">
                            {{ $modules->links() }}
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
