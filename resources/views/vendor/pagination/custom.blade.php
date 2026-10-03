@if ($paginator->hasPages())
    <nav class="flex items-center justify-end space-x-2 my-4" aria-label="Pagination">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="w-10 h-10 flex items-center justify-center rounded-[5px] border border-slate-200 dark:border-slate-800 text-slate-300 dark:text-slate-600 bg-slate-50/50 dark:bg-slate-900/50 cursor-not-allowed">
                <i class="feather icon-chevron-left"></i>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="w-10 h-10 flex items-center justify-center rounded-[5px] border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-800 hover:bg-primary hover:text-white dark:hover:bg-primary-600 transition-all duration-300 shadow-sm hover:shadow-md hover:-translate-y-0.5">
                <i class="feather icon-chevron-left"></i>
            </a>
        @endif

        {{-- Pagination Elements --}}
        <div class="flex items-center gap-2">
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="px-2 text-slate-400 dark:text-slate-600">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="w-10 h-10 flex items-center justify-center rounded-[5px] bg-primary text-white font-bold shadow-lg shadow-primary/25 z-10">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="w-10 h-10 flex items-center justify-center rounded-[5px] border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-800 hover:border-primary hover:text-primary transition-all duration-300 shadow-sm">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="w-10 h-10 flex items-center justify-center rounded-[5px] border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-800 hover:bg-primary hover:text-white dark:hover:bg-primary-600 transition-all duration-300 shadow-sm hover:shadow-md hover:-translate-y-0.5">
                <i class="feather icon-chevron-right"></i>
            </a>
        @else
            <span class="w-10 h-10 flex items-center justify-center rounded-[5px] border border-slate-200 dark:border-slate-800 text-slate-300 dark:text-slate-600 bg-slate-50/50 dark:bg-slate-900/50 cursor-not-allowed">
                <i class="feather icon-chevron-right"></i>
            </span>
        @endif
    </nav>
@endif
