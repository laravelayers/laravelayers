@component('form::layouts.button.view', ['element' => $element, 'button' => $button])

    @slot('class')

        success

    @endslot

    @slot('icon')

        @icon('icon-pencil-alt')

    @endslot

@endcomponent