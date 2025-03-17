@props([
    'steps' => [],          // Array of steps (labels)
    'currentStep' => 0,     // Current active step
    'primaryColor' => '--primary-color',          // Primary color variable
    'hPrimaryColor' => '--h-primary-color',       // Hover primary color variable
    'bgColor' => '--h-bg-color',                 // Background color variable
    'hBgColor' => '--secondary-bg-color',       // Hover primary color variable
])

<!-- Step Indicators -->
<div class="flex justify-between mb-2 progress-indicators">
    @foreach ($steps as $index => $step)
        <p
            class="text-xs inline-block font-medium tracking-wide py-1 px-3 capitalize rounded-md text-[--text-color] transition-all 0.3s ease-in-out cursor-pointer
            {{ $currentStep === $index + 1 ? 'bg-[' . $primaryColor . '] hover:bg-[' . $hPrimaryColor . ']' : 'bg-[' . $bgColor . '] hover:bg-[' . $hBgColor . ']' }}"
            id="step{{ $index + 1 }}-indicator"
            onclick="gotoStep({{ $index + 1 }})">
            {{ $step }}
        </p>
    @endforeach
</div>

<!-- Progress Bar -->
<div class="flex h-2 mb-4 overflow-hidden bg-[{{ $bgColor }}] rounded-full">
    <div class="transition-all duration-500 ease-in-out bg-[{{ $primaryColor }}]"
        id="progress-bar"
        style="width: calc(({{ $currentStep }} / {{ count($steps) }}) * 100%)">
    </div>
</div>
