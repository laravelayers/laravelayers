<option value="{{ $tree->getValue($node) }}"
        id="{{ $tree->getId($node) }}"
        {{ $tree->getIsSelected($node) ? 'selected' : '' }}>
    {{ str_repeat('...', $node->nodeLevel) }} {{ $tree->getText($node) }}
</option>

{{ $slot }}
