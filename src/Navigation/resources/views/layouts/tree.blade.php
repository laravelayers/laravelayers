@component('navigation::layouts.treeList', ['tree' => $tree])

    @slot('list')

        {{ isset($list) && !empty(e($list)) ? $list : 'navigation::layouts.list' }}

        @slot('listClass')

            {{ isset($listClass) && !empty(e($listClass)) ? $listClass : '' }}

        @endslot

        @slot('listAttributes')

            {{ isset($listAttributes) && !empty(e($listAttributes)) ? $listAttributes : '' }}

        @endslot

    @endslot

    @slot('sublist')

        {{ isset($sublist) && !empty(e($sublist)) ? $sublist : 'navigation::layouts.sublist' }}

        @slot('sublistClass')

            {{ isset($sublistClass) && !empty(e($sublistClass)) ? $sublistClass : '' }}

        @endslot

        @slot('sublistAttributes')

            {{ isset($sublistAttributes) && !empty(e($sublistAttributes)) ? $sublistAttributes : '' }}

        @endslot

    @endslot

    @slot('item')

        {{ isset($item) && !empty(e($item)) ? $item : 'navigation::layouts.item' }}

        @slot('itemClass')

            {{ isset($itemClass) && !empty(e($itemClass)) ? $itemClass : '' }}

        @endslot

        @slot('itemAttributes')

            {{ isset($itemAttributes) && !empty(e($itemAttributes)) ? $itemAttributes : '' }}

        @endslot

    @endslot

@endcomponent