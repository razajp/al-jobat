@props([
    'label' => '',          // Label text for the select
    'name' => '',           // Select name
    'options' => [],        // Options array (value => text)
    'value' => '',          // Default selected value
    'showDefault' => 'false',          // Default selected showDefault
])

@php
    $haveOptions = count($options) > 0;
@endphp

<div class="form-group">
    @if($label)
        <label for="{{ $name }}" class="block font-medium text-[--secondary-text] mb-2">{{ $label }}</label>
    @endif

    <div class="relative">
        <select
            @if (!$haveOptions)
                disabled
            @endif

            id="{{ $name }}" 
            name="{{ $name }}"
            {{ $attributes->merge([
                'class' => 'w-full rounded-lg bg-[--h-bg-color] border-gray-600 text-[--text-color] px-3 py-2 border appearance-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300 ease-in-out'
            ]) }}
        >
            @if ($showDefault == "true" && $haveOptions)
                <option value="">
                    -- Select {{$label}} --
                </option>
            @endif

            @if ($haveOptions)
                @foreach($options as $optionValue => $optionText)
                    <option value="{{ $optionValue }}" {{ old($name, $value) == $optionValue ? 'selected' : '' }}>
                        {{ $optionText }}
                    </option>
                @endforeach
            @else
                <option value="">
                    -- No options available --
                </option>
            @endif
        </select>
    </div>

    @error($name)
        <div class="text-[--border-error] text-xs mt-1 transition-all 0.3s ease-in-out">{{ $message }}</div>
    @enderror
    
    <div id="{{ $name }}-error" class="text-[--border-error] text-xs mt-1 hidden transition-all 0.3s ease-in-out"></div>
</div>