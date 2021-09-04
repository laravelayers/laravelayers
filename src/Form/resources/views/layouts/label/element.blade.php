<label id="{{ "label_{$element->id}" }}"
        {{ $element->errors ? 'class="is-invalid-label"' : '' }}
        {{ $attributes ?? '' }}
        {!! $element->tooltip !!}>

    @if ($element->label)

        @if (!is_null($element->getAttributes('required')))

            <strong>{{ $element->label }}</strong>

        @else

            {{ $element->label }}

        @endif

    @endif

    {{ $slot }}

    @if ($element->error)

        <span id="{{ $element->id }}_error"
              class="form-error {{ $element->errors ? 'is-visible' : ''}}"
              data-form-error-for="{{ $element->id }}">
            {!! $element->error !!}
        </span>

    @endif

</label>

@if ($element->help)

    <p id="{{ $element->helpId }}" class="help-text">
        {!! $element->help !!}
    </p>

@endif
