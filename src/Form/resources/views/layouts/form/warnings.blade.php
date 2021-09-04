@component('foundation::layouts.callout', ['closable' => !empty($closable) ?: false])

    @slot('class')

        warning {{ $class ?? ''  }}

    @endslot

    @slot('attributes')

        {{ $attributes ?? ''  }}

    @endslot

    @if ($count = count($elements->getWarnings()))

        @if ($count == 1)

            <p>{!! $elements->getWarnings()->first() !!}</p>

        @else

            <ul>

                @foreach($elements->getWarnings()->all() as $warning)

                    <li>{{ $warning }}</li>

                @endforeach

            </ul>

        @endif

    @endif

@endcomponent