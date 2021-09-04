@component('form::layouts.button.view', ['element' => $element, 'button' => $button])

    @slot('class')

        secondary

    @endslot

    @slot('icon')

        @icon('icon-times')

    @endslot

@endcomponent