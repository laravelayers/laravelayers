<div class="reveal {!! $class ?? '' !!}"
     id="gallery_container_{{ $element->id }}"
     data-reveal {!! $attributes ?? '' !!} {!! $element->getAttributes('reveal') !!}
     aria-labelledby="header_gallery_container_{{ $element->id }}">

    <p id="header_gallery_container_{{ $element->id }}">
        {!! $element->label ?: '&nbsp;' !!}
    </p>

    <span id="gallery_{{ $element->id }}"></span>

    <button class="close-button" data-close aria-label="Close modal" type="button">
        <span aria-hidden="true">&times;</span>
    </button>
</div>