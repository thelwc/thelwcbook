@if ($paginator->hasPages())
    <style>
        .custom-pagination .page-link {
            border: none;
            color: #6c757d;
            font-weight: 600;
            border-radius: 8px !important;
            min-width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 3px;
            transition: all 0.2s ease;
            background-color: transparent;
        }
        .custom-pagination .page-link:hover {
            background-color: #f1f5f9;
            color: #0d6efd;
            transform: translateY(-2px);
        }
        .custom-pagination .page-item.active .page-link {
            background-color: #0d6efd;
            color: #fff;
            box-shadow: 0 4px 10px rgba(13, 110, 253, 0.3);
            transform: translateY(-2px);
        }
        .custom-pagination .page-item.disabled .page-link {
            color: #dee2e6;
            background-color: transparent;
            pointer-events: none;
        }
    </style>

    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center mb-0 custom-pagination">
            
            {{-- Nút Trang Đầu (First Page) --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled"><span class="page-link" title="Trang đầu"><i class="fas fa-angle-double-left"></i></span></li>
            @else
                <li class="page-item"><a class="page-link" href="{{ $paginator->url(1) }}" title="Trang đầu"><i class="fas fa-angle-double-left"></i></a></li>
            @endif

            {{-- Nút Lùi (Previous Page) --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled"><span class="page-link" title="Trang trước"><i class="fas fa-angle-left"></i></span></li>
            @else
                <li class="page-item"><a class="page-link" href="{{ $paginator->previousPageUrl() }}" title="Trang trước"><i class="fas fa-angle-left"></i></a></li>
            @endif

            {{-- Các Số Trang (1, 2, 3, ...) --}}
            @foreach ($elements as $element)
                {{-- Dấu "..." --}}
                @if (is_string($element))
                    <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
                @endif

                {{-- Mảng các trang --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Nút Tới (Next Page) --}}
            @if ($paginator->hasMorePages())
                <li class="page-item"><a class="page-link" href="{{ $paginator->nextPageUrl() }}" title="Trang sau"><i class="fas fa-angle-right"></i></a></li>
            @else
                <li class="page-item disabled"><span class="page-link" title="Trang sau"><i class="fas fa-angle-right"></i></span></li>
            @endif

            {{-- Nút Trang Cuối (Last Page) --}}
            @if ($paginator->hasMorePages())
                <li class="page-item"><a class="page-link" href="{{ $paginator->url($paginator->lastPage()) }}" title="Trang cuối"><i class="fas fa-angle-double-right"></i></a></li>
            @else
                <li class="page-item disabled"><span class="page-link" title="Trang cuối"><i class="fas fa-angle-double-right"></i></span></li>
            @endif
            
        </ul>
    </nav>
@endif