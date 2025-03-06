@props([
    'label' => '',          // Label text for the input
    'name' => '',           // Input name
    'type' => 'text',       // Input type (text, password, etc.)
    'placeholder' => '',    // Placeholder text
    'value' => '',          // Default value
    'required' => false,     // If the input is required
    'uppercased' => false     // If the input is uppercased
])

@if ($uppercased)
    <style>
        input#{{ $name }} {
            text-transform: uppercase;
        }

        input#{{ $name }}::placeholder {
            text-transform: none;
        }
    </style>
@endif

<div class="form-group">
    @if($label)
        <label for="{{ $name }}" class="block font-medium text-[--secondary-text] mb-2">{{ $label }}</label>
    @endif

    <input 
        id="{{ $name }}"
        type="{{ $type }}" 
        name="{{ $name }}" 
        value="{{ old($name, $value) }}" 
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge([
            'class' => 'w-full rounded-lg bg-[--h-bg-color] border-gray-600 text-[--text-color] px-3 py-2 border focus:ring-2 focus:ring-primary focus:border-transparent transition-all 0.3s ease-in-out'
        ]) }}
    />

    @error($name)
        <div class="text-[--border-error] text-xs mt-1 transition-all 0.3s ease-in-out">{{ $message }}</div>
    @enderror

    <div id="{{ $name }}-error" class="text-[--border-error] text-xs mt-1 hidden transition-all 0.3s ease-in-out"></div>
</div>