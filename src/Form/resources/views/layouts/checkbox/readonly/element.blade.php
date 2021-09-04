<div class="{{ $class ?? '' }} {{ $element->class }}"
        {!! $element->helpId ? 'aria-describedby="' . $element->helpId . '"' : '' !!}
        {{ $attributes ?? '' }} {!! $element->attributes !!}>
    <nobr>
        <input type="{{ $element->type }}"
               {{ $element->getIsSelected($checkbox = $element->value->first()) ? 'checked' : '' }}
               disabled>

        @if ($element->getText($checkbox))

            <label for="{{ $element->getId($checkbox) }}"
                   class="{{ $element->getClass($checkbox) }} ">

                {{ $element->getText($checkbox) }}

            </label>

        @endif

        @component("form::layouts.text.text", ['element' => $checkbox])

            @slot('type')

                hidden

            @endslot

            @slot('name')

                {{ $element->getName($checkbox) }}

            @endslot

            @slot('value')

                {{ $checkbox->value }}

            @endslot

            @slot('id')

                {{ $element->getId($checkbox) }}

            @endslot

            @slot('attributes')

                {{ $checkbox->attributes }}

            @endslot

        @endcomponent

    </nobr>
</div>