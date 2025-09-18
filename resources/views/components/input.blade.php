@props([
    'label' => '',
    'name' => '',
    'type' => 'text',
    'placeholder' => '',
    'value' => '',
    'required' => false,
    'disabled' => false,
    'uppercased' => false,
    'capitalized' => false,
    'class' => '',
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
    input#{{ $id }} { text-transform: uppercase; }
    input#{{ $id }}::placeholder { text-transform: none; }
</style>
@endif

@if ($capitalized)
<style>
    input#{{ $id }} { text-transform: capitalize; }
    input#{{ $id }}::placeholder { text-transform: none; }
</style>
@endif

<div class="form-group relative {{$parentGrow ? 'grow' : ''}}">
    @if($label)
        <label for="{{ $name }}" class="block font-medium text-[var(--secondary-text)] mb-2">
            {{ $label }}{{ !$required && !$readonly && !$disabled ? ' (optional)' : '' }}
        </label>
    @endif

    <div class="relative flex gap-4">

        {{-- Checkbox group --}}
        @if ($withCheckbox)
            <div {{ $attributes->merge([
                'class' => $class . ' w-full rounded-lg ' .
                    ($errors->has($name) ? 'border-[var(--border-error)]' : 'border-gray-600') .
                    ' text-[var(--text-color)] px-1 py-1 border focus:ring-2 focus:ring-primary transition-all duration-300 ease-in-out disabled:bg-transparent placeholder:capitalize'
            ]) }}>
                <div class="checkboxes_container grid gap-1 grid-cols-4">
                    @foreach ($checkBoxes as $checkbox)
                        <label class="flex items-center gap-2 cursor-pointer rounded-md border bg-[var(--h-bg-color)] px-2 py-[0.1875rem] shadow-sm transition hover:shadow-md hover:border-primary">
                            <input type="checkbox"
                                   onchange="toggleThisCheckbox(this)"
                                   data-checkbox="{{ $checkbox }}"
                                   class="checkbox appearance-none bg-[var(--secondary-bg-color)] w-4 h-4 border border-gray-600 rounded-sm checked:bg-[var(--primary-color)] transition"/>
                            <span class="text-sm font-medium text-[var(--secondary-text)] capitalize">{{ ucfirst($checkbox) }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        @else
            {{-- Main input --}}
            @if($type === 'date')
                {{-- Visible Flatpickr input --}}
                <input
                    id="{{ $id }}_flatpickr"
                    type="text"
                    placeholder="{{ $placeholder }}"
                    autocomplete="{{ $autocomplete }}"
                    data-hidden-id="{{ $id }}"
                    {{ $disabled ? 'disabled' : '' }}
                    {{ $required ? 'required' : '' }}
                    {{ $readonly ? 'readonly' : '' }}
                    class="{{ $class }} w-full rounded-lg bg-[var(--h-bg-color)] {{ $errors->has($name) ? 'border-[var(--border-error)]' : 'border-gray-600' }} text-[var(--text-color)] px-3 py-[7px] border focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300 ease-in-out disabled:bg-transparent placeholder:capitalize"
                    }}"
                    {{ $onchange ? 'onchange='.$onchange : '' }}
                />
                {{-- Hidden input for backend --}}
                <input type="hidden" id="{{ $id }}" name="{{ $name }}" value="{{ old($name, $value) }}"/>
            @else
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
                    {{ $validateMax ? 'max='.$max : '' }}
                    {{ $validateMin ? 'min='.$min : '' }}
                    {{ $onchange ? 'onchange='.$onchange : '' }}
                    {!! $oninput ? 'oninput="'.$oninput.'"' : '' !!}
                    {{ $dataValidate ? 'data-validate='.$dataValidate : '' }}
                    {{ $dataClean ? 'data-clean='.$dataClean : '' }}
                    {{ $dataClearable ? 'data-clearable' : '' }}
                    class="{{ $class }} w-full rounded-lg bg-[var(--h-bg-color)] {{ $errors->has($name) ? 'border-[var(--border-error)]' : 'border-gray-600' }} text-[var(--text-color)] px-3 py-2 border focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300 ease-in-out disabled:bg-transparent placeholder:capitalize"
                    }}"
                />
            @endif
        @endif

        {{-- Dual input --}}
        @if ($dualInput)
            <input
                id="{{ $id2 }}"
                type="{{ $type2 }}"
                value=""
                {{ $attributes->merge([
                    'class' => $class . ' w-full rounded-lg bg-[var(--h-bg-color)] ' .
                        ($errors->has($name) ? 'border-[var(--border-error)]' : 'border-gray-600') .
                        ' text-[var(--text-color)] px-3 py-2 border focus:ring-2 focus:ring-primary transition-all duration-300 ease-in-out disabled:bg-transparent placeholder:capitalize'
                ]) }}
            />
        @endif

        {{-- Image --}}
        @if ($withImg)
            <img id="img-{{ $id }}" src="{{ $imgUrl }}" alt="image" class="absolute right-2 top-1/2 transform -translate-y-1/2 w-6 h-6 cursor-pointer object-cover rounded {{ $imgUrl == '' ? 'opacity-0' : '' }}"/>
        @endif

        {{-- Button --}}
        @if ($withButton)
            <button id="{{$btnId}}" type="button" class="{{ $btnClass }} bg-[var(--primary-color)] px-4 rounded-lg hover:bg-[var(--h-primary-color)] transition-all duration-300 ease-in-out cursor-pointer {{ $btnText === '+' ? 'text-lg font-bold' : 'text-nowrap' }} disabled:opacity-50 disabled:cursor-not-allowed">{!! $btnText !!}</button>
        @endif
    </div>

    {{-- Datalist --}}
    @if($list != '')
        <datalist id="{{ $list }}">
            @foreach ($listOptions as $option)
                <option value="{{ $option }}"></option>
            @endforeach
        </datalist>
    @endif

    {{-- Error --}}
    @error($name)
        <div class="text-[var(--border-error)] text-xs mt-1 transition-all duration-300 ease-in-out">{{ $message }}</div>
    @enderror
</div>
