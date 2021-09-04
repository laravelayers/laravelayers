<div class="title-bar hide-for-medium" data-responsive-toggle="top-bar" data-hide-for="medium">
    <div class="grid-container {{ !empty($full) ? 'fluid' : '' }}">
        <div class="grid-x grid-padding-x align-middle">
            <div class="shrink cell title-bar-left">

                @includeFirst(['layouts.titleBarLeft', 'foundation::layouts.titleBarLeft'])

            </div>
            <div class="auto cell title-bar-right">

                @includeFirst(['layouts.titleBarRight', 'foundation::layouts.titleBarRight'])

            </div>
        </div>
    </div>
</div>
