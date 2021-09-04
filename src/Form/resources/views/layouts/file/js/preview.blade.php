@if ($element->getAttributes('data-image-mode'))

    @include("form::layouts.file.js.image", ['id' => $element->id])

@endif

<div class="cell shrink {{ (!$file || $element->getAttributes('data-image-mode')) ? 'hide' : '' }}"
     id="file_preview_{{ $element->id }}">

    @if (!$file)

        @icon('icon-file icon-3x secondary')

    @else

        @icon('icon-file icon-5x secondary')

    @endif

</div>
