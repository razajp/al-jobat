@props([
    'label' => '',          // Label text for the input
    'name' => '',           // Input name
    'type' => 'text',       // Input type (text, password, etc.)
    'placeholder' => '',    // Placeholder text
    'value' => '',          // Default value
    'required' => false,     // If the input is required
    'disabled' => false,     // If the input is disabled
    'uppercased' => false,     // If the input is uppercased
    'class' => '',     // If the input is uppercased
    'id' => '',
    'list' => '',
    'autocomplete' => 'on',
    'listOptions' => [],
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
        id="{{ $id }}"
        type="{{ $type }}" 
        name="{{ $name }}" 
        value="{{ old($name, $value) }}" 
        placeholder="{{ $placeholder }}"
        autocomplete="{{ $autocomplete }}"
        list="{{ $list }}"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->merge([
            'class' => $class . ' w-full rounded-lg bg-[--h-bg-color] border-gray-600 text-[--text-color] px-3 py-2 border focus:ring-2 focus:ring-primary focus:border-transparent transition-all 0.3s ease-in-out disabled:bg-transparent'
        ]) }}
    />
    
    @if($list != '')
        <datalist id="{{ $list }}">
            @foreach ($listOptions as $option)
                <option value="{{ $option }}"></option>
            @endforeach
        </datalist>
    @endif

    @error($name)
        <div class="text-[--border-error] text-xs mt-1 transition-all 0.3s ease-in-out">{{ $message }}</div>
    @enderror

    <div id="{{ $name }}-error" class="text-[--border-error] text-xs mt-1 hidden transition-all 0.3s ease-in-out"></div>
</div>