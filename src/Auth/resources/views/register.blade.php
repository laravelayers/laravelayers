@extends('foundation::layouts.app', ['simple' => true, 'title' => Lang::get('auth::auth.register_heading')])

@section('content')

    @component('foundation::layouts.content')

        <div class="grid-x align-center">
            <div class="large-5 medium-6 cell">
                <div class="card">
                    <div class="card-divider">
                        {{ Lang::get('auth::auth.register_heading') }}
                    </div>
                    <div class="card-section">

                        {{ $elements->render() }}

                    </div>
                </div>
            </div>
        </div>

    @endcomponent

@endsection
