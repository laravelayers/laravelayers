<li class="{{ $itemClass ?? '' }} {{ !$node->menuUrl ? 'disabled' : '' }}"
        {{ $itemAttributes ?? '' }}>

    <a {!! ($node->menuUrl && !$node->isNodeSelected) ? 'href="' . $node->menuUrl . '"' : '' !!}
       class="{{ $node->menuClass }}">

        @if ($node->menuIcon)

            @icon($node->menuIcon)

        @endif

        <span>{!! $node->menuName !!}</span>

        @if ($node->menuLabel)

            @component('foundation::layouts.span')

                @slot('class')

                    label {{ $node->menuLabel['class']  }}

                @endslot

                {{ $node->menuLabel['label'] }}

            @endcomponent

        @endif

    </a>

    {{ $slot }}

</li>