@if (e($slot))

    <div class="header-bar" id="header-bar">
        <div class="grid-container {{ !empty($full) ? 'fluid' : '' }}">
            <div class="grid-x grid-padding-x">
                <div class="cell">

                    {{ $slot }}

                </div>
            </div>
        </div>
    </div>

@endif