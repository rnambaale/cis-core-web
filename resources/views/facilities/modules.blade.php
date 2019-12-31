@extends('layouts.app')

@section('content')
<div class="main-content">
    <div class="container-fluid">

        <div class="page-title">
            <h4>Facility Modules</h4>
        </div>

        <div class="row mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('facilities.index') }}">Facilities</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('facilities.show', $facility->id) }}">{{ $facility->name }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Modules
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
                <form role="form" method="POST" action="{{ route('facilities.modules.update', $facility->id) }}">
                    @csrf
                    @method('PUT')
                    <div id="accordion-1" class="accordion panel-group" role="tablist" aria-multiselectable="true">
                        @foreach ($facility->modules as $category => $modules)
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="headingOne">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion-1" href="#collapse-{{ $category }}">
                                            <span>{{ $category }}</span>
                                            <i class="icon ti-arrow-circle-down"></i>
                                        </a>
                                    </h4>
                                </div>

                                <div id="collapse-{{ $category }}" class="collapse panel-collapse">
                                    <div class="panel-body">
                                        <div class="row">
                                            @foreach ($modules as $module)
                                                <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                                                    <div class="checkbox">
                                                        <input
                                                            id="{{ $module->name }}" name="modules[]"
                                                            type="checkbox" value="{{ $module->name }}"
                                                            {{ ($module->granted === true) ? 'checked' : '' }}>
                                                        <label class="" for="{{ $module->name }}">
                                                            {{ $module->name }}
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
                        <i class="fa fa-save"></i>&nbsp;Reassign Modules
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
