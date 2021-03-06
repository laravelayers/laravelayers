@extends('foundation::layouts.app', ['simple' => true, 'title' => Lang::get('Login')])

@section('content')

    @component('foundation::layouts.content')

        <div class="grid-x align-center">
            <div class="large-5 medium-6 cell">
                <div class="card">
                    <div class="card-divider">
                        {{ Lang::get('Login') }}
                    </div>
                    <div class="card-section">

                        {{ $elements->render() }}

                        @if (Route::has('password.request'))

                            <div>
                                <a href="{{ route('password.request') }}">
                                    {{ Lang::get('Forgot Your Password?') }}
                                </a>
                            </div>

                        @endif

                    </div>
                </div>
            </div>
        </div>

    @endcomponent

@endsection
