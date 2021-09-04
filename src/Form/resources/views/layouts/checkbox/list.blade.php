<div class="{{ $class ?? '' }} {{ $element->class }}"
        {!! $element->helpId ? 'aria-describedby="' . $element->helpId . '"' : '' !!}
        {{ $attributes ?? '' }} {!! $element->attributes !!}>

    @foreach ($element as $key => $checkbox)

        @include($slot)

    @endforeach

</div>