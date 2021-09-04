@component("form::layouts.file.file", ['element' => $element])

    @slot('class')

        {{ $element->class ? $element->class : 'hollow expanded' }}

    @endslot

    @slot('icon')

        @if ($element->multiple)

            @icon('icon-file-alt')

        @else

            @icon('icon-file')

        @endif

    @endslot

    @slot('attributes')

        {{ $attributes ?? '' }}

    @endslot

    {{ $slot ?? '' }}

@endcomponent
