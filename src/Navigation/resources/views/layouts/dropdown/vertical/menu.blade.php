@component('navigation::layouts.tree', ['tree' => $tree])

    @slot('listClass')

        vertical dropdown

    @endslot

    @slot('listAttributes')

        data-dropdown-menu

    @endslot

    @slot('sublistClass')

        vertical

    @endslot

@endcomponent