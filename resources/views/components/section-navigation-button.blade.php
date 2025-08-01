@props([
    'link' => '',
    'title' => '',
    'icon' => '',
    'direction' => 'left',
    'id' => '',
])
@if ($link != '')
    <a href="{{ $link }}" id="{{ $id }}" class="border border-[var(--glass-border-color)]/10 group bg-[var(--glass-border-color)]/5 backdrop-blur-md rounded-xl cursor-pointer flex items-center justify-end p-1 overflow-hidden {{ $direction == 'left' ? 'hover:pl-3' : 'hover:pr-3' }} transition-all duration-300 ease-in-out shadow-md pointer-events-auto">
        @if ($direction == 'left')
            <span class="inline-block max-w-0 opacity-0 overflow-hidden whitespace-nowrap transition-all duration-300 ease-in-out group-hover:opacity-100 group-hover:max-w-[300px] {{ $direction == 'left' ? 'group-hover:mr-2' : 'group-hover:ml-2' }}">
                {{ $title }}
            </span>
        @endif
        <div class="flex items-center justify-center bg-[var(--bg-color)] border border-[var(--glass-border-color)]/20 rounded-lg p-2">
            <div class="size-3 transition-all duration-300 ease-in-out group-hover:size-2.5 relative">
                <i class="fas {{ $icon }} text-xs absolute top-1/2 left-1/2 -translate-1/2"></i>
            </div>
        </div>
        @if ($direction != 'left')
            <span class="inline-block max-w-0 opacity-0 overflow-hidden whitespace-nowrap transition-all duration-300 ease-in-out group-hover:opacity-100 group-hover:max-w-[300px] {{ $direction == 'left' ? 'group-hover:mr-2' : 'group-hover:ml-2' }}">
                {{ $title }}
            </span>
        @endif
    </a>
@else
    <div id="{{ $id }}" class="border border-[var(--glass-border-color)]/10 group bg-[var(--glass-border-color)]/5 backdrop-blur-md rounded-xl cursor-pointer flex items-center justify-end p-1 overflow-hidden h-auto {{ $direction == 'left' ? 'hover:pl-3' : 'hover:pr-3' }} transition-all duration-300 ease-in-out shadow-md pointer-events-auto">
        @if ($direction == 'left')
            <span class="inline-block max-w-0 opacity-0 overflow-hidden whitespace-nowrap transition-all duration-300 ease-in-out group-hover:opacity-100 group-hover:max-w-[300px] {{ $direction == 'left' ? 'group-hover:mr-2' : 'group-hover:ml-2' }}">
                {{ $title }}
            </span>
        @endif
        <div class="flex items-center justify-center bg-[var(--bg-color)] border border-[var(--glass-border-color)]/20 rounded-lg p-2">
            <div class="size-3 transition-all duration-300 ease-in-out group-hover:size-2.5 relative">
                <i class="fas {{ $icon }} text-xs absolute top-1/2 left-1/2 -translate-1/2"></i>
            </div>
        </div>
        @if ($direction != 'left')
            <span class="inline-block max-w-0 opacity-0 overflow-hidden whitespace-nowrap transition-all duration-300 ease-in-out group-hover:opacity-100 group-hover:max-w-[300px] {{ $direction == 'left' ? 'group-hover:mr-2' : 'group-hover:ml-2' }}">
                {{ $title }}
            </span>
        @endif
    </div>
@endif