@can('admin.*')

<div class="admin-menu-bar off-canvas position-left {{ Request::is(['admin', 'admin/*']) ? 'reveal-for-large' : '' }}"
     id="offCanvasLeft" data-off-canvas data-transition="{{ Request::is(['admin', 'admin/*']) ? 'overlap' : 'overlap' }}">
    <a class="close-button small" aria-label="Close menu" type="button" data-close>
        <span aria-hidden="true">&times;</span>
    </a>

    <div class="header">

        @component('admin::layouts.breadcrumbs')

            <ul class="breadcrumbs">
                <li>

                    @if (Request::is('admin'))

                        <a href="/">{{ config('app.name') }}</a>

                    @else

                        <a href="{{ route('admin.index') }}">@lang('admin::admin.menu.name')</a>

                    @endif

                </li>
            </ul>

        @endcomponent

    </div>

    @if (Route::currentRouteName() != 'home')

    <div class="{{ Request::is(['admin', 'admin/*']) ? 'hide-for-medium-only' : 'hide-for-medium' }}">

        @include('auth::layouts.auth', ['isDropdown' => true, 'isAccordion' => true, 'isAvatar' => true])

    </div>

    @endif

    @foreach($menu as $item)

        @include('navigation::layouts.accordion.menu', ['tree' => $menu->getNode($item->getNodeId())])

    @endforeach

</div>

@endcan