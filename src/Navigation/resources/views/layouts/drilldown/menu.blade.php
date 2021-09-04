@component('navigation::layouts.tree', ['tree' => $tree])

    @slot('listClass')

        vertical drilldown

    @endslot

    @slot('listAttributes')

        data-drilldown @include('navigation::layouts.drilldown.dataBackButton')

    @endslot

    @slot('sublistClass')

        vertical

    @endslot

@endcomponent