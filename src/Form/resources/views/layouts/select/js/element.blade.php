@component('navigation::layouts.tree', ['tree' => $element])

    @slot('list')

        @slot('listClass')

            large

        @endslot

        @slot('listAttributes')

            data-multiple-opened="true" data-deep-link="true" data-update-history="true"

        @endslot

        form::layouts.select.js.listContainer

    @endslot

    @slot('sublist')

        @slot('sublistClass')

            vertical

        @endslot

        form::layouts.checkbox.tree.sublist

    @endslot

    @slot('item')

        form::layouts.select.js.item

    @endslot

@endcomponent