@props([
    'title',
    'layout' => 'grid',
    'changeLayoutBtn' => false,
])

{{-- Title bar for the form --}}
<div class="form-title absolute top-0 left-0 w-full p-1.5 flex items-center gap-1.5 z-30">
    <div class="absolute top-0 left-0 w-full h-12 bg-[var(--secondary-bg-color)] blur-sm z-0"></div>
    <div class="text-center bg-[var(--primary-color)] py-1 shadow-lg uppercase font-semibold text-sm rounded-lg grow relative z-40">
        <h4>{{ $title }}</h4>
    </div>
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