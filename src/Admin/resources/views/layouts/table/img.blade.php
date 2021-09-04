@if (e($slot))

    <div data-toggle="{{ $id = uniqid() }}" class="{{ !empty($class) ? $class : 'text-center' }}">
        <img src="{{ $slot }}" class="thumbnail">
    </div>

    <div class="reveal text-center" id="{{ $id }}" data-reveal>
        <img src="{{ $link ?? $slot }}">
        <button class="close-button" data-close aria-label="Close reveal" type="button">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

@endif