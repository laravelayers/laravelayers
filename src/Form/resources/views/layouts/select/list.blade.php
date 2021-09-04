<select name="{{ $tree->name }}"
        id="{{ $tree->id }}"
        class="{{ $class ?? '' }} {{ $tree->class }} {{ $tree->errors ? 'is-invalid-input' : '' }}"
        {!! $tree->helpId ? 'aria-describedby="' . $tree->helpId . '"' : '' !!}
        {!! $attributes ?? '' !!} {!! $tree->getAttributesExcept('data-placeholder') !!}
        {{ $tree->multiple }}>

        @if (!$tree->getAttributes('required') && $tree->getAttributes('required') !== '')

                <option>{!! $tree->getAttributes('data-placeholder') !!}</option>

        @endif

    {{ $slot }}

</select>
