@component("form::layouts.text.group.group", ['element' => $element, 'reverse' => false])

    @slot('type')

        text

    @endslot

    @slot('class')

        form-datetime

    @endslot

    @slot('icon')

        @icon('icon-calendar-week')

    @endslot

    @slot('attributes')

        data-form-datetime
        data-date-format="{{ $element->getAttributes('data-date-format') ?? config('date.datetime.format') }}"
        data-alt-format="{{ $element->getAttributes('data-alt-format') ?? 'W' }}"
        data-enable-time="false"
        data-week-numbers="true"
        {!! $element->multiple ? 'data-multiple="multiple"' : '' !!}

    @endslot

@endcomponent


