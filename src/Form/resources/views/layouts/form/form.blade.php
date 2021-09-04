<form {!! $elements->getForm('method') ? 'method="' . $elements->getForm('method') . '"' : '' !!}
      {!! $elements->getForm('action') ? 'action="' . $elements->getForm('action') . '"' : '' !!}
      {!! $elements->getForm()->name ? 'name="' . $elements->getForm()->name .'"' : '' !!}
      {!! $elements->getForm()->id ? 'id="' . $elements->getForm()->id .'"' : '' !!}
      class="{{ $class ?? '' }} {{ $elements->getForm()->class }}"
        {{ $attributes ?? '' }} {!! $elements->getForm()->attributes !!}>

    {{ $elements->getForm('method') == 'POST' ? csrf_field() : '' }}
    {{ $elements->getForm('methodField') ? method_field($elements->getForm('methodField')) : '' }}

    @include('form::layouts.form.success', ['closable' => true, 'reloadable' => $elements->getForm()->hidden, 'slot' => ''])

    @include('form::layouts.form.warnings')

    @if (!$elements->getForm()->hidden)

        {{ $slot }}

    @endif

</form>
