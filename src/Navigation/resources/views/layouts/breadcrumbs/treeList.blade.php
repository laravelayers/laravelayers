<ul class="breadcrumbs {{ !empty($tree) ? 'show-for-medium' : '' }}"
    id="breadcrumbs{{ !empty($tree) && $tree->isNotEmpty() ? '_' . $tree->last()->nodeId : '' }}"
    data-toggler="show-for-medium">

    {{ $slot }}

</ul>

@if (!empty($tree) && $tree->isNotEmpty())

<ul class="breadcrumbs show-for-small-only" id="breadcrumbs_current_{{ $tree->last()->nodeId }}">
    <li>
        <a href="#" data-toggle="breadcrumbs_{{ $tree->last()->nodeId }}">@icon('icon-chevron-left')</a>
    </li>

    @include('navigation::layouts.breadcrumbs.treeItem', ['node' => $tree->last()])

</ul>

    @push('scripts')

    <script>

        $('#breadcrumbs_{{ $tree->last()->nodeId }}').on('off.zf.toggler', function() {
            $('#breadcrumbs_current_{{ $tree->last()->nodeId }}').addClass('is-hidden');
        });

    </script>

    @endpush

@endif
