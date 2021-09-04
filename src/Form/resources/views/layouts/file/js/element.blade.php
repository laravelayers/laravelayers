<div class="grid-x grid-padding-x grid-padding-y" id="file_wrapper_{{ $element->id }}"
    {!! $element->getAttributes('data-ajax-url') ? "data-form-checkbox data-html-checkbox-all=\"#select_images_{$element->id}\" data-html-checkbox=\"#image_preview_{$element->id} input\"" : '' !!}>

    @if (!$element->multiple && !$element->getAttributes('data-ajax-url'))

        @include("form::layouts.file.js.preview", ['file' => $element->value->first()])

    @endif

    <div class="cell auto" id="file_block_{{ $element->id }}">

        @component("form::layouts.file.group.group", ['element' => $element])

            @slot('attributes')

                data-form-file

            @endslot

            @if (!$element->getAttributes('data-ajax-url'))

                @include("form::layouts.file.listing")

            @endif

        @endcomponent

    </div>

    @if ($element->getAttributes('data-ajax-url'))

        @include("form::layouts.file.js.previewMultiple")

    @endif

</div>
