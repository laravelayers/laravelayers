@component("form::layouts.text.group.group", ['element' => $element, 'reverse' => false, 'type' => 'text'])

    @slot('icon')

        @icon('icon-link')

    @endslot

    @slot('attributes')

        data-validator="url"

    @endslot

@endcomponent