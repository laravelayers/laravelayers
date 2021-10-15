@if ($element->value->isNotEmpty())

    <div id="file_link_{{ $element->id }}">

        @if (!$element->multiple)

            <nobr>
                <input type="checkbox" checked name="{{ $element->getName() }}"
                       value="{{ $element->value->first()->value }}"
                        {{ !is_null($element->getAttributes('disabled')) ? 'disabled' : '' }}>
                <label>

                    @foreach($element->value as $value)

                        <a class="{!! $loop->count > 1 ? 'button hollow small' : '' !!}"
                           href="{{ $value->value }}" target="_blank">
                            {{ basename($value->value) }}
                        </a>

                    @endforeach

                </label>
            </nobr>

        @else

            @foreach($element->value as $value)

                <nobr>
                    <input type="checkbox" checked name="{{ $element->getName() }}"
                           value="{{ basename($value->value) }}"
                            {{ !is_null($element->getAttributes('disabled')) ? 'disabled' : '' }}>
                    <label><a href="{{ $value->value }}" target="_blank">{{ basename($value->value) }}</a></label>
                </nobr>

            @endforeach

        @endif

    </div>

@endif
