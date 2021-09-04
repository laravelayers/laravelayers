<button type="{{ in_array($button->type, ['button', 'reset']) ? $button->type : 'submit' }}"
        name="{{ $element->getName($button) }}"
        value="{{ $element->getValue($button) }}"
        id="{{ $element->getId($button) }}"
        class="button {{ $class ?? '' }} {{ $element->getClass($button) }}"
        {!! isset($external) && e($external) ? 'formtarget="_blank"' : '' !!}
        {!! $button->attributes !!}>

    {{ $slot }}

</button>