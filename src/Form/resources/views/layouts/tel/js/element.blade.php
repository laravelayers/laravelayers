@component("form::layouts.text.group.group", ['element' => $element, 'reverse' => false])

    @slot('icon')

        @icon('icon-phone')

    @endslot

    @slot('attributes')

        data-validator="phone"

    @endslot

@endcomponent