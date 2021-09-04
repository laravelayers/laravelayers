@component('form::layouts.button.dropdown.actions', ['element' => $element])

    @slot('dropdownAttributes')

        data-hover="{{ !is_null($element->getAttributes('data-hover')) ? $element->getAttributes('data-hover') : true }}"
        data-hover-pane="{{ !is_null($element->getAttributes('data-hover')) ? $element->getAttributes('data-hover') : true }}"

    @endslot

@endcomponent