<a {{ request()->path() != '/' ? 'href=' . url('/')  : '' }}>
    @icon('icon-home')
</a>
