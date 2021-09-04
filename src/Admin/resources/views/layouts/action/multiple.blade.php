@extends('admin::layouts.app')

@section('breadcrumbs')

    @parent

@endsection

@section('content')

    @component('admin::layouts.content')

        {{ $items->render() }}

    @endcomponent

@endsection