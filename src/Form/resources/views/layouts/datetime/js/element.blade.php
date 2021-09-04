@component("form::layouts.text.group.group", ['element' => $element, 'reverse' => false])

    @slot('type')

        text

    @endslot

    @slot('class')

        form-datetime

    @endslot

    @slot('icon')

        @icon('icon-calendar-alt')

    @endslot

    @slot('attributes')

        data-form-datetime
        data-date-format="{{ $element->getAttributes('data-date-format') ?? config('date.datetime.format') }}"
        data-alt-format="{{ $element->getAttributes('data-alt-format') ?? str_ireplace('s', 'S', config('date.datetime.format')) }}"
        {!! $element->multiple ? 'data-multiple="multiple"' : '' !!}

    @endslot

@endcomponent


