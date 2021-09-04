<div class="progress {{ $class ?? 'hide' }}" role="progressbar" tabindex="0" {{ $attributes ?? '' }}
     aria-valuenow="{{ $valueNow ?? 0 }}" aria-valuemin="{{ $valueMin ?? 0 }}" aria-valuemax="{{ $valueMax ?? 100 }}">
    <div class="progress-meter" {!! isset($style) ? 'style="'. $style .'"' : '' !!}>
        <p class="progress-meter-text {{ e($slot) ?: 'hide'}}">{{ $slot }}</p>
    </div>
</div>