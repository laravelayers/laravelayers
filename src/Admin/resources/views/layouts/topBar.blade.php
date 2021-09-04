@can('admin.*')

    @push('adminTopBar')

        <div class="top-bar" data-margin-top="0" id="admin-menu">
            <div class="grid-container {{ !empty($full) ? 'fluid' : '' }}">
                <div class="grid-x align-middle">
                    <div class="auto cell">
                        <div class="admin-top-bar-left">
                            <ul class="dropdown menu vertical">
                                <li>
                                    <a data-toggle="offCanvasLeft">
                                        <i class="icon icon-bars" aria-hidden="true"></i> <span>@lang('admin::admin.menu.name')</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="shrink cell hide-for-small-only">
                        <div class="admin-top-bar-right">
                            <div class="grid-x align-right">
                                <div class="shrink cell">

                                    @include('auth::layouts.auth', ['isDropdown' => false, 'isAccordion' => false, 'isAvatar' => true])

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endpush

    <div class="sticky admin-top-bar show-for-medium {{ Request::is(['admin', 'admin/*']) ? 'hide-for-large' : '' }}" data-sticky data-margin-top="0">

        @stack('adminTopBar')

    </div>

    <div class="admin-top-bar show-for-small-only">

        @stack('adminTopBar')

    </div>

@endcan
