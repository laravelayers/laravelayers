@component('navigation::layouts.tree', ['tree' => $tree])

    @slot('listClass')

        dropdown

    @endslot

    @slot('listAttributes')

        data-dropdown-menu

    @endslot

@endcomponent