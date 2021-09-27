@extends('foundation::layouts.app', ['simple' => true, 'title' => __('Confirm Password')])

@section('content')

    @component('foundation::layouts.content')

        <div class="grid-x align-center">
            <div class="large-5 medium-6 cell">
                <div class="card">
                    <div class="card-divider">
                        {{ __('Confirm Password') }}
                    </div>
                    <div class="card-section">
                        <p>{{ __('Please confirm your password before continuing.') }}</p>

                        {{ $elements->getElements()->render() }}
                    </div>
                </div>
            </div>
        </div>

    @endcomponent

@endsection
