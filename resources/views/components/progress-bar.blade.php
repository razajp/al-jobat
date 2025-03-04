@props([
    'steps' => [],          // Array of steps (labels)
    'currentStep' => 1,     // Current active step
    'primaryColor' => '--primary-color',          // Primary color variable
    'hPrimaryColor' => '--h-primary-color',       // Hover primary color variable
    'bgColor' => '--h-bg-color',                 // Background color variable
    'hBgColor' => '--secondary-bg-color',       // Hover primary color variable
])

<!-- Step Indicators -->
<div class="flex justify-between mb-2 progress-indicators">
    @foreach ($steps as $index => $step)
        <span
            class="text-xs font-semibold inline-block py-1 px-3 uppercase rounded-md text-[--text-color] transition-all 0.3s ease-in-out cursor-pointer
            {{ $currentStep === $index + 1 ? 'bg-[' . $primaryColor . ']' : 'bg-[' . $bgColor . ']' }}"
            id="step{{ $index + 1 }}-indicator"
            onclick="gotoStep({{ $index + 1 }})"
            style="
                @if($currentStep === $index + 1)
                    --bg-color: var({{ $primaryColor }});
                    --hover-color: var({{ $hPrimaryColor }});
                @else
                    --bg-color: var({{ $bgColor }});
                    --hover-color: var({{ $hBgColor }});
                @endif
                background-color: var(--bg-color);
            "
            onmouseover="this.style.backgroundColor = 'var(--hover-color)'"
            onmouseout="this.style.backgroundColor = 'var(--bg-color)'">
            {{ $step }}
        </span>
    @endforeach
</div>

<!-- Progress Bar -->
<div class="flex h-2 mb-4 overflow-hidden bg-[{{ $bgColor }}] rounded-full">
    <div class="transition-all duration-500 ease-in-out bg-[{{ $primaryColor }}]"
        id="progress-bar"
        style="width: calc(({{ $currentStep }} / {{ count($steps) }}) * 100%)">
    </div>
</div>
