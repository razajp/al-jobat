@props([
    'steps' => [],          // Array of steps (labels)
    'currentStep' => 0,     // Current active step
    'primaryColor' => 'var(--primary-color)',          // Primary color variable
    'hPrimaryColor' => 'var(--h-primary-color)',       // Hover primary color variable
    'bgColor' => 'var(--h-bg-color)',                 // Background color variable
    'hBgColor' => 'var(--secondary-bg-color)',       // Hover primary color variable
])

<!-- Step Indicators -->
<div class="flex justify-between mb-2 progress-indicators">
    @foreach ($steps as $index => $step)
        <p
            class="text-xs inline-block font-medium tracking-wide py-1.5 px-3.5 capitalize rounded-lg text-[var(--text-color)] transition-all duration-300 ease-in-out cursor-pointer
            {{ $currentStep === $index + 1 ? 'bg-[' . $primaryColor . '] hover:bg-[' . $hPrimaryColor . ']' : 'bg-[' . $bgColor . '] hover:bg-[' . $hBgColor . ']' }}"
            id="step{{ $index + 1 }}-indicator leading-none"
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
