<div class="simple-title-bar" data-responsive-toggle="top-bar" data-hide-for="medium">
    <div class="grid-container {{ !empty($full) ? 'fluid' : '' }}">
        <div class="grid-x align-middle">
            <div class="auto cell simple-title-bar-left">

                @includeFirst(['layouts.simpleTitleBarLeft', 'foundation::layouts.titleBarLeft'])

            </div>

            @cannot('admin.*')

            <div class="auto cell simple-title-bar-right">

                @includeFirst(['layouts.simpleTitleBarRight', 'foundation::layouts.titleBarRight'])

            </div>

            @endcannot

        </div>
    </div>
</div>
