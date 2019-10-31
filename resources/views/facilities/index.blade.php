@extends('layouts.app')

@section('content')
<div class="main-content">
    <div class="container-fluid">

        <div class="page-title">
            <h4>Facilities</h4>
        </div>

        <div class="row">
            <div class="col-md-12">
                @include('flash::message')
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-block">
                        <div class="table-overflow">
                            <table id="facilities" class="table table-striped table-hover no-wrap">
                                <caption>List of facilities.</caption>
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Website</th>
                                    <th class="center">Actions</th>
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
                                            <td class="center"></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
