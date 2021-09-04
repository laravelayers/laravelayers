@component("form::layouts.text.group.group", ['element' => $element])

    @if ($element->text)

        @component("foundation::layouts.icon")

            @slot("class")

                {{ $element->text }}

            @endslot

        @endcomponent

    @endif

@endcomponent
