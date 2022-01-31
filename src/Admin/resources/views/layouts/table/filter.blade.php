@if ($items->getFilter()->isNotEmpty())

    <div class="grid-container full">
        <div class="grid-x grid-padding-x align-top">

            @if ($items->getFilterResetLink() && !$items->getFilterResetLink()->getHidden())

                <div class="shrink cell">

                    {!! $items->getFilterResetLink() !!}

                </div>

            @endif

            <div class="medium-auto cell">

                @component("form::layouts.label.element", ['element' => $items->getFilterLink()])

                    @if (!$items->getFilterLink()->getAttributes('data-open'))

                        @slot('attributes')

                            data-open="filter"

                        @endslot

                    @endif

                    {!! (clone $items->getFilterLink())->put('label', '')->put('help', '')->put('tooltip', '') !!}

                @endcomponent

            </div>

        </div>
    </div>

    <div class="large reveal" id="filter" data-reveal data-multiple-opened="true"
         data-deep-link="true" data-update-history="true">

        <p>
            <a id="icon_{{ $items->getFilter()->getForm()->getId() }}" title="@lang('admin::admin.filter.reset_button')">
                @icon('icon-filter')
            </a>
        </p>

        {!! $items->getFilter() !!}

        <button class="close-button" data-close aria-label="Close modal" type="button">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

    @push('scripts')

        <script>

            $('#icon_{{ $items->getFilter()->getForm()->getId() }}').on('click', function() {
                $('#{{ $items->getFilter()->getForm()->getId() }}').get(0).reset();
            });

            $('#label_{{ $items->getFilterLink()->id }}').on('click', function() {
                let open = $(this).attr('data-open');

                if (open) {
                    open = '#' + open;

                    $(open).keypress(function() {
                        if ($(this).attr('data-is-key-pressed') === undefined) {
                            $(this).attr('data-is-key-pressed', true);
                            $(open).find('input[type={{ $items->getFilterLink()->type }}]').focus();
                        }

                    });

                    $(open).find('.callout').find('.close-button').trigger('click');
                    $(open).foundation('open');
                }
            });

            $('#label_{{ $items->getFilterLink()->id }} input').on('input', function() {
                $(this).val(
                    $('#' + $('#label_{{ $items->getFilterLink()->id }}').attr('data-open'))
                        .find('input[type={{ $items->getFilterLink()->type }}]').val()
                );
            });

            $('#{{ $items->getFilter()->getForm()->getId() }}').find('.callout .close-button').click(function() {
                $('#{{ $items->getElements()->getForm()->getId() }}').find('.callout').hide();
            });

        </script>

    @endpush
@endif

{!! $items->getQuickFilter() !!}
