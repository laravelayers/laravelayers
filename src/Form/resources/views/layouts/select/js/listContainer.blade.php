<div class="reveal {!! $listClass ?? '' !!}"
     id="container_{{ $tree->id }}"
     data-reveal {!! $listAttributes ?? '' !!} {!! $tree->getAttributes('reveal') !!}
     aria-labelledby="header_container_{{ $tree->id }}">

    <p id="header_container_{{ $tree->id }}">

        @if ($tree->multiple)

            <input type="checkbox" id="checkbox_{{ $tree->id }}" data-abide-ignore>

        @endif

        <label {!! $tree->multiple ? 'for="checkbox_' . $tree->id . '"' : '' !!}>{!! $tree->label ?: '&nbsp;' !!}</label>
    </p>

    @include('form::layouts.select.js.list')

    <button class="close-button" data-close aria-label="Close modal" type="button">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

@component("form::layouts.text.group.group", ['element' => $tree])

    @slot('class')

        form-select {{ $class ?? '' }}

    @endslot

    @slot('attributes')

        data-form-select data-open="container_{{ $tree->id }}" {!! $tree->multiple ? 'data-multiple="true"' : '' !!}

    @endslot

    @slot('icon')

        @icon("icon-tasks ")

    @endslot

    @slot('type')

        text

    @endslot

    @slot('value')

        @if ($tree->value->first() && $tree->value->first()->has('value') && $tree->getAttributes('data-ajax-url'))

            {{ $tree->value->implode('value', ', ') }}

        @else

            {{ $tree->value->getSelectedItems('formElementText', ',') }}

        @endif

    @endslot

@endcomponent