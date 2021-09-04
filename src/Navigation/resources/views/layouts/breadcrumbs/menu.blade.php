@component('navigation::layouts.tree', ['tree' => $tree])

    @slot('list')

        navigation::layouts.breadcrumbs.treelist

    @endslot

    @slot('sublist')

        navigation::layouts.breadcrumbs.treeSublist

    @endslot

    @slot('item')

        navigation::layouts.breadcrumbs.treeItem

    @endslot

@endcomponent