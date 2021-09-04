@component("form::layouts.form.form", ['elements' => $elements])

    @slot('class')

        {{ $elements->getForm()->class === '' ? 'expanded' : '' }}

    @endslot

    @slot('slot')

        @component('form::layouts.form.errors', ['errors' => $errors ?? []])

        @endcomponent

        {{ $slot }}

    @endslot

@endcomponent
