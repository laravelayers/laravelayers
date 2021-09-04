@component('navigation::layouts.tree', ['tree' => $tree])

    @slot('listClass')

        vertical

    @endslot

    @slot('listAttributes')

        data-responsive-menu="drilldown medium-accordion" @include('navigation::layouts.drilldown.dataBackButton')

    @endslot

    @slot('sublistClass')

        vertical

    @endslot

@endcomponent