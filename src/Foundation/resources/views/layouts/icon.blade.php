@if (e($class ?? ''))

    @if (strpos($class, '{') !== false || strpos($class, 'icon-') === 0)

        <i class="icon {{ $class }}" {!! $attributes ?? '' !!}></i>

    @else

        {{ $class }}

    @endif

@endif
