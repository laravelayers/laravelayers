@component('form::layouts.button.actions', ['element' => $element])

    @slot('class')

        button-group {{ !$element->class ? 'expanded-for-medium stacked-for-small' : '' }}

    @endslot

@endcomponent