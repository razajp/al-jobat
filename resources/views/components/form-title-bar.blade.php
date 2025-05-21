@props([
    'title',
    'layout' => 'grid',
    'changeLayoutBtn' => false,
])

{{-- Title bar for the form --}}
<div class="form-title absolute top-0 left-0 w-full p-1.5">
    <div class="text-center bg-[var(--primary-color)] py-1 shadow-lg uppercase font-semibold text-sm rounded-lg">
        <h4>{{ $title }}</h4>

        @if ($changeLayoutBtn)
            <div class="buttons absolute top-0 right-4.5 text-sm h-full flex items-center">
                <div class="relative group">
                    <form method="POST" action="{{ route('change-data-layout') }}">
                        @csrf
                        <input type="hidden" name="layout" value="{{ $layout }}">
                        @if ($layout == 'grid')
                            <button type="submit" class="group cursor-pointer">
                                <i class='fas fa-list-ul text-white'></i>
                                <span
                                    class="absolute shadow-md text-nowrap border border-gray-600 z-10 -right-1 top-8 bg-[var(--h-secondary-bg-color)] text-[var(--text-color)] text-[12px] rounded px-3 py-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">List</span>
                            </button>
                        @else
                            <button type="submit" class="group cursor-pointer">
                                <i class='fas fa-grip text-white'></i>
                                <span
                                    class="absolute shadow-md text-nowrap border border-gray-600 z-10 -right-1 top-8 bg-[var(--h-secondary-bg-color)] text-[var(--text-color)] text-[12px] rounded px-3 py-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">Grid</span>
                            </button>
                        @endif
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
