<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no">

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    {{-- Favicon --}}
    {{-- <link rel="shortcut icon" href="{{ asset('espire/images/logo/favicon.png') }}"> --}}

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    @yield('extra-css')
</head>

<body>
    
    <div class="app">
        @include('includes.navigation')

        @include('includes.sidebar')
        
        <div class="layout">

            {{-- Page Container START --}}
            <div class="page-container">

                {{-- Content Wrapper START --}}
                @yield('content')
                {{-- Content Wrapper END --}}

            </div>
            {{-- Page Container END --}}

        </div>

        {{-- Footer START --}}
            @include('includes.footer')
        {{-- Footer END --}}
    </div>

    <script> window.appUrl = '{!! config('app.url') !!}'; </script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script type="text/javascript">
        var app = '{{ config('app.url') }}';

        window.$.ajaxSetup({
            headers: {
                'Accept': 'application/json',
                // 'Authorization': 'Bearer {{ session('api_token') }}',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // $('div.alert').not('.alert-danger, .alert-important').delay(5000).fadeOut(500);

        var modal = $('.modal');

        modal.on('shown.bs.modal', function () {
            $(this).find('[autofocus]').focus();
        });

        modal.on('hidden.bs.modal', function () {
            // Source: https://stackoverflow.com/a/35079811
            // $(this).find('form').trigger('reset');
            $(this).find('form')[0].reset();
        });
    </script>

    @stack('extra-js')

</body>

</html>
