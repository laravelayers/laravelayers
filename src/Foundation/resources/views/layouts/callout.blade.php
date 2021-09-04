@if (e($slot))

    <div class="callout {{ $class ?? ''}}" {{ $attributes ?? '' }} {{ !empty($closable) ? 'data-closable' : ''}}>

        @if (!empty($closable))

            <button class="close-button" aria-label="Dismiss alert" type="button" data-close>
                <span aria-hidden="true">&times;</span>
            </button>

        @endif

        {!! $slot !!}

    </div>

@endif
