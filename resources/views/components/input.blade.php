@props([
    'label' => '',          // Label text for the input
    'name' => '',           // Input name
    'type' => 'text',       // Input type (text, password, etc.)
    'placeholder' => '',    // Placeholder text
    'value' => '',          // Default value
    'required' => false,     // If the input is required
    'disabled' => false,     // If the input is disabled
    'uppercased' => false,     // If the input is uppercased
    'capitalized' => false,     // If the input is uppercased
    'class' => '',     // If the input is uppercased
    'id' => '',
    'list' => '',
    'autocomplete' => 'on',
    'listOptions' => [],
    'max' => '',
    'validateMax' => false,
    'min' => '',
    'validateMin' => false,
    'readonly' => false,
    'withImg' => false,
    'imgUrl' => '',
    'withButton' => false,
    'btnId' => '',
    'btnText' => "+",
    'btnClass' => '',
    'onchange' => '',
    'oninput' => '',
    'minlength' => '',
    'dualInput' => '',
    'type2' => '',
    'id2' => '',
    'dataFilterPath' => '',
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

@if ($capitalized)
    <style>
        input#{{ $id }} {
            text-transform: capitalize;
        }

        input#{{ $id }}::placeholder {
            text-transform: none;
        }
    </style>
@endif

@if ($type == 'username')
    @php
        $type = 'text';
        $oninput = 'formatUsername(this)';
        $minlength = '6';
    @endphp
    
<script>
    function formatUsername(input) {
        input.value = input.value.toLowerCase().replace(/[^a-z0-9]/g, '');
    }

    function validateUsername() {
        const username = document.getElementById('username').value;

        if (username.length < 6) {
            alert('Username must be at least 6 characters long.');
            return false;
        }

        return true;
    }
</script>
@endif

<div class="form-group relative">
    @if($label)
        <label for="{{ $name }}" class="block font-medium text-[var(--secondary-text)] mb-2">{{ $label }}{{ $required ? ' *' : '' }}</label>
    @endif

    <div class="relative flex gap-4">
        <input 
            id="{{ $id }}"
            type="{{ $type }}" 
            name="{{ $name }}" 
            @if ($value != '')
                value="{{ $value }}"
            @endif
            placeholder="{{ $placeholder }}"
            autocomplete="{{ $autocomplete }}"
            list="{{ $list }}"
            {{ $required ? 'required' : '' }}
            {{ $readonly ? 'readonly' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes->merge([
                'class' => $class . ' w-full rounded-lg bg-[var(--h-bg-color)] ' .
                    ($errors->has($name) ? 'border-[var(--border-error)]' : 'border-gray-600') .
                    ' text-[var(--text-color)] px-3 ' . 
                    ($type == 'date' ? 'py-[7px]' : 'py-2') .
                    ' border focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300 ease-in-out disabled:bg-transparent placeholder:capitalize'
            ]) }}
            {{ $validateMax ? 'max='.$max : '' }}
            {{ $validateMin ? 'min='.$min : '' }}
            {{ $onchange ? 'onchange='.$onchange : '' }}
            {!! $oninput ? 'oninput="'.$oninput.'"' : '' !!}
            {!! $dataFilterPath ? 'data-filter-path="' . $dataFilterPath . '"' : '' !!}
        />
        @if ($dualInput)
            <input 
                id="{{ $id2 }}"
                type="{{ $type2 }}"
                {{ $attributes->merge([
                    'class' => $class . ' w-full rounded-lg bg-[var(--h-bg-color)] ' .
                        ($errors->has($name) ? 'border-[var(--border-error)]' : 'border-gray-600') .
                        ' text-[var(--text-color)] px-3 ' . 
                        ($type == 'date' ? 'py-[7px]' : 'py-2') .
                        ' border focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300 ease-in-out disabled:bg-transparent placeholder:capitalize'
                ]) }}
                {!! $oninput ? 'oninput="'.$oninput.'"' : '' !!}
                {!! $dataFilterPath ? 'data-filter-path="' . $dataFilterPath . '"' : '' !!}
            />
        @endif
        @if ($withImg)
            <img id="img-{{ $id }}" src="{{ $imgUrl }}" alt="image" class="absolute right-2 top-1/2 transform -translate-y-1/2 w-6 h-6 cursor-pointer object-cover rounded {{ $imgUrl == '' ? 'opacity-0' : '' }}" onclick="openArticleModal()">
        @endif
        @if ($withButton)
            <button id="{{$btnId}}" type="button" class="{{ $btnClass }} bg-[var(--primary-color)] px-4 rounded-lg hover:bg-[var(--h-primary-color)] transition-all duration-300 ease-in-out cursor-pointer {{ $btnText === '+' ? 'text-lg font-bold' : 'text-nowrap' }} disabled:opacity-50 disabled:cursor-not-allowed">{!! $btnText !!}</button>
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
        <div class="absolute -bottom-5 left-1 text-[var(--border-error)] text-xs mt-1 transition-all duration-300 ease-in-out">{{ $message }}</div>
    @enderror

    <div id="{{ $name }}-error" class="absolute -bottom-5 left-1 text-[var(--border-error)] text-xs mt-1 hidden transition-all duration-300 ease-in-out"></div>
</div>