@component('navigation::layouts.breadcrumbs.treeList')

    @if (!empty($back))

        {!! $back !!}

    @endif

    @component('navigation::layouts.breadcrumbs.treeItemCurrent')

        {{ $slot }}

    @endcomponent

@endcomponent