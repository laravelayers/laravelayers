@if ($slot)

    @foreach ((is_array($slot) ? $slot : (array) e($slot, false)) as $key => $value)
        <span class="{{ !empty($class) ? (is_array($class) ? $class : (array) e($class))[$key] : '' }}">

            {!! !empty($small) ? '<small>' : '' !!}{!! !empty($nowrap) ? '<nobr>' : '' !!}
            {!! $value !!}{{ (empty($class) && !$loop->last)  ? ',' : '' }}
            {!! !empty($nowrap) ? '</nobr>' : '' !!}{!! !empty($small) ? '</small>' : '' !!}

        </span>

    @endforeach

@endif
