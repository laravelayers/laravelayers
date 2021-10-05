@if ($slot)

    @if (!empty($ol))

        <ol class="{{ $class ?? '' }}">

    @else

        <ul class="{{ $class ?? '' }}">

    @endif

        @foreach (((is_array($slot) ? $slot : [$slot])) as $row)

            <li>

                @if (!empty($nowrap))

                    <nobr>{!! $row !!}</nobr>

                @else

                    {!! $row !!}

                @endif

            </li>

        @endforeach

    @if (!empty($ol))

        </ol>

    @else

        </ul>

    @endif

@elseif(e($default ?? ''))

    {!! $default !!}

@endif
