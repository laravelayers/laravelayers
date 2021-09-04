@includeFirst(['layouts.titleBar', 'foundation::layouts.titleBar'])

<div class="top-bar" id="top-bar">
    <div class="grid-container {{ !empty($full) ? 'fluid' : '' }}">
        <div class="grid-x grid-padding-x grid-padding-y align-middle">
            <div class="medium-shrink cell text-center hide-for-small-only top-bar-left">
                <div>

                    @includeFirst(['layouts.topBarLeft', 'foundation::layouts.topBarLeft'])

                </div>
            </div>
            <div class="medium-auto cell top-bar-center">

                @includeFirst(['layouts.topBarCenter', 'foundation::layouts.topBarCenter'])

            </div>
            <div class="medium-shrink cell top-bar-right" id="top-bar-right">
                <div>

                    @includeFirst(['layouts.topBarRight', 'foundation::layouts.topBarRight'])

                </div>
            </div>

        </div>
    </div>
</div>
