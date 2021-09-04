@extends('foundation::layouts.app', ['simple' => true, 'title' => __('Verify Your Email Address')])

@section('content')

    @component('foundation::layouts.content')

        <div class="grid-x align-center">
            <div class="large-5 medium-6 cell">
                <div class="card">
                    <div class="card-divider">
                        {{ __('Verify Your Email Address') }}
                    </div>
                    <div class="card-section">

                        @if (session('resent'))

                            @component('foundation::layouts.callout', ['class' => 'success', 'closable' => true])

                                <p>{{ __('A fresh verification link has been sent to your email address.') }}</p>

                            @endcomponent

                        @endif

                        <p>{{ __('Before proceeding, please check your email for a verification link.') }}<p>
                        <p>
                            {{ __('If you did not receive the email') }},
                            <a href="{{ route('verification.resend') }}">{{ __('click here to request another') }}</a>.
                        </p>
                    </div>
                </div>
            </div>
        </div>

    @endcomponent

@endsection
