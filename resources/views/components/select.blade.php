@props([
    'label' => '', // Label text for the select
    'name' => '', // Select name
    'options' => [], // Options array (value => text)
    'value' => '', // Default selected value
    'showDefault' => 'false', // Default selected showDefault
    'class' => '',
    'withButton' => false,
    'btnId' => '',
    'id' => '',
    'btnText' => '+',
    'onchange' => '',
    'btnOnclick' => '',
    'dataFilterPath' => '',
    'searchable' => false,
])

@php
    $haveOptions = count($options) > 0;
@endphp

<style>
    .optionsDropdown {
        opacity: 0;
        pointer-events: none;
        transition: .3s ease-in-out all;
        translate: 0 -10px;
    }

    .selectParent:has(input:focus) .optionsDropdown {
        opacity: 1;
        pointer-events: all;
        translate: 0;
    }

    .selected {
        background-color: var(--h-bg-color);
    }
</style>

<div class="{{ $class }} form-group">
    @if ($label)
        <label for="{{ $name }}"
            class="block font-medium text-[var(--secondary-text)] mb-2">{{ $label . ' *' }}</label>
    @endif

    <div class="selectParent relative flex gap-4">
        @if (!$searchable)
            <select @if (!$haveOptions) disabled @endif id="{{ $id }}" name="{{ $name }}"
                {{ $attributes->merge([
                    'class' =>
                        'w-full rounded-lg bg-[var(--h-bg-color)] border-gray-600 text-[var(--text-color)] px-3 py-2 border appearance-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300 ease-in-out disabled:bg-transparent',
                ]) }}
                {!! $onchange ? 'onchange="' . $onchange . '"' : '' !!} {!! $dataFilterPath ? 'data-filter-path="' . $dataFilterPath . '"' : '' !!}>
                @if ($showDefault == 'true' && $haveOptions)
                    <option value="">
                        -- Select {{ $label }} --
                    </option>
                @endif

                @if ($haveOptions)
                    @foreach ($options as $optionValue => $optionText)
                        <option data-option='{{ $optionText['data_option'] ?? '' }}' value="{{ $optionValue }}"
                            {{ old($name, $value) == $optionValue ? 'selected' : '' }}>
                            {{ $optionText['text'] }}
                        </option>
                    @endforeach
                @else
                    <option value="" selected>
                        -- No options available --
                    </option>
                @endif
            </select>
        @else
            <x-input disabled={{!$haveOptions}} id="{{ $id }}" parentGrow oninput="searchSelect(this)" onblur="validateSelectInput(this)" placeholder="{{!$haveOptions ? '-- No options available --' : ''}}" />
            <input type="hidden" class="dbInput" data-for="{{ $id }}" name="{{ $name }}" />
            <ul
                class="optionsDropdown absolute z-50 top-full mt-2 w-full rounded-xl bg-[var(--secondary-bg-color)] border-gray-600 text-[var(--text-color)] p-1.5 border appearance-none focus:ring-2 focus:ring-primary focus:border-transparent max-h-[14rem] overflow-auto my-scrollbar-2 space-y-0.5">
                @if ($showDefault == 'true' && $haveOptions)
                    <li data-for="{{ $id }}" data-value="" onmousedown="selectThisOption(this)" class="py-2 px-3 cursor-pointer rounded-lg transition hover:bg-[var(--h-bg-color)]">
                        -- Select {{ $label }} --
                    </li>
                @endif

                @if ($haveOptions)
                    @foreach ($options as $optionValue => $optionText)
                        <li data-for="{{ $id }}" data-value="{{ $optionValue }}" onmousedown="selectThisOption(this)"
                            class="py-2 px-3 cursor-pointer rounded-lg transition hover:bg-[var(--h-bg-color)]">
                            {{ $optionText['text'] }}
                        </li>
                    @endforeach
                @endif
            </ul>
        @endif

        @if ($withButton)
            <button onclick="{{ $btnOnclick }}" id="{{ $btnId }}" type="button"
                class="bg-[var(--primary-color)] px-4 rounded-lg hover:bg-[var(--h-primary-color)] transition-all duration-300 ease-in-out cursor-pointer {{ $btnText === '+' ? 'text-lg font-bold' : 'text-nowrap' }} disabled:opacity-50 disabled:cursor-not-allowed">{{ $btnText }}</button>
        @endif
    </div>

    @error($name)
        <div class="text-[var(--border-error)] text-xs mt-1 transition-all duration-300 ease-in-out">{{ $message }}
        </div>
    @enderror

    <div id="{{ $name }}-error"
        class="text-[var(--border-error)] text-xs mt-1 hidden transition-all duration-300 ease-in-out"></div>
</div>
