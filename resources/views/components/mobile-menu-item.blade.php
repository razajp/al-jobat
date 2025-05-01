@props([
    'href' => '#',                  // Default link
    'title',                        // Menu title (required)
    'dropdown' => [],               // Dropdown items (optional)
    'active' => false,              // Active state (optional)
    'includesDropdown' => false,    // Check if dropdown is included
    'asButton' => false,            // Use button instead of a tag if true
    'onclick' => '',                 // Custom onclick handler for buttons
    'id' => 'id',
])

@php
    // Check if any dropdown item is active
    $activeItem = null;
    $isDropdownActive = false;
    foreach ($dropdown as $item) {
        if (request()->url() === $item['href']) {
            $activeItem = $item['title'];
            $isDropdownActive = true;  // Mark dropdown as active if any item matches
            break;
        }
    }
@endphp

<div class="relative group">
    @if($includesDropdown)
        {{-- Dropdown Menu --}}
        @if (count($dropdown) > 0)
            <button class="dropdown-toggle w-full text-left px-4 py-2 
                {{ $isDropdownActive ? 'text-[var(--text-color)] bg-[var(--h-secondary-bg-color)] font-semibold' : 'text-[var(--secondary-text)] hover:text-[var(--text-color)]' }} 
                rounded-lg transition-all duration-300 ease-in-out flex items-center justify-between cursor-pointer">
                {{ $title }}
                <i class="fas fa-chevron-down transition-transform duration-300"></i>
            </button>
            <div class="dropdown-menu hidden flex-col space-y-2 pl-6 mt-2">
                @foreach ($dropdown as $item)
                    @php
                        $itemActive = request()->url() === $item['href'];
                    @endphp
                    <a href="{{ $item['href'] }}" class="px-4 py-2 block 
                        {{ $itemActive ? 'text-[var(--text-color)] bg-[var(--h-secondary-bg-color)] font-semibold' : 'text-[var(--secondary-text)] hover:text-[var(--text-color)]' }} 
                        rounded-lg transition-all duration-300 ease-in-out">
                        {{ $item['title'] }}
                    </a>
                @endforeach
            </div>
        @endif
    @else
        {{-- Main Menu Item --}}
        @if($asButton)
            {{-- Button Version --}}
            <button id="{{ $id }}" onclick="{{ $onclick }}" class="px-4 py-2 block w-full text-left text-[var(--secondary-text)] hover:text-[var(--text-color)]
                transition-all duration-300 ease-in-out rounded-lg cursor-pointer">
                {{ $title }}
            </button>
        @else
            {{-- Link Version --}}
            <a href="{{ $href }}" class="px-4 py-2 block 
                {{ $active ? 'text-[var(--text-color)] bg-[var(--h-secondary-bg-color)] font-semibold' : 'text-[var(--secondary-text)] hover:text-[var(--text-color)]' }} 
                transition-all duration-300 ease-in-out rounded-lg">
                {{ $title }}
            </a>
        @endif
    @endif
</div>
