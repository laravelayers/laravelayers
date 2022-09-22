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

            ;(function () {
                function copy_breadcrumbs() {
                    if ($('#breadcrumbs_{{ $tree->last()->nodeId }}').is(':hidden')) {
                        $('#breadcrumbs_current_{{ $tree->last()->nodeId }}').removeClass('is-hidden');

                        if ($('#breadcrumbs_current_{{ $tree->last()->nodeId }} > li').length === 1) {
                            $('#breadcrumbs_{{ $tree->last()->nodeId }} > li:last-child')
                                .detach()
                                .appendTo('#breadcrumbs_current_{{ $tree->last()->nodeId }}');
                        }
                    } else {
                        if ($('#breadcrumbs_current_{{ $tree->last()->nodeId }} > li').length > 1) {
                            $('#breadcrumbs_current_{{ $tree->last()->nodeId }}')
                                .addClass('is-hidden')
                                .children('li:last-child')
                                .detach()
                                .appendTo('#breadcrumbs_{{ $tree->last()->nodeId }}');
                        }
                    }
                }

                $(function() {
                    copy_breadcrumbs();
                });

                $(window).on('resize', function() {
                    copy_breadcrumbs();
                });

                $('#breadcrumbs_{{ $tree->last()->nodeId }}').on('off.zf.toggler', function() {
                    copy_breadcrumbs();
                });
            }());

        </script>

    @endpush

@endif
