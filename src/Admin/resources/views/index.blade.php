@extends('admin::layouts.app')

@section('breadcrumbs')

    @component('navigation::layouts.breadcrumbs.heading')

        @lang('admin::admin.menu.name')

    @endcomponent

@endsection

@section('content')

    @component('foundation::layouts.content')

        {!! $content !!}

    @endcomponent

@endsection