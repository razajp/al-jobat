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
    'dataClearable' => false,
    'parentGrow' => false,
    'dataValidate' => '',
    'dataClean' => '',
    'withCheckbox' => false,
    'checkBoxes' => [],
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

<div class="form-group relative {{$parentGrow ? "grow" : ""}}">
    @if($label)
        <label for="{{ $name }}" class="block font-medium text-[var(--secondary-text)] mb-2">{{ $label }}{{ !$required && !$required && !$readonly && !$disabled ? ' (optional)' : '' }}</label>
    @endif

    <div class="relative flex gap-4">
        @if ($withCheckbox)
            <div
                {{ $attributes->merge([
                    'class' => $class . ' w-full rounded-lg ' .
                        ($errors->has($name) ? 'border-[var(--border-error)]' : 'border-gray-600') .
                        ' text-[var(--text-color)] px-1 py-1 ' .
                        ' border focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300 ease-in-out disabled:bg-transparent placeholder:capitalize'
                ]) }}
            >
                <div class="checkboxes_container grid gap-1 grid-cols-4">
                    @foreach ($checkBoxes as $checkbox)
                        <label class="flex items-center gap-2 cursor-pointer rounded-md border border-[var(--h-bg-color)] bg-[var(--h-bg-color)] px-2 py-[0.1875rem] shadow-sm transition hover:shadow-md hover:border-primary">
                            <input
                                type="checkbox"
                                onchange="toggleThisCheckbox(this)"
                                data-checkbox="{{ $checkbox }}"
                                class="checkbox appearance-none bg-[var(--secondary-bg-color)] w-4 h-4 border border-gray-600 rounded-sm checked:bg-[var(--primary-color)] transition"
                            />
                            <span class="text-sm font-medium text-[var(--secondary-text)]">
                                {{ ucfirst($checkbox) }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
        @else
            <input
                id="{{ $id }}"
                type="{{ $type }}"
                name="{{ $name }}"
                @if ($value != '')
                    value="{{ old($name, $value) }}"
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
                {{ $dataValidate ? 'data-validate='.$dataValidate : '' }}
                {{ $dataClean ? 'data-clean='.$dataClean : '' }}
                {{ $validateMax ? 'max='.$max : '' }}
                {{ $validateMin ? 'min='.$min : '' }}
                {{ $onchange ? 'onchange='.$onchange : '' }}
                {!! $oninput ? 'oninput="'.$oninput.'"' : '' !!}
                {!! $dataFilterPath ? 'data-filter-path="' . $dataFilterPath . '"' : '' !!}
                @if ($dataClearable) data-clearable @endif
            />
        @endif

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
                {!! $dataFilterPath ? 'data-clearable="' . $dataFilterPath . '"' : '' !!}
                {!! $dataClearable ? 'data-clearable="' . $dataClearable . '"' : '' !!}
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
        <div class="text-[var(--border-error)] text-xs mt-1 transition-all duration-300 ease-in-out">{{ $message }}</div>
    @enderror

    <div id="{{ $name }}-error" class="text-[var(--border-error)] text-xs mt-1 hidden transition-all duration-300 ease-in-out"></div>
</div>
