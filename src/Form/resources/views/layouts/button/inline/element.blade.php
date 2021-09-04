@foreach ($element as $key => $buttons)

    @foreach ($buttons as $buttonKey => $button)

        @include($button->view)

    @endforeach

@endforeach