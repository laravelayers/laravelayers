@component('form::layouts.button.view', ['element' => $element, 'button' => $button])

    @slot('name')

        {{ (isset($name) && e($name)) ? $name : '' }}

    @endslot

    @slot('value')

        {{ (isset($value) && e($value)) ? $value : '' }}

    @endslot

    @slot('class')

        primary

    @endslot

    @slot('icon')

        @icon('icon-check')

    @endslot

@endcomponent