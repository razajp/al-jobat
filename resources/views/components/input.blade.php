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
    'max' => "",
    'validateMax' => false,
    'readonly' => false,
    'withImg' => false,
    'imgUrl' => "",
])

@if ($uppercased)
    <style>
        input#{{ $id }} {
            text-transform: uppercase;
        }

        input#{{ $id }}::placeholder {
            text-transform: none;
        }
    </style>
@endif

<div class="form-group relative">
    @if($label)
        <label for="{{ $name }}" class="block font-medium text-[--secondary-text] mb-2">{{ $label }}</label>
    @endif

    <div class="relative">
        <input 
            id="{{ $id }}"
            type="{{ $type }}" 
            name="{{ $name }}" 
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            autocomplete="{{ $autocomplete }}"
            list="{{ $list }}"
            {{ $required ? 'required' : '' }}
            {{ $readonly ? 'readonly' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes->merge([
                'class' => $class . ' w-full rounded-lg bg-[--h-bg-color] border-gray-600 text-[--text-color] px-3 py-2 border focus:ring-2 focus:ring-primary focus:border-transparent transition-all 0.3s ease-in-out disabled:bg-transparent'
            ]) }}
            {{
                $validateMax ? 'max='.$max : ''
            }}
        />
        @if ($withImg)
            <img id="img-{{ $id }}" src="{{ $imgUrl }}" alt="image" class="absolute right-2 top-1/2 transform -translate-y-1/2 w-6 h-6 cursor-pointer object-cover rounded {{ $imgUrl == '' ? 'opacity-0' : '' }}" onclick="openArticleModal()">
        @endif
    </div>
    
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