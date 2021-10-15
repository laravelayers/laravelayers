<fieldset class="fieldset">
    <legend>

        @if (!is_null($element->getAttributes('required')))

            <strong>{{ $element->group }}</strong>

        @else

            {{ $element->group }}

        @endif

    </legend>

    {{ $slot }}

</fieldset>