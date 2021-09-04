@if ($slot)

    @foreach ((is_array($slot) ? $slot : (array) e($slot, false)) as $key => $value)

        {!! !empty($nowrap) ? "<nobr>" : '' !!}

        <a href="{!! is_array($href) ? $href[$key] : ((array) e($href, false))[$key] !!}"
           class="{{ !empty($class) ? (is_array($class) ? $class : (array) e($class))[$key] : '' }}"
                {!! !empty($external) ? 'target="_blank"' : '' !!}>

            {!! $value !!}</a>{{ (empty($class) && !$loop->last) ? ',' : '' }}

        {!! !empty($nowrap) ? "</nobr>" : '' !!}

    @endforeach

@elseif(e($default ?? ''))

    {!! $default !!}

@endif
