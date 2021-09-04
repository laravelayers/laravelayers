<div class="cell hide" id="delete_images_{{ $element->id }}">
    <div class="grid-x grid-padding-x grid-padding-y align-center">
        <div class="cell shrink"><input type="checkbox" id="select_images_{{ $element->id }}"></div>
        <div class="cell auto"><button class="button hollow expanded alert" title="@lang('admin::admin.actions.delete_multiple')?">@lang('admin::admin.actions.delete_multiple')</button></div>
    </div>
</div>
<div class="cell" id="images_preview_{{ $element->id }}">

    @component('form::layouts.form.success', ['closable' => true])

        <p>@icon('icon-thumbs-up icon-fw') @lang('form::form.alerts.success_upload')</p>

    @endcomponent

    @component('form::layouts.form.errors')

        <p>
            <span>@icon('icon-exclamation-triangle') @lang('form::form.alerts.errors_upload')</span>
            <span class="hide">@icon('icon-exclamation-triangle') @lang('form::form.alerts.errors_delete')</span>
        </p>

    @endcomponent

    @component('form::layouts.form.progress')

    @endcomponent

    @include('foundation::layouts.preloader')

    <div class="grid-x grid-padding-x grid-padding-y align-center" id="image_preview_{{ $element->id }}">

        @foreach($element->value as $key => $image)

            @component("form::layouts.file.js.image", ['element' => $element, 'file' => $image])

                <div>
                    <input type="checkbox"
                           name="{{ $element->getName($image) }}"
                           id="{{ $element->getId($image) }}"
                           value="{{ basename($element->getValue($image)) }}">
                    <label for="{{ $element->getId($image) }}"></label>
                    <a>@icon('icon-eye')</a>
                </div>

            @endcomponent

        @endforeach

    </div>
</div>
