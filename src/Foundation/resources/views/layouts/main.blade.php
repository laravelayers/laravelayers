<div class="main {{ !empty($full) ? 'full' : '' }}" id="main">
    <div class="grid-container {{ !empty($full) ? 'fluid' : '' }}">
        <span id="main-top-anchor"></span>
        <div class="grid-x grid-padding-x grid-padding-y {{ $full ?? 'align-top' }}">

            {{ $slot }}

        </div>
        <span id="main-bottom-anchor"></span>
    </div>
</div>
