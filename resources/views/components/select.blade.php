@props([
    'label' => '',          // Label text for the select
    'name' => '',           // Select name
    'options' => [],        // Options array (value => text)
    'value' => '',          // Default selected value
    'showDefault' => 'false',          // Default selected showDefault
    'class' => '',
    'withButton' => false,
    'btnId' => '', 
    'id' => '', 
    'btnText' => '+', 
    'onchange' => '',
])

@php
    $haveOptions = count($options) > 0;
@endphp

<div class="{{ $class }} form-group">
    @if($label)
        <label for="{{ $name }}" class="block font-medium text-[var(--secondary-text)] mb-2">{{ $label }}</label>
    @endif

    <div class="relative flex gap-4">
        <select
            @if (!$haveOptions)
                disabled
            @endif

            id="{{ $id }}" 
            name="{{ $name }}"
            {{ $attributes->merge([
                'class' => 'w-full rounded-lg bg-[var(--h-bg-color)] border-gray-600 text-[var(--text-color)] px-3 py-2 border appearance-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all 0.3s ease-in-out'
            ]) }}

            {{ $onchange ? 'onchange='.$onchange : '' }}
        >
            @if ($showDefault == "true" && $haveOptions)
                <option value="">
                    -- Select {{$label}} --
                </option>
            @endif

            @if ($haveOptions)
                @foreach($options as $optionValue => $optionText)
                    <option data-option='{{ $optionText['data_option'] ?? '' }}' value="{{ $optionValue }}" {{ old($name, $value) == $optionValue ? 'selected' : '' }}>
                        {{ $optionText['text'] }}
                    </option>
                @endforeach
            @else
                <option value="">
                    -- No options available --
                </option>
            @endif
        </select>
        @if ($withButton)
            <button id="{{$btnId}}" type="button" class="bg-[var(--primary-color)] px-4 rounded-lg hover:bg-[var(--h-primary-color)] transition-all 0.3s ease-in-out {{ $btnText === '+' ? 'text-lg font-bold' : 'text-nowrap' }} disabled:opacity-50 disabled:cursor-not-allowed">{{ $btnText }}</button>
        @endif
    </div>

    @error($name)
        <div class="text-[var(--border-error)] text-xs mt-1 transition-all 0.3s ease-in-out">{{ $message }}</div>
    @enderror
    
    <div id="{{ $name }}-error" class="text-[var(--border-error)] text-xs mt-1 hidden transition-all 0.3s ease-in-out"></div>
</div>