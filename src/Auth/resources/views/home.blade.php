@extends('foundation::layouts.app', ['simple' => true, 'title' => Lang::get('auth::auth.profile_heading')])

@section('content')

    @component('foundation::layouts.content')

        <div class="grid-x align-center">
            <div class="large-5 medium-6 cell">
                <div class="card">
                    <div class="card-divider">
                        {{ Lang::get('auth::auth.profile_heading') }}
                    </div>
                    <div class="card-section">

                        @if (session('verified'))

                            @component('foundation::layouts.callout', ['class' => 'success', 'closable' => true])

                                <p>{{ Lang::get('auth::auth.email_verified') }}</p>

                            @endcomponent

                        @endif

                        {{ $elements->render() }}

                    </div>
                </div>
            </div>
        </div>

    @endcomponent

@endsection

@push('scripts')

    <script>

        $('#{{ $elements->password->id }}').on('input', function() {
            let $confirmation = $('#{{ $elements->password_confirmation->id }}');

            if ($(this).val()) {
                $confirmation.attr('required', '');
            } else {
                $confirmation.removeAttr('required', '');
            }
        });

        $('#{{ $elements->password_confirmation->id }}').on('input', function() {
            let $password = $('#{{ $elements->password->id }}');

            if ($(this).val()) {
                $password.attr('required', '');
            } else {
                $password.removeAttr('required');
            }
        });

    </script>

@endpush
