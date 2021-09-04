@if (Route::currentRouteName() != 'login' || Route::has('register'))

    <a data-toggle="top-bar">@icon('icon-bars')</a>

@endif
