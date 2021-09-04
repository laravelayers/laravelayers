@component($item, ['tree' => $tree, 'node' => $node])

    @slot('itemClass')

        {{ $itemClass }}

        @if (strlen($node->menuParentId) && $node->getTree()->isNotEmpty())

            is-subtree-parent

        @endif

        @if ($node->isSelected)

            active

        @endif

        @if ($node->isNodeSelected)

            is-active-node

        @endif

    @endslot

    @slot('itemAttributes')

        {{ $itemAttributes }}

    @endslot

    @include('navigation::layouts.treeSublist')

@endcomponent