@extends('admin::layouts.app')

@section('breadcrumbs')

    @parent

@endsection

@section('content')

    @component('admin::layouts.content')

        @if ($item->getElements()->isNotEmpty())

            {{ $item->getElements()->render() }}

        @else

            @component('foundation::layouts.callout', ['class' => 'warning'])

                @lang('admin::admin.alerts.not_found')

            @endcomponent

        @endif

    @endcomponent

@endsection