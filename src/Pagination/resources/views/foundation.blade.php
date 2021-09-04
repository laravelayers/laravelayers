@if ($paginator->hasPages())

    <ul class="pagination" role="navigation" aria-label="Pagination">

        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())

            <li class="disabled"><span>@lang('pagination::pagination.previous', ['page' => '<span class="show-for-sr">' . trans('pagination::pagination.page') . '</span>'])</span></li>

        @else

            <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev">@lang('pagination::pagination.previous', ['page' => '<span class="show-for-sr">' . trans('pagination::pagination.page') . '</span>'])</a></li>

        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements ?? [] as $element)

            {{-- "Three Dots" Separator --}}
            @if (is_string($element))

                <li class="disabled"><span>{{ $element }}</span></li>

            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))

                @foreach ($element as $page => $url)

                    @if ($page == $paginator->currentPage())

                        <li class="current"><span><span class="show-for-sr">You're on page</span> {{ $page }}</span></li>

                    @else

                        <li><a href="{{ $url }}">{{ $page }}</a></li>

                    @endif

                @endforeach

            @endif

        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())

            <li><a href="{{ $paginator->nextPageUrl() }}" aria-label="Next page" rel="next">@lang('pagination::pagination.next', ['page' => '<span class="show-for-sr">' . trans('pagination::pagination.page') . '</span>'])</a></li>

        @else

            <li class="disabled"><span>@lang('pagination::pagination.next', ['page' => '<span class="show-for-sr">' . trans('pagination::pagination.page') . '</span>'])</span></li>

        @endif

    </ul>

@endif
