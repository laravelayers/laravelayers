@component('navigation::layouts.tree', ['tree' => $tree])

    @slot('listClass')

        vertical accordion-menu

    @endslot

    @slot('listAttributes')

        data-accordion-menu

    @endslot

    @slot('sublistClass')

        vertical

    @endslot

@endcomponent