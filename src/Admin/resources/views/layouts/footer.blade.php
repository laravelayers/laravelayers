@if (e($slot ?? ''))

    <div class="footer hide">
        <div class="grid-container {{ !empty($full) ? 'fluid' : '' }}">
            <div class="grid-x grid-padding-x grid-padding-y">

                {!! $slot !!}

            </div>
        </div>
    </div>

@endif
