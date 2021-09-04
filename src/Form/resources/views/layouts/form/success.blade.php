@component('foundation::layouts.callout', ['closable' => !empty($closable)])

    @slot('class')

        success

    @endslot

    @slot('attributes')

        id="{{ $id = uniqid() }}"

        @if (!session('success'))

            style="display: none;"

        @endif

    @endslot

    @if (session('success'))

        <p>@icon('icon-thumbs-up icon-fw') {{ session('success') }}</p>

    @else

        @if (isset($slot) && e($slot))

            {!! $slot !!}

        @else

            <p>@icon('icon-thumbs-up icon-fw') @lang('form::form.alerts.success')</p>

        @endif

    @endif

    @if (!empty($closable) && !empty($reloadable))

        @push('scripts')

            <script>

                $('#{{ $id }} .close-button').click(function() {
                    location.reload();
                });

            </script>

        @endpush

    @endif

@endcomponent
