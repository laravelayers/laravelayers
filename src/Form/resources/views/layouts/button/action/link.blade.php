@component('form::layouts.button.view', ['element' => $element, 'button' => $button])

    @slot('class')

        primary

    @endslot

    @slot('icon')

        @icon('icon-link')

    @endslot

@endcomponent