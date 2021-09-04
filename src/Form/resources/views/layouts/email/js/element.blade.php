@component("form::layouts.text.group.group", ['element' => $element, 'reverse' => false])

    @slot('icon')

        @icon('icon-at')

    @endslot

    @slot('attributes')

        data-validator="validator" data-validator-name="email"

    @endslot

@endcomponent