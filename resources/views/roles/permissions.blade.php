@extends('layouts.app')

@section('content')
<div class="main-content">
    <div class="container-fluid">

        <div class="page-title">
            <h4>Role Permissions</h4>
        </div>

        <div class="row mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('roles.index') }}">Roles</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('roles.show', $role->id) }}">{{ $role->name }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Permissions
                    </li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col">
                @include('flash::message')
            </div>
        </div>

        <div class="row">
            <div class="col">
                <form role="form" method="POST" action="{{ route('roles.permissions.update', $role->id) }}">
                    @csrf
                    @method('PUT')
                    <div id="accordion-1" class="accordion panel-group" role="tablist" aria-multiselectable="true">
                        @foreach ($role->permissions as $module => $permissions)
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="headingOne">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion-1" href="#collapse-{{ $module }}">
                                            <span>{{ $module }}</span>
                                            <i class="icon ti-arrow-circle-down"></i>
                                        </a>
                                    </h4>
                                </div>

                                <div id="collapse-{{ $module }}" class="collapse panel-collapse">
                                    <div class="panel-body">
                                        <div class="row">
                                            @foreach ($permissions as $permission)
                                                <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                                                    <div class="checkbox">
                                                        <input
                                                            id="{{ $permission->id }}" name="permissions[]"
                                                            type="checkbox" value="{{ $permission->id }}"
                                                            {{ ($permission->granted === true) ? 'checked' : '' }}>
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
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fa fa-save"></i>&nbsp;Reassign Permissions
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
