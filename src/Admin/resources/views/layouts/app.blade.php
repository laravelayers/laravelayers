<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Laravel') }}</title>
    <link rel="icon" href="../../favicon.ico">

    {{-- Styles --}}
    <link href="{{ mix('/css/app.admin.css') }}" rel="stylesheet">

    @stack('head')

    {{-- Scripts --}}
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
            'country' => 'RU',
        ]) !!};
    </script>
</head>
<body>
    <div class="admin {{ !empty($class) ? $class : '' }}" id="app">

        @section('adminMenuBar')

            @if (empty($clear) && empty($full))

                @include('admin::layouts.menuBar')

            @endif

        @show

        <div class="off-canvas-content" data-off-canvas-content>

            @section('header')

                @empty($clear)

                    @include('admin::layouts.header')

                @endempty

            @show

            @section('main')

                @component('admin::layouts.main', ['full' => $full ?? ''])

                    @yield('content')

                @endcomponent

            @show

            @section('footer')

                @if (empty($simple) && empty($clear) && empty($full))

                    @include('admin::layouts.footer', ['full' => true])

                @endif

            @show

        </div>
    </div>

    {{-- Scripts --}}
    <script src="{{ mix('/js/app.admin.js') }}"></script>
    <script>
        $(function() {
            $(document).foundation();
        });
    </script>

    @stack('scripts')

</body>
</html>