@component('form::layouts.button.view', ['element' => $element, 'button' => $button, 'external' => true])

    @slot('class')

        warning

    @endslot

    @slot('icon')

        @icon('icon-external-link-alt')

    @endslot

@endcomponent