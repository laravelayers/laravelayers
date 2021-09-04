@component('navigation::layouts.tree', ['tree' => $element])

    @slot('list')

        form::layouts.checkbox.tree.list

        @slot('listClass')

            {{ $element->class }}

        @endslot

        @slot('listAttributes')

            {!! $element->attributes !!}

        @endslot

    @endslot

    @slot('sublist')

        @slot('sublistClass')

            vertical

        @endslot

        form::layouts.checkbox.tree.sublist

    @endslot

    @slot('item')

        form::layouts.checkbox.tree.item

    @endslot

@endcomponent