<div id="wrapper_{{ $tree->id }}"
     class="form-select-wrapper {{ $tree->class }}"
        {!! $tree->multiple ? 'data-multiple="true"' : '' !!}
        {!! $tree->getAttributesExcept('reveal') !!}>

    <input type="search" placeholder="@lang('form::form.elements.search_placeholder')" data-abide-ignore>

    @component('foundation::layouts.callout', ['class' => 'alert is-hidden'])

        @lang('form::form.alerts.not_found') <a class="hide" target="_blank"><i class="icon icon-plus icon-fw"></i></a>

    @endcomponent

    @include('form::layouts.checkbox.tree.list')

    @include('foundation::layouts.preloader')

</div>
