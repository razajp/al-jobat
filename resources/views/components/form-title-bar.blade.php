@props([
    'title',
    'layout' => 'grid',
    'changeLayoutBtn' => false,
    'resetSortBtn' => false,
    'printBtn' => false,
])

{{-- Title bar for the form --}}
<div class="form-title absolute top-0 left-0 w-full p-1.5 flex items-center gap-1.5 z-30">
    @if ($resetSortBtn && $layout != 'grid')
        <div class="text-center bg-[var(--primary-color)] h-7 shadow-lg uppercase font-semibold text-sm rounded-lg relative z-40">
            <div class="buttons top-0 right-4.5 text-sm h-full flex items-center px-2">
                <div class="relative group flex items-center justify-between" onclick="resetSort()">
                    <button type="button" class="group cursor-pointer" id="resetSortBtn">
                        <svg
                            version="1.1"
                            xmlns="http://www.w3.org/2000/svg"
                            xmlns:xlink="http://www.w3.org/1999/xlink"
                            class="size-4.5"
                            viewBox="0 0 187.88 155.52"
                            style="enable-background: new 0 0 187.88 155.52"
                            xml:space="preserve"
                            >
                            <g id="Layer_1">
                                <g>
                                <path
                                    d="M91.15,37.64c-19.32,0-38.64,0.01-57.96,0c-7.37,0-11.69-3.74-11.62-10c0.07-6.08,4.31-9.77,11.35-9.77
                                        c39.14-0.01,78.28-0.02,117.42,0c5.98,0,9.91,3.09,10.78,8.26c0.8,4.8-1.91,9.46-6.62,10.96c-1.53,0.49-3.26,0.53-4.89,0.53
                                        C130.12,37.66,110.64,37.64,91.15,37.64z"
                                />
                                <path
                                    d="M108,87.64c-12.19,0.02-24.38,0-36.57,0c-12.99,0-25.98,0.03-38.96-0.01c-6.66-0.02-10.8-3.76-10.9-9.71
                                        c-0.11-6.1,4.18-10.05,11.06-10.05c19.17-0.02,38.33-0.03,57.5-0.02L108,87.64z"
                                />
                                <path
                                    d="M51.19,137.64c-6.49,0-12.98,0.07-19.47-0.02c-6.09-0.09-10.19-4.19-10.15-9.94c0.04-5.72,4.17-9.77,10.3-9.8
                                        c12.98-0.07,25.96-0.07,38.95,0c6.16,0.03,10.3,3.99,10.4,9.7c0.1,5.93-4.1,9.99-10.55,10.05
                                        C64.17,137.69,57.68,137.64,51.19,137.64z"
                                />
                                <path
                                    d="M164.33,117.43c2.85,3.15,2.6,8-0.55,10.85c-1.47,1.33-3.31,1.98-5.14,1.98c-2.1,0-4.19-0.86-5.71-2.53l-23.6-26.12
                                        l-23.6,26.12c-1.52,1.67-3.61,2.53-5.7,2.53c-1.84,0-3.68-0.65-5.15-1.98c-3.14-2.85-3.4-7.7-0.55-10.85l24.65-27.28L94.33,62.86
                                        c-2.85-3.15-2.6-8,0.55-10.85c3.15-2.85,8-2.6,10.85,0.55l23.6,26.12l23.6-26.12c2.85-3.15,7.7-3.4,10.85-0.55s3.4,7.7,0.55,10.85
                                        l-24.65,27.29L164.33,117.43z"
                                />
                                </g>
                            </g>
                        </svg>
                        <span class="absolute shadow-xl -left-2 top-7.5 z-10 bg-[var(--h-secondary-bg-color)] border border-gray-600 text-[var(--text-color)] text-xs rounded-lg px-2.5 py-1 opacity-0 group-hover:opacity-100 transition-all duration-300 pointer-events-none text-nowrap">Reset Sort</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
    <div class="absolute top-0 left-0 w-full h-12 bg-[var(--secondary-bg-color)] blur-sm z-0"></div>
    <div id="page-title" class="text-center bg-[var(--primary-color)] py-1 shadow-lg uppercase font-semibold text-sm rounded-lg grow relative z-40">
        <h4>{{ $title }}</h4>
    </div>
    @if ($printBtn && $layout != 'grid')
        <div class="text-center bg-[var(--primary-color)] h-7 shadow-lg uppercase font-semibold text-sm rounded-lg relative z-40">
            <div class="buttons top-0 right-4.5 text-sm h-full flex items-center px-2">
                <div class="relative group flex items-center justify-between" onclick="printPage()">
                    <button type="submit" class="group cursor-pointer" id="printBtn">
                        <i class='fas fa-print text-white text-'></i>
                        <span class="absolute shadow-xl -right-2 top-7.5 z-10 bg-[var(--h-secondary-bg-color)] border border-gray-600 text-[var(--text-color)] text-xs rounded-lg px-2.5 py-1 opacity-0 group-hover:opacity-100 transition-all duration-300 pointer-events-none text-nowrap">Print</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
    @if ($changeLayoutBtn)
        <div class="text-center bg-[var(--primary-color)] h-7 shadow-lg uppercase font-semibold text-sm rounded-lg relative z-40">
            <div class="buttons top-0 right-4.5 text-sm h-full flex items-center px-2">
                <div class="relative group flex items-center justify-between" onclick="changeLayout()">
                    @if ($layout == 'grid')
                        <button type="submit" class="group cursor-pointer" id="changeLayoutBtn">
                            <i class='fas fa-list-ul text-white'></i>
                            <span class="absolute shadow-xl -right-2 top-7.5 z-10 bg-[var(--h-secondary-bg-color)] border border-gray-600 text-[var(--text-color)] text-xs rounded-lg px-2.5 py-1 opacity-0 group-hover:opacity-100 transition-all duration-300 pointer-events-none text-nowrap">List</span>
                        </button>
                    @else
                        <button type="submit" class="group cursor-pointer" id="changeLayoutBtn">
                            <i class='fas fa-grip text-white'></i>
                            <span class="absolute shadow-xl -right-2 top-7.5 z-10 bg-[var(--h-secondary-bg-color)] border border-gray-600 text-[var(--text-color)] text-xs rounded-lg px-2.5 py-1 opacity-0 group-hover:opacity-100 transition-all duration-300 pointer-events-none text-nowrap">Grid</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
