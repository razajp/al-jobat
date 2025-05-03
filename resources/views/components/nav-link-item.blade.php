@props([
    'label',
    'icon' => '',
    'includesDropdown' => false,
    'href' => '#',
    'items' => [],
    'activatorTags' => []
])

@if ($includesDropdown)
    <!-- Main Icon Button -->
    <button class="nav-link {{ strtolower($label) }} dropdown-trigger text-[var(--text-color)] p-3 rounded-full hover:bg-[var(--h-bg-color)] transition-all duration-300 ease-in-out w-10 h-10 flex items-center justify-center cursor-pointer relative">
        <i class="{{ $icon }} group-hover:text-[var(--primary-color)]"></i>
        <span class="absolute shadow-xl left-18 top-1/2 transform -translate-y-1/2 bg-[var(--h-secondary-bg-color)] border border-gray-600 text-[var(--text-color)] text-xs rounded-lg px-2 py-1 opacity-0 group-hover:opacity-100 transition-all duration-300 pointer-events-none text-nowrap">
            {{ $label }}
        </span>
    </button>

    <!-- Dropdown Menu -->
    <div class="dropdownMenu text-sm absolute top-0 left-16 hidden group-hover:block border border-gray-600 w-48 bg-[var(--h-secondary-bg-color)] text-[var(--text-color)] shadow-lg rounded-2xl opacity-0 transform scale-95 transition-all duration-300 ease-in-out z-50">
        <ul class="p-2">
            @foreach ($items as $item)
                @if ($item['type'] === 'group')
                    <li class="relative open-dropdown">
                        <div class="flex items-center justify-between px-4 py-2 hover:bg-[var(--h-bg-color)] rounded-lg cursor-pointer transition-all duration-200 ease-in-out">
                            {{ $item['label'] }}
                            <i class="fas fa-chevron-right text-xs ml-2"></i>
                        </div>

                        <!-- Submenu -->
                        <ul class="absolute top-0 left-full -ml-2.5 hidden open-dropdown-hover:block w-48 bg-[var(--h-secondary-bg-color)] border border-gray-600 shadow-lg rounded-2xl scale-90 z-50 p-2">
                            @foreach ($item['children'] as $child)
                                <li>
                                    <a href="{{ $child['href'] }}" class="block px-4 py-2 hover:bg-[var(--h-bg-color)] rounded-lg transition-all duration-200 ease-in-out">
                                        {{ $child['label'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @elseif ($item['type'] === 'link')
                    <li>
                        <a href="{{ $item['href'] }}" class="block px-4 py-2 hover:bg-[var(--h-bg-color)] rounded-lg transition-all duration-200 ease-in-out">
                            {{ $item['label'] }}
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
@else
    <!-- No Dropdown, Just Link -->
    <a href="{{ $href }}"
       class="nav-link {{ strtolower($label) }} text-[var(--text-color)] p-3 rounded-full hover:bg-[var(--h-bg-color)] transition-all duration-300 ease-in-out w-10 h-10 flex items-center justify-center group relative">
        <i class="{{ $icon }} group-hover:text-[var(--primary-color)] transition-all duration-300 ease-in-out"></i>
        <span class="absolute shadow-xl left-16 top-1/2 transform -translate-y-1/2 bg-[var(--h-secondary-bg-color)] border border-gray-600 text-[var(--text-color)] text-xs rounded-lg px-2 py-1 opacity-0 group-hover:opacity-100 transition-all duration-300 pointer-events-none">
            {{ $label }}
        </span>
    </a>
@endif

<!-- Highlight Active Menu -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const url = window.location.href.toLowerCase();
        const label = "{{ strtolower($label) }}";

        if (url.includes(label)) {
            document.querySelector(".nav-link." + label)?.classList.add("active");
        }

        @if(isset($activatorTags) && is_array($activatorTags))
            @foreach($activatorTags as $tag)
                if (url.includes("{{ strtolower($tag) }}")) {
                    document.querySelector(".nav-link.{{ strtolower($label) }}")?.classList.add("active");
                }
            @endforeach
        @endif
    });
</script>