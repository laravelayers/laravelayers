<span>

    @component("form::layouts.textarea.textarea", ['element' => $element])

        @slot('class')

            form-editor

        @endslot

        @slot('attributes')

            data-form-editor

        @endslot

    @endcomponent

</span>

@if ($element->getAttributes('data-editor-gallery'))

    @component("form::layouts.textarea.js.gallery", ['element' => $element])

        @slot('class')

            large

        @endslot

        @slot('attributes')

            data-multiple-opened="true" data-deep-link="true" data-update-history="true"

        @endslot

    @endcomponent

@endif
