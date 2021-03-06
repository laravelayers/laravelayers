@if (!empty($tree) && $tree->isNotEmpty())

    @component(e($list), ['tree' => $tree])

        @slot('listClass')

            {{ $listClass }}

        @endslot

        @slot('listAttributes')

            {{ $listAttributes }}

        @endslot

        @foreach ($tree as $key => $node)

            @include('navigation::layouts.treeItem')

        @endforeach

    @endcomponent

@endif