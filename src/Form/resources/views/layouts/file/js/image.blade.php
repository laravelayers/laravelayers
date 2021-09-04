<div class="cell medium-3 large-2 {{ !($value = $file->value ?? '') ? 'hide' : '' }}"
     id="image_preview_{{ $id = $id ?? $element->getId($file) }}">
    <div data-open="image_container_{{ $id }}" class="text-center">

        @if (isset($slot) && e($slot))

            {!! $slot !!}

        @endif

        <img src="{{ $src = preg_match("/\.(jpg|jpeg|png|gif|svg)$/i", $value) ? $value : 'data:,' }}"
             title="{{ $value }}"
             class="thumbnail {{ $src == 'data:,' ? 'hide' : '' }}">
    </div>

    <div class="reveal {{ $containerClass ?? '' }}" id="image_container_{{ $id }}"
         data-reveal {!! $containerAttributes ?? 'data-multiple-opened="true"' !!}>
        <p class="text-center"><img src="{{ $src }}" title="{{ $value }}"></p>

        @if (isset($element))

            @include("form::layouts.file.js.imageEditor")

        @endif

        <button class="close-button" data-close aria-label="Close reveal" type="button">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</div>
