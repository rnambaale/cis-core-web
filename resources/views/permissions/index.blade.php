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

        <div class="page-title d-flex justify-content-between mb-3">
            <h4 class="m-0">Permissions</h4>
            <a href="{{ route('permissions.create') }}" class="btn btn-sm btn-primary m-0">
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
                        Permissions
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
                            <table id="permissions" class="table table-hover table-cis">
                                <caption>List of permissions.</caption>
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Module Name</th>
                                    <th>Description</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($permissions as $permission)
                                        <tr class="">
                                            <td>
                                                <a href="{{ route('permissions.show', $permission->id) }}">
                                                    {{ $permission->name }}
                                                </a>
                                            </td>
                                            <td>{{ $permission->module_name }}</td>
                                            <td>{{ $permission->description }}</td>
                                            <td class="text-center">
                                                @if(auth_can('permissions', 'update'))
                                                    <a href="{{ route('permissions.edit', $permission->id) }}" class="text-info">
                                                        <i class="fa fa-pencil-alt px-1" title="Edit"></i>
                                                    </a>
                                                @endif
                                                @if(auth_can('permissions', 'delete'))
                                                    <a href="#" class="text-danger" data-toggle="modal"
                                                        data-id="{{ $permission->id }}" data-name="{{ $permission->name }}"
                                                        data-target="#destroy-permission-modal">
                                                        <i class="fa fa-trash px-1" title="Delete"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end">
                            {{ $permissions->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('permissions.modals.destroy')

@endsection
