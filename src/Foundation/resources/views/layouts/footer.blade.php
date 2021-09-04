<div class="footer">
    <div class="grid-container {{ !empty($full) ? 'fluid' : '' }}">
        <div class="grid-x grid-padding-x grid-padding-y">

            @if (e($slot ?? ''))

                {!! $slot !!}

            @else

                @includeIf('layouts.footer')

            @endif

        </div>
    </div>
</div>
