<div class="input-group {{ $element->getClass('hide') }}">

    @if ($icon = (!is_null($element->icon) ? $element->icon : ($icon ?? '')))

        @push(${'stack_label_' . $element->id} = uniqid())

            <span class="input-group-label">{!! $icon !!}</span>

        @endpush

    @endif

    @if (e($slot))

        @push(${'stack_button_' . $element->id} = uniqid())

            <div class="input-group-button">
                <button type="{{ $buttonType ?? ($element->name ? 'submit' : 'button') }}"
                        class="button {{ $buttonClass ?? '' }}">

                    {{ $slot }}

                </button>
            </div>

        @endpush

    @endif

    @if (!($reverse = $reverse ?? $element->reverse))

        @stack(${'stack_label_' . $element->id} ?? null)

    @else

        @stack(${'stack_button_' . $element->id} ?? null)

    @endif

    @component("form::layouts.text.text", ['element' => $element])

        @if (isset($type))

            @slot('type')

                {{ $type ?? '' }}

            @endslot

        @endif

        @if (isset($value))

            @slot('value')

                {{ $value }}

            @endslot

        @endif

        @slot('class')

            input-group-field {{ $class ?? '' }}

        @endslot

        @slot('attributes')

            {{ $attributes ?? '' }}

        @endslot

    @endcomponent

    @if (!$reverse)

        @stack(${'stack_button_' . $element->id} ?? null)

    @else

        @stack(${'stack_label_' . $element->id} ?? null)

    @endif

</div>