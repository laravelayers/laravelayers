<div class="auth">
    <ul class="menu medium-horizontal {{ !empty($isDropdown) ? 'dropdown vertical' : '' }}"
            {!! !empty($isDropdown) ? 'data-responsive-menu="accordion' . (empty($isAccordion) ? ' medium-dropdown' : '') . '""' : '' !!}>

        @guest

            @push('stack_auth_login_' . ($uniqid = uniqid()))

                <a href="{{ route('login') }}">
                    @icon('icon-sign-in-alt icon-fw') {{ Lang::get((!Lang::has('auth.login') ? 'auth::' : '') . 'auth.login') }}
                </a>

            @endpush

            @push('stack_auth_register_' . $uniqid)

                @if (($isRegister = (Route::currentRouteName() != 'register')) && Route::has('register'))

                    <li>
                        <a href="{{ route('register') }}">
                            @icon('icon-user-plus icon-fw') {{ Lang::get((!Lang::has('auth.register') ? 'auth::' : '') . 'auth.register') }}
                        </a>
                    </li>

                @endif

            @endpush

            @if ($isNotLogin = (Route::currentRouteName() != 'login'))

                <li>

                    @stack('stack_auth_login_' . $uniqid)

                    @if (!empty($isDropdown) && Route::has('register') && !starts_with(Request::url(), route('register')))

                        <ul class="menu vertical nested{{ !empty($isDropdown) && !empty($isActive) ? ' is-active' : ''}}">
                            <li>

                                @stack('stack_auth_login_' . $uniqid)

                            </li>

                            @stack('stack_auth_register_' . $uniqid)

                        </ul>

                    @endif

                </li>

            @endif

            @if (empty($isDropdown) || !$isNotLogin)

                @stack('stack_auth_register_' . $uniqid)

            @endif

        @else

            @push('stack_auth_user_' . ($uniqid = uniqid()))

                <a href="{{ route('home') }}" {{ !empty($isAvatar) && Auth::user()->getAvatar() ? 'class="avatar"' : '' }}>

                    @if (!empty($isAvatar) && Auth::user()->getAvatar())

                        <img src="{{ Auth::user()->getAvatar() }}" onclick="event.stopPropagation(); document.location.href=this.parentNode.href;">

                    @endif

                    <span>@icon('icon-user icon-fw') {{ Auth::user()->name }}</span>

                </a>

            @endpush

            @push('stack_auth_logout_' . $uniqid)

                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        {{ csrf_field() }}
                        <a onclick="this.parentNode.submit();">
                            @icon('icon-sign-out-alt icon-fw') {{ Lang::get((!Lang::has('auth.logout') ? 'auth::' : '') . 'auth.logout') }}
                        </a>
                    </form>
                </li>

            @endpush

            @if ($isNotHome = (Route::currentRouteName() != 'home'))

                <li>

                    @stack('stack_auth_user_' . $uniqid)

                    @if (!empty($isDropdown))

                        <ul class="menu vertical nested">
                            <li>

                                @stack('stack_auth_user_' . $uniqid)

                            </li>

                            @stack('stack_auth_logout_' . $uniqid)

                        </ul>

                    @endif

                </li>

            @endif

            @if (empty($isDropdown) || !$isNotHome)

                @stack('stack_auth_logout_' . $uniqid)

            @endif

        @endguest

    </ul>
</div>
