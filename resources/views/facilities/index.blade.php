@extends('layouts.app')

@push('extra-js')
    <script type="text/javascript">
        $(document).ready(function () {
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

        <div class="row mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent py-0" style="font-size: 0.95rem;">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Facilities
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
                            <table id="facilities" class="table table-hover table-cis">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Website</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($facilities as $facility)
                                        <tr class="@if($facility->deleted_at) strike-through @endif">
                                            <td>
                                                <a href="{{ route('facilities.show', $facility->id) }}">
                                                    {{ $facility->name }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="mailto:{{ $facility->email }}">
                                                    {{ $facility->email }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ $facility->website }}" target="_blank">
                                                    {{ $facility->website }}
                                                </a>
                                            </td>
                                            <td class="text-center">
                                                @if($facility->deleted_at)
                                                    @if(auth_can('facilities', 'restore'))
                                                        <a href="" class="text-success" data-toggle="modal"
                                                            data-id="{{ $facility->id }}" data-name="{{ $facility->name }}"
                                                            data-target="#restore-facility-modal">
                                                            <i class="fa fa-refresh px-1" title="Restore"></i>
                                                        </a>
                                                    @endif
                                                    @if(auth_can('facilities', 'force-delete'))
                                                        <a href="#" class="text-danger" data-toggle="modal"
                                                            data-id="{{ $facility->id }}" data-name="{{ $facility->name }}"
                                                            data-target="#destroy-facility-modal">
                                                            <i class="fa fa-trash px-1" title="Delete"></i>
                                                        </a>
                                                    @endif
                                                @else
                                                    @if(auth_can('facilities', 'update'))
                                                        <a href="{{ route('facilities.edit', $facility->id) }}" class="text-info">
                                                            <i class="fas fa-pencil-alt px-1" title="Edit"></i>
                                                        </a>
                                                    @endif
                                                    @if(auth_can('modules', 'assign-modules'))
                                                        <a href="{{ route('facilities.modules.show', $facility->id) }}" class="text-success">
                                                            <i class="fa fa-key" title="Modules"></i>
                                                        </a>
                                                    @endif
                                                    @if(auth_can('facilities', 'soft-delete'))
                                                        <a href="" class="text-warning" data-toggle="modal"
                                                            data-id="{{ $facility->id }}" data-name="{{ $facility->name }}"
                                                             data-target="#revoke-facility-modal">
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
                            {{ $facilities->links() }}
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
