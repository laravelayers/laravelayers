<span data-form-beforeunload class="form-beforeunload">

    @component("form::layouts.form.form", ['elements' => $elements])

        @slot('class')

            abide {{ !$elements->getForm()->class ? 'expanded' : '' }}

        @endslot

        @slot('attributes')

            data-abide novalidate

        @endslot

        @include('form::layouts.form.js.errors')

        {{ $slot }}

    @endcomponent

</span>

@push('scripts')

    <script>

        $('#{{ $elements->getForm()->id }}').foundation('scrollToError');
        $('#{{ $elements->getForm()->id }}').foundation('saveButtonValue');

    </script>

@endpush
