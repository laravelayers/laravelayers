{!! !empty($isWrapper) ? '<span>' : '' !!}

@component('navigation::layouts.tree', ['tree' => $element])

    @slot('list')

        form::layouts.select.list

    @endslot

    @slot('sublist')

        @slot('sublistClass')

            vertical

        @endslot

        form::layouts.select.sublist

    @endslot

    @slot('item')

        form::layouts.select.item

    @endslot

@endcomponent

{!! !empty($isWrapper) ? '</span>' : '' !!}
