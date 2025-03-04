@props([
    'label' => '',          // Label text for the select
    'name' => '',           // Select name
    'options' => [],        // Options array (value => text)
    'value' => '',          // Default selected value
])

<div class="form-group">
    @if($label)
        <label for="{{ $name }}" class="block font-medium text-[--secondary-text] mb-2">{{ $label }}</label>
    @endif

    <div class="relative">
        <select 
            id="{{ $name }}" 
            name="{{ $name }}"
            {{ $attributes->merge([
                'class' => 'w-full rounded-lg bg-[--h-bg-color] border-gray-600 text-[--text-color] px-3 py-2 border appearance-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300 ease-in-out'
            ]) }}
        >
            @foreach($options as $optionValue => $optionText)
                <option value="{{ $optionValue }}" {{ old($name, $value) == $optionValue ? 'selected' : '' }}>
                    {{ $optionText }}
                </option>
            @endforeach
        </select>
    </div>

    @error($name)
        <div class="text-[--border-error] text-xs mt-1">{{ $message }}</div>
    @enderror
</div>