<ul class="breadcrumbs {{ !empty($tree) && $tree->count() > 1 ? 'show-for-medium' : '' }}"
    id="breadcrumbs{{ !empty($tree) && $tree->isNotEmpty() ? '_' . $tree->last()->nodeId : '' }}"
    data-toggler="show-for-medium">

    {{ $slot }}

</ul>

@if (!empty($tree) && $tree->count() > 1)

<ul class="breadcrumbs show-for-small-only" id="breadcrumbs_current_{{ $tree->last()->nodeId }}">
    <li>
        <a href="#" data-toggle="breadcrumbs_{{ $tree->last()->nodeId }}">@icon('icon-chevron-left')</a>
    </li>
</ul>

    @push('scripts')

        <script>

            $(function() {
                $('#breadcrumbs_{{ $tree->last()->nodeId }} > li:last-child')
                    .detach()
                    .appendTo('#breadcrumbs_current_{{ $tree->last()->nodeId }}');
            });

            $('#breadcrumbs_{{ $tree->last()->nodeId }}').on('off.zf.toggler', function() {
                $('#breadcrumbs_current_{{ $tree->last()->nodeId }}')
                    .addClass('is-hidden')
                    .children('li:last-child')
                    .detach()
                    .appendTo('#breadcrumbs_{{ $tree->last()->nodeId }}');
            });

        </script>

    @endpush

@endif
