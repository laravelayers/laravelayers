@component('form::layouts.button.view', ['element' => $element, 'button' => $button])

    @slot('class')

        alert

    @endslot

    @slot('icon')

        @icon('icon-trash-alt')

    @endslot

@endcomponent