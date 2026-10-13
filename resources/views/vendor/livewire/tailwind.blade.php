@php
if (! isset($scrollTo)) {
    $scrollTo = 'body';
}

$scrollIntoViewJsSnippet = ($scrollTo !== false)
    ? <<<JS
       (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()
    JS
    : '';
@endphp

<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between gap-4">
            {{-- Mobile Pagination View --}}
            <div class="flex items-center justify-between flex-1 sm:hidden">
                <div>
                    @if ($paginator->onFirstPage())
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-300 bg-slate-50 border border-slate-200 rounded-md cursor-not-allowed">
                            <i class="ri-arrow-left-s-line text-sm"></i>
                            <span>{!! __('pagination.previous') !!}</span>
                        </span>
                    @else
                        <button 
                            type="button" 
                            wire:click="previousPage('{{ $paginator->getPageName() }}')" 
                            x-on:click="{{ $scrollIntoViewJsSnippet }}" 
                            wire:loading.attr="disabled" 
                            dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.before" 
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white border border-slate-200 rounded-md shadow-2xs hover:bg-slate-50 hover:text-slate-900 active:scale-95 transition-all cursor-pointer"
                        >
                            <i class="ri-arrow-left-s-line text-sm"></i>
                            <span>{!! __('pagination.previous') !!}</span>
                        </button>
                    @endif
                </div>

                <div class="text-xs text-slate-500 font-medium">
                    <span>Page</span>
                    <span class="font-bold text-slate-900">{{ $paginator->currentPage() }}</span>
                    <span>of</span>
                    <span class="font-bold text-slate-900">{{ $paginator->lastPage() }}</span>
                </div>

                <div>
                    @if ($paginator->hasMorePages())
                        <button 
                            type="button" 
                            wire:click="nextPage('{{ $paginator->getPageName() }}')" 
                            x-on:click="{{ $scrollIntoViewJsSnippet }}" 
                            wire:loading.attr="disabled" 
                            dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.before" 
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white border border-slate-200 rounded-md shadow-2xs hover:bg-slate-50 hover:text-slate-900 active:scale-95 transition-all cursor-pointer"
                        >
                            <span>{!! __('pagination.next') !!}</span>
                            <i class="ri-arrow-right-s-line text-sm"></i>
                        </button>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-300 bg-slate-50 border border-slate-200 rounded-md cursor-not-allowed">
                            <span>{!! __('pagination.next') !!}</span>
                            <i class="ri-arrow-right-s-line text-sm"></i>
                        </span>
                    @endif
                </div>
            </div>

            {{-- Desktop Pagination View --}}
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between gap-4">
                {{-- Showing Results Info --}}
                <div>
                    <p class="text-xs text-slate-500 font-medium">
                        <span>{!! __('Showing') !!}</span>
                        <span class="font-bold text-slate-900">{{ $paginator->firstItem() }}</span>
                        <span>{!! __('to') !!}</span>
                        <span class="font-bold text-slate-900">{{ $paginator->lastItem() }}</span>
                        <span>{!! __('of') !!}</span>
                        <span class="font-bold text-slate-900">{{ $paginator->total() }}</span>
                        <span>{!! __('results') !!}</span>
                    </p>
                </div>

                {{-- Page Navigation Links --}}
                <div class="flex items-center gap-1">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                            <span class="inline-flex items-center justify-center w-8 h-8 text-xs font-semibold text-slate-300 bg-slate-50 border border-slate-200 rounded-md cursor-not-allowed" aria-hidden="true">
                                <i class="ri-arrow-left-s-line text-base"></i>
                            </span>
                        </span>
                    @else
                        <button 
                            type="button" 
                            wire:click="previousPage('{{ $paginator->getPageName() }}')" 
                            x-on:click="{{ $scrollIntoViewJsSnippet }}" 
                            dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.after" 
                            class="inline-flex items-center justify-center w-8 h-8 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-md shadow-2xs hover:bg-slate-100 hover:text-slate-900 active:scale-95 transition-all cursor-pointer" 
                            aria-label="{{ __('pagination.previous') }}"
                            title="Previous Page"
                        >
                            <i class="ri-arrow-left-s-line text-base"></i>
                        </button>
                    @endif

                    {{-- Pagination Number Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="inline-flex items-center justify-center w-8 h-8 text-xs font-bold text-slate-400 cursor-default">
                                    {{ $element }}
                                </span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                <span wire:key="paginator-{{ $paginator->getPageName() }}-page{{ $page }}">
                                    @if ($page == $paginator->currentPage())
                                        <span aria-current="page">
                                            <span class="inline-flex items-center justify-center w-8 h-8 text-xs font-bold text-white bg-slate-900 border border-slate-900 rounded-md shadow-xs cursor-default">
                                                {{ $page }}
                                            </span>
                                        </span>
                                    @else
                                        <button 
                                            type="button" 
                                            wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" 
                                            x-on:click="{{ $scrollIntoViewJsSnippet }}" 
                                            class="inline-flex items-center justify-center w-8 h-8 text-xs font-semibold text-slate-700 bg-white border border-slate-200 rounded-md shadow-2xs hover:bg-slate-100 hover:text-slate-900 active:scale-95 transition-all cursor-pointer" 
                                            aria-label="{{ __('Go to page :page', ['page' => $page]) }}"
                                        >
                                            {{ $page }}
                                        </button>
                                    @endif
                                </span>
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <button 
                            type="button" 
                            wire:click="nextPage('{{ $paginator->getPageName() }}')" 
                            x-on:click="{{ $scrollIntoViewJsSnippet }}" 
                            dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.after" 
                            class="inline-flex items-center justify-center w-8 h-8 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-md shadow-2xs hover:bg-slate-100 hover:text-slate-900 active:scale-95 transition-all cursor-pointer" 
                            aria-label="{{ __('pagination.next') }}"
                            title="Next Page"
                        >
                            <i class="ri-arrow-right-s-line text-base"></i>
                        </button>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                            <span class="inline-flex items-center justify-center w-8 h-8 text-xs font-semibold text-slate-300 bg-slate-50 border border-slate-200 rounded-md cursor-not-allowed" aria-hidden="true">
                                <i class="ri-arrow-right-s-line text-base"></i>
                            </span>
                        </span>
                    @endif
                </div>
            </div>
        </nav>
    @endif
</div>
