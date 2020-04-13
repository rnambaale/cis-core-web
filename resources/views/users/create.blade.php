@extends('layouts.app')

@section('content')
<div class="main-content">
    <div class="container-fluid">

        <div class="page-title">
            <h4>
                <a href="{{ route('users.index') }}">Users</a>
            </h4>
        </div>

        <div class="row">
            <div class="col-md-12">
                @include('flash::message')
            </div>
        </div>

        <form method="POST" action="{{ route('users.store') }}" autocomplete="off">
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
                                <label for="alias">Alias</label>
                                <input type="text" name="alias" id="alias"
                                    class="form-control" value="{{ old('alias') }}">
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email"
                                    class="form-control" value="{{ old('email') }}">
                            </div>

                            <div class="form-group">
                                <label for="role_id">Role</label>
                                <select type="role_id" name="role_id" id="role_id" class="form-control" required>
                                    <option value="">Select</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
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
