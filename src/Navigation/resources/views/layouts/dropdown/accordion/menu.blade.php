@component('navigation::layouts.tree', ['tree' => $tree])

    @slot('listClass')

        vertical medium-horizontal

    @endslot

    @slot('listAttributes')

        data-responsive-menu="accordion medium-dropdown"

    @endslot

    @slot('sublistClass')

        vertical

    @endslot

@endcomponent