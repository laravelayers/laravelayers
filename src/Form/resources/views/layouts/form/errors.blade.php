@component('foundation::layouts.callout', ['closable' => !empty($closable) ?: false])

    @slot('class')

        alert {{ $class ?? ''  }}

    @endslot

    @slot('attributes')

        {{ $attributes ?? ''  }}

        @if (!count($errors))

            style="display: none;"

        @endif

    @endslot


    @if (count($errors))

        <h5>@icon('icon-exclamation-triangle') @lang('form::form.alerts.errors')</h5>

    @else

        @if (isset($slot) && e($slot))

            {!! $slot !!}

        @else

            <p>@icon('icon-exclamation-triangle') @lang('form::form.alerts.errors')</p>

        @endif

    @endif

    @if (count($errors))

        <ul>

            @foreach($errors->all() as $error)

                <li>{{$error}}</li>

            @endforeach

        </ul>

    @endif

@endcomponent