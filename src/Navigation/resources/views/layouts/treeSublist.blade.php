@if (strlen($node->menuParentId) && $node->getTree()->isNotEmpty())

    @component(e($sublist), ['tree' => $tree, 'node' => $node])

        @slot('sublistClass')

            {{ $sublistClass }} is-subtree

            @if ($node->isNodeSelected)

                is-active

            @endif

        @endslot

        @slot('sublistAttributes')

            {{ $sublistAttributes }}

        @endslot

        @foreach ($node->getTree() as $node)

            @include('navigation::layouts.treeItem')

        @endforeach

    @endcomponent

@endif