@extends('layouts.app')

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

        <form method="POST" action="{{ route('permissions.store') }}" autocomplete="off">
            @csrf

            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="card">
                        <div class="card-body pb-0">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" name="name" id="name" required
                                    class="form-control" value="{{ old('name') }}">
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <input type="text" name="description" id="description"
                                    class="form-control" value="{{ old('description') }}">
                            </div>

                            <div class="form-group">
                                <label for="module_name">Module</label>
                                <select type="module_name" name="module_name" id="module_name" class="form-control" required>
                                    <option value="">Select</option>
                                    @foreach ($modules as $module)
                                        <option value="{{ $module->name }}">{{ $module->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row">
                                <p class="col">
                                    <button type="submit" class="btn btn-info btn-sm btn-block">
                                        <i class="fa fa-plus"></i>&nbsp;Register
                                    </button>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
