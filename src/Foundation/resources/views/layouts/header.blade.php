<div class="header">

    @include('admin::layouts.topBar')

    @if (empty($clear) && empty($full))
        @empty($simple)

            @includeFirst(['layouts.topBar', 'foundation::layouts.topBar'])

            @component('foundation::layouts.breadcrumbs')

                @slot('right')

                    @yield('breadcrumbsRight')

                @endslot

                @yield('breadcrumbs')

            @endcomponent

            @component('foundation::layouts.headerBar', ['full' => $full ?? false])

                @yield('headerBar')

            @endcomponent
        @else

            @includeFirst(['layouts.simpleTopBar', 'foundation::layouts.simpleTopBar'])

        @endempty
    @endif

</div>
