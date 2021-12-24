<a {!! !$button->getAttributes('disabled') ? 'href="' . $button->link . '"' : '' !!}
   id="{{ $element->getId($button) }}"
   class="button {{ $class ?? '' }} {{ $button->class ?: 'hollow' }}"
        {!! isset($external) && e($external) ? 'target="_blank"' : '' !!}
        {!! $button->attributes !!}
        {!! in_array($button->type, ['reset']) ? "data-reset" : '' !!}>

    {{ $slot }}

</a>