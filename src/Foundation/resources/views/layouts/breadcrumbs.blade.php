@if (e($slot))

    @component('navigation::layouts.breadcrumbs.nav.nav')

        <span>
            <div class="grid-container {{ !empty($full) ? 'fluid' : '' }}">
                <div class="grid-x grid-padding-x align-middle">
                    <div class="cell auto">

                        {{ $slot }}

                    </div>

                    @if (e($right ?? ''))

                        <div class="cell shrink">

                            {{ $right }}

                        </div>

                    @endif

                </div>
            </div>
        </span>

    @endcomponent

@endif