@if (!$node->nodeLevel && !$node->isNodeSelected && $node->getTree()->count() > 1)

    <li>

        @component('navigation::layouts.list')

            @slot('listClass')

                dropdown

            @endslot

            @slot('listAttributes')

                data-dropdown-menu

            @endslot

            @slot('sublistClass')

                vertical

            @endslot

            @include('navigation::layouts.item', ['isPath' => true])

        @endcomponent

    </li>

@else

    @if (!$node->nodeLevel && $node->isNodeSelected)

        @component('navigation::layouts.breadcrumbs.treeItemCurrent')

            <span class="{{ $node->menuClass }}">{{ $node->menuName }}</span>

        @endcomponent

    @else

        @include('navigation::layouts.item', ['slot' => $node->getTree()->count() > 1 ? $slot : '', 'isPath' => true])

    @endif

@endif

