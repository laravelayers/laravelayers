@extends('admin::layouts.app')

@section('breadcrumbs')

    @parent

@endsection

@section('breadcrumbs-right')

    @parent

@endsection

@section('header-bar')

    @parent

@endsection

@section('content')

    @component('admin::layouts.content')

        @include('admin::layouts.table.filter')

        {{ $items->render() }}

    @endcomponent

@endsection