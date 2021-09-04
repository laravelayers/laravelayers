@if ($params->html)

    <span class="cropped-text" data-open="{{ $reveal_id = "container_{$params['column']}_{$item->getKey()}" }}">
        {!! $item->getCroppedRenderText($params['column'], $params->length) !!}
    </span>

    <div class="large reveal" id="{{ $reveal_id }}" data-reveal>
        <p class="h4">{{ $params->name }}</p>
        {!! $item->getRenderer($params['column']) !!}
        <button class="close-button" data-close aria-label="Close modal" type="button">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

@else

    <span class="cropped-text" data-toggle="{{ $reveal_id = "container_{$params['column']}_{$item->getKey()}" }}">

        {!! $item->getCroppedRenderText($params['column'], $params->length) !!}

    </span>
    <span class="expanded-text is-hidden" id="{{ $reveal_id }}" data-toggler="is-hidden">
        {!! $item->getCroppedRenderText($params['column'], $params->length, true) !!}
    </span>
    <span class="expand-text" data-toggle="{{ $reveal_id }}">...</span>

@endif
