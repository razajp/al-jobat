<!-- Main Button -->

@if ($includesDropdown == "true")
    <button class="nav-link {{ strtolower($label) }} dropdown-trigger text-[var(--text-color)] p-3 rounded-full group-hover:bg-[var(--h-bg-color)] transition-all 0.3s ease-in-out w-10 h-10 flex items-center justify-center cursor-pointer">
        <i class="{{ $icon }} group-hover:text-[var(--primary-color)]"></i>
        <span class="absolute shadow-xl left-16 top-1/2 transform -translate-y-1/2 bg-[var(--h-secondary-bg-color)] border border-gray-600 text-[var(--text-color)] text-xs rounded-lg px-2 py-1 opacity-0 group-hover:opacity-100 transition-all 0.3s pointer-events-none text-nowrap">
            {{ $label }}
        </span>
    </button>

    <!-- Dropdown Menu -->
    <div class="dropdownMenu text-sm absolute top-0 left-16 hidden border border-gray-600 w-48 bg-[var(--h-secondary-bg-color)] text-[var(--text-color)] shadow-lg rounded-2xl opacity-0 transform scale-95 transition-all 0.3s ease-in-out z-50">
        <ul class="p-2">
            @foreach ($items as $item)
                <li>
                    @if ($item['type'] === 'link')
                        <a href="{{ $item['href'] }}" class="block px-4 py-2 hover:bg-[var(--h-bg-color)] rounded-lg transition-all duration-200 ease-in-out">
                            {{ $item['label'] }}
                        </a>
                    @elseif ($item['type'] === 'button')
                        <button onclick="{{ $item['onclick'] ?? '' }}" class="block w-full text-left px-4 py-2 {{ $item['class'] ?? '' }} rounded-lg transition-all duration-200 ease-in-out">
                            {{ $item['label'] }}
                        </button>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
@else
    <a href="{{ $href }}"
    class="nav-link {{ strtolower($label) }} text-[var(--text-color)] p-3 rounded-full hover:bg-[var(--h-bg-color)] transition-all 0.3s ease-in-out w-10 h-10 flex items-center justify-center group relative">
        <i class="{{ $icon }} group-hover:text-[var(--primary-color)] transition-all 0.3s ease-in-out"></i>
        <span class="absolute shadow-xl left-16 top-1/2 transform -translate-y-1/2 bg-[var(--h-secondary-bg-color)] border border-gray-600 text-[var(--text-color)] text-xs rounded-lg px-2 py-1 opacity-0 group-hover:opacity-100 transition-all 0.3s pointer-events-none">
            {{ $label }}
        </span>
    </a>
@endif

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const url = window.location.href.toLowerCase();
        const label = "{{ strtolower($label) }}";
        
        // Check if the label is present in the URL
        if (url.includes(label)) {
            document.querySelector(".nav-link." + label)?.classList.add("active");
        }

        // Check activatorTags if passed
        @if(isset($activatorTags) && is_array($activatorTags))
            @foreach($activatorTags as $tag)
                if (url.includes("{{ strtolower($tag) }}")) {
                    document.querySelector(".nav-link.{{ strtolower($label) }}")?.classList.add("active");
                }
            @endforeach
        @endif
    });
</script>