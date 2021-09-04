@component('form::layouts.button.view', ['element' => $element, 'button' => $button, 'reverse' => true])

    @slot('class')

        primary

    @endslot

    @slot('icon')

        @icon('icon-chevron-right')

    @endslot

@endcomponent