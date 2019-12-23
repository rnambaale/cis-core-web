@extends('layouts.app')

@push('extra-js')
    <script type="text/javascript">
        $(document).ready(function () {
            $("#revoke-role-modal").on("show.bs.modal", function (event) {
                var relatedTarget = $(event.relatedTarget);

                var id = relatedTarget.data("id");
                var name = relatedTarget.data("name");

                var form = $(this).find("form#revoke_role");

                form.attr('action', route('roles.revoke', id));

                form.find('span#name').text(name);
            });

            $("#restore-role-modal").on("show.bs.modal", function (event) {
                var relatedTarget = $(event.relatedTarget);

                var id = relatedTarget.data("id");
                var name = relatedTarget.data("name");

                var form = $(this).find("form#restore_role");

                form.attr('action', route('roles.restore', id));

                form.find('span#name').text(name);
            });

            $("#destroy-role-modal").on("show.bs.modal", function (event) {
                var relatedTarget = $(event.relatedTarget);

                var id = relatedTarget.data("id");
                var name = relatedTarget.data("name");

                var form = $(this).find("form#destroy_role");

                form.attr('action', route('roles.destroy', id));

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
                <a href="{{ route('roles.index') }}">Roles</a>
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
                                {{ $role->name }}
                            </p>
                        </div>
                        <div class="row">
                            <p class="col-12 mb-0 small text-uppercase">
                                Description
                            </p>
                            <p class="col-12 bold">
                                {{ $role->description }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                @if(auth_can('permissions', 'assign-permissions'))
                    <form role="form" method="POST" action="{{ route('roles.permissions.update', $role->id) }}">
                        @csrf
                        @method('PUT')
                        <div id="accordion-1" class="accordion panel-group" role="tablist" aria-multiselectable="true">
                            @foreach ($permissions as $key => $module_permissions)                       
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading" role="tab" id="headingOne">
                                        <h4 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#accordion-1" href="#collapse-{{ $key }}">
                                                <span>{{ $key }}</span>
                                                <i class="icon ti-arrow-circle-down"></i> 
                                            </a>
                                        </h4>
                                    </div>

                                    <div id="collapse-{{ $key }}" class="collapse panel-collapse">
                                        <div class="panel-body">
                                            @foreach ($module_permissions as $permission)
                                            <div class="">
                                                <div class="checkbox">
                                                    <input
                                                        id="{{ $permission->id }}"
                                                        name="permissions[]" type="checkbox" value="{{ $permission->id }}"
                                                        {{ ($permission->granted === true) ? 'checked' : '' }}
                                                        >
                                                    <label class="" for="{{ $permission->id }}">
                                                        {{ $permission->name }}
                                                    </label>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="col-md-12">
                            <button
                                type="submit"
                                class="btn btn-success btn-sm">
                                <i class="fa fa-save"></i>&nbsp;Save Permissions
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>        
    </div>
</div>

@endsection
