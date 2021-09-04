<li class="{{ $itemClass ?? '' }}" {{ $itemAttributes ?? '' }}>

    {{ $itemAttributes }}

    <a>
        @include('form::layouts.checkbox.checkbox', ['element' => $tree, 'checkbox' => $node])
    </a>

    {{ $slot }}

</li>