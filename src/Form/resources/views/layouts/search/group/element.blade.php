@component("form::layouts.text.group.group", ['element' => $element])

    @component("foundation::layouts.icon")

        @slot("class")

            {{ $element->text ? : 'icon-search' }}

        @endslot

    @endcomponent

@endcomponent