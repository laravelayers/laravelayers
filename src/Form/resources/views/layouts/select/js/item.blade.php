@component('form::layouts.checkbox.tree.item', ['tree' => $tree, 'node' => $node])

    @slot('itemClass')

        {{ $itemClass ?? '' }}

    @endslot

    @slot('itemAttributes')

        {{ $itemAttributes ?? '' }}

    @endslot

    @slot('attributes')

        data-abide-ignore

    @endslot

    {{ $slot }}

@endcomponent