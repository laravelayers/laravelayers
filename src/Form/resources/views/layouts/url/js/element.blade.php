@component("form::layouts.text.group.group", ['element' => $element, 'reverse' => false])

    @slot('icon')

        @icon('icon-link')

    @endslot

    @slot('attributes')

        data-validator="url"

    @endslot

@endcomponent