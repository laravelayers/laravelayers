{{ $slot ?? '' }}

@push('file_' . $element->id)

    <label id="label_file_{{ $element->id }}" for="{{ $element->id }}"
           class="button {{ $class ?? '' }} {{ $element->class }}"
            {{ $element->getAttributes('disabled') ? 'disabled' : '' }}>
        @if (!$element->reverse){!! $element->icon ?: ($icon ?? '') !!}@endif
        {{ $element->text ?: trans('form::form.file.upload_' . ($element->multiple ? 'multiple' : 'once')) }}
        @if ($element->reverse){!! $element->icon ?: ($icon ?? '') !!}@endif
    </label>

    <input type="{{ $element->type }}"
           name="{{ $element->name }}"
           id="{{ $element->id }}"
           class="show-for-sr"
            {{ $element->multiple ? 'multiple' : '' }}
            {!! $element->helpId ? 'aria-describedby="' . $element->helpId . '"' : '' !!}
           {{ $attributes ?? '' }} {!! $element->attributes !!}>

@endpush

@if ($element->label)

    <div>@stack('file_' . $element->id)</div>

@else

    <span>@stack('file_' . $element->id)</span>

@endif
