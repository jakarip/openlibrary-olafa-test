@if ($paginator->hasPages())
    <nav aria-label="Page navigation">
        <ul class="pagination pagination-danger justify-content-end">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item prev disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <a class="page-link" href="javascript:void(0);"><i class="ti ti-chevron-left ti-xs"></i></a>
                </li>
            @else
                <li class="page-item prev">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">
                        <i class="ti ti-chevron-left ti-xs"></i>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true"><a class="page-link" href="javascript:void(0);">{{ $element }}</a></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page"><a class="page-link" href="javascript:void(0);">{{ $page }}</a></li>
                        @else
                            <li class="page-item"><a href="{{ $url }}" class="page-link">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item next">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">
                        <i class="ti ti-chevron-right ti-xs"></i>
                    </a>
                </li>
            @else
                <li class="page-item next disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <a class="page-link" href="javascript:void(0);"><i class="ti ti-chevron-right ti-xs"></i></a>
                </li>
            @endif
        </ul>
    </nav>
@endif
