<div class="pt-3 md:pt-5 print-hide">
    <div class="row">
        <div class="col-lg-12">
            <div class="mt-0 mb-0 py-1 md:py-1.5 px-4 md:px-6 bg-white card_style rounded-bottom-flat transition-all duration-300 shadow-sm border-b-0">
                <div class="flex flex-row items-center justify-between gap-3 "> 
                    <!-- Left Section: Title & Breadcrumb Links -->
                    <div class="space-y-0.5 md:space-y-1.5 min-w-0">
                        <h1 class="text-[13px] md:text-lg font-black text-slate-800 tracking-tight m-0 leading-tight truncate">
                            @yield('page-title')
                        </h1>
                        
                        <nav class="flex overflow-x-auto custom-scrollbar pb-1 md:pb-2" aria-label="Breadcrumb">
                            <ol class="flex items-center space-x-2 text-[8px] md:text-xs font-bold tracking-widest text-slate-400 whitespace-nowrap">
                                <li class="hidden sm:flex items-center">
                                    <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors flex items-center gap-1.5">
                                        <i class="feather icon-home"></i> {{ __('Home') }}
                                    </a>
                                </li>
                                
                                @php
                                    $section_title = explode('-', $__env->yieldContent('section-title'));
                                @endphp
                                
                                @if ($section_title)
                                    @foreach ($section_title as $title)
                                        @if (trim($title) != '')
                                            <li class="hidden sm:flex items-center space-x-2">
                                                <span class="text-slate-300 font-light translate-y-[-1px]">/</span>
                                                <a href="javascript:void(0);" class="hover:text-primary transition-colors hover:cursor-default">
                                                    {{ __(str_replace('_', ' ', $title)) }}
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                @endif
                                
                                <li class="flex items-center space-x-2">
                                    <span class="sm:hidden text-slate-300 font-light translate-y-[-0.5px]">/</span>
                                    <span class="text-primary font-black">@yield('page-title')</span>
                                </li>
                            </ol>
                        </nav>
                    </div>

                    <!-- Right Section: Action Buttons -->
                    <div class="flex items-center justify-end shrink-0 pt-0 breadcrumb-action-area">
                        <div class="flex flex-wrap gap-2 md:gap-3">
                            @yield('action-button')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
