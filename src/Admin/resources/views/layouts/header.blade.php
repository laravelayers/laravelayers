<div class="header">

    @include('admin::layouts.topBar', ['full' => true])

    @component('admin::layouts.breadcrumbs', ['full' => true])

        @slot('right')

            @section('breadcrumbsRight')

                @if ($path->isNotEmpty() && $path->last()->tree->isNotEmpty())

                    <div data-responsive-toggle="header-bar" data-hide-for="medium">
                        <a data-toggle="header-bar">@icon('icon-bars')</a>
                    </div>

                @endif

            @show

        @endslot

        @section('breadcrumbs')

            @if ($path->isNotEmpty())

                {{ $path->render('breadcrumbs.menu') }}

            @endif

        @show

    @endcomponent

    @section('headerBar')

        @component('admin::layouts.headerBar', ['full' => true])

            @if ($path->isNotEmpty())

                {{ $path->last()->getTree()->render('menu') }}

            @endif

        @endcomponent

    @show

</div>
