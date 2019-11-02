@extends('layouts.app')

@section('content')
<div class="main-content">
    <div class="container-fluid">

        <div class="page-title">
            <h4>
                <a href="{{ route('facilities.index') }}">Facilities</a>
            </h4>
        </div>

        <div class="row">
            <div class="col-md-12">
                @include('flash::message')
            </div>
        </div>

        <form method="POST" action="{{ route('facilities.store') }}">
            @csrf

            <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="card">
                        <div class="card-block pb-0">
                            <div class="form-group">
                                <label for="name">Name *</label>
                                <input type="text" name="name" id="name" required
                                    class="form-control" value="{{ old('name') }}">
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <input type="text" name="description" id="description"
                                    class="form-control" value="{{ old('description') }}">
                            </div>
                            <div class="form-group">
                                <label for="address">Address *</label>
                                <input type="text" name="address" id="address" required
                                    class="form-control" value="{{ old('address') }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="card">
                        <div class="card-block pb-0">
                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" name="email" id="email" required
                                    class="form-control" value="{{ old('email') }}">
                            </div>
                            <div class="form-group">
                                <label for="website">Website</label>
                                <input type="url" name="website" id="website"
                                    class="form-control" value="{{ old('website') }}">
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="tel" name="phone" id="phone"
                                    class="form-control" value="{{ old('phone') }}">
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-block pb-0">
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
            </div>
        </form>
    </div>
</div>
@endsection
