<div class="dropdown-button">
    <div class="button-group {{ $class ?? '' }} {{ $element->class ?: 'expanded'}}"
         {!! $element->helpId ? 'aria-describedby="' . $element->helpId . '"' : '' !!}
         data-toggle="{{ $element->id }}"
            {!! $attributes ?? '' !!} {!! $element->getAttributesExcept('dropdown-pane') !!}>
        <a class="button">{{ $element->text ?: trans('form::form.button.actions') }}</a>
        <a class="dropdown button arrow-only">
            <span class="show-for-sr">Show menu</span>
        </a>
    </div>

    <div id="{{ $element->id }}"
         class="dropdown-pane {{ $dropdownClass ?? '' }}"
         data-dropdown {!! $dropdownAttributes ?? '' !!} {!! $element->getAttributesOnly('dropdown-pane') !!}>

        @foreach ($element as $group)

            <div class="menu vertical">

                @foreach ($group as $button)

                    <li>

                        @include($button->view)

                    </li>

                @endforeach

            </div>

        @endforeach

    </div>
</div>