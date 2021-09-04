@includeFirst(['layouts.simpleTitleBar', 'foundation::layouts.simpleTitleBar'])

<div class="simple-top-bar" id="top-bar">
    <div class="grid-container {{ !empty($full) ? 'fluid' : '' }}">
        <div class="grid-x align-middle">
            <div class="auto cell simple-top-bar-left hide-for-small-only">

                @includeFirst(['layouts.simpleTopBarLeft', 'foundation::layouts.topBarLeft'])

            </div>
            <div class="medium-shrink cell simple-top-bar-right">

                @cannot('admin.*')

                    @includeFirst(['layouts.auth', 'auth::layouts.auth'], ['isDropdown' => false])

                @endcannot

            </div>
        </div>
    </div>
</div>