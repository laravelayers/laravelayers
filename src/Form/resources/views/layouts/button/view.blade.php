@if (!$button->hidden)

    @component("form::layouts.button." . ($button->link ? 'link' : 'button'), ['element' => $element, 'button' => $button])

        @slot('class')

            {{ (isset($class) && e($class)) ? $class : '' }}

        @endslot

        @slot('external')

            {{ (isset($external) && e($external)) ? true : false }}

        @endslot

        <nobr>

            @if ($icon = (!is_null($button->icon) ? $button->icon : ($icon ?? '')))

                @push(${'stack_button_' . $element->getId($button)} = uniqid() )

                    {!! $icon !!}

                @endpush

            @endif

            @if (!($reverse = $reverse ?? $button->reverse))

                @stack(${'stack_button_' . $element->getId($button)} ?? null)

            @endif

            {{ $button->text }}

            @if ($reverse)

                @stack(${'stack_button_' . $element->getId($button)} ?? null)

            @endif

        </nobr>

    @endcomponent

@endif