@component("form::layouts.text.group.group", ['element' => $element, 'reverse' => false])

    @slot('icon')

        @icon('icon-sort')

    @endslot

    @slot('attributes')

        data-validator="number"

    @endslot

@endcomponent