<div class="form-search-wrapper">

    @component("form::layouts.text.group.group", ['element' => $element, 'reverse' => false])

        @slot('class')

            form-search

        @endslot

        @slot('attributes')

            data-form-search data-toggle="search_pane_{{ $element->id }}" autocomplete="off"

        @endslot

        @if ($element->text)

            @component("foundation::layouts.icon")

                @slot("class")

                    {{ $element->text ? : 'icon-search' }}

                @endslot

            @endcomponent

        @endif

    @endcomponent

    <div class="dropdown-pane" id="search_pane_{{ $element->id }}" data-dropdown data-auto-focus="true" data-close-on-click="true"></div>
</div>
