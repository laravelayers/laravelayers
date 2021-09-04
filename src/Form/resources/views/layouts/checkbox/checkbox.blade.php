<nobr>
    <input type="{{ ($element->multiple || $element->type == 'checkbox') ? 'checkbox' : 'radio' }}"
           name="{{ $element->getName($checkbox) }}"
           value="{{ $element->getValue($checkbox) }}"
           id="{{ $element->getId($checkbox) }}"
            {{ $element->getIsSelected($checkbox) ? 'checked' : '' }}
            {!! $attributes ?? '' !!} {!! $checkbox->attributes !!}>

    @if ($element->getText($checkbox))

        <label for="{{ $element->getId($checkbox) }}"
               class="{{ $element->getClass($checkbox) }}">

            {{ $element->getText($checkbox) }}

        </label>

    @endif

</nobr>