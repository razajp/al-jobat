@props([
    'label' => '',
    'name' => '',
    'options' => [],
    'value' => '',
    'showDefault' => false,
    'class' => '',
    'withButton' => false,
    'btnId' => '',
    'id' => '',
    'btnText' => '+',
    'onchange' => '',
    'btnOnclick' => '',
    'dataFilterPath' => '',
    'dataClearable' => false,
    'disabled' => false,
    'addBtnLink' => '',
])

@php
    $haveOptions = count($options) > 0;
    $resolvedValue = old($name, $value);
    $isDisabled = !$haveOptions || $disabled;

    // Determine selected option
    $selectedText = '';
    if ($resolvedValue && isset($options[$resolvedValue])) {
        $selectedText = $options[$resolvedValue]['text'];
    }

    // Placeholder logic
    $placeholderText = '';
    if ($isDisabled && $selectedText) {
        $placeholderText = $selectedText;
    } elseif (!$haveOptions) {
        $placeholderText = '-- No options available --';
    } elseif ($showDefault === true && !$resolvedValue) {
        $placeholderText = '-- Select ' . $label . ' --';
    }

    // Highlight default if not disabled and no selection
    $showDefaultSelected = !$isDisabled && $resolvedValue === '' && $showDefault;
@endphp

<style>
    .dropDownParent {
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease-in-out, translate 0.3s ease-in-out;
        translate: 0 -10px;
    }
    .selectParent:has(input:focus) .dropDownParent {
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
        <span class="flex items-center justify-between mb-2">
            <label for="{{ $name }}" class="block font-medium text-[var(--secondary-text)]">
                {{ $label }}
            </label>
            @if ($addBtnLink !== '')
                <a class='text-lg px-2 leading-none' href="{{ $addBtnLink }}">+</a>
            @endif
        </span>
    @endif

    <div class="selectParent flex gap-4">
        {{-- Visible Input --}}
        <x-input
            id="{{ $id }}"
            name="{{ $id }}_name"
            parentGrow
            {{-- oninput="searchSelect(this)" --}}
            {{-- onblur="validateSelectInput(this)" --}}
            autocomplete="off"
            :disabled="$isDisabled"
            :value="$isDisabled ? '' : $selectedText"
            :placeholder="$placeholderText"
            onfocus="selectClicked(this)"
            {{-- onkeydown="selectKeyDown(event, this)" --}}
            :dataClearable="$dataClearable"
            isSelect
        />

        {{-- Hidden Input --}}
        <input
            type="hidden"
            class="dbInput"
            data-for="{{ $id }}"
            name="{{ $name }}"
            value="{{ $isDisabled ? '' : $resolvedValue }}"
            {!! $onchange ? 'onchange="' . $onchange . '"' : '' !!}
            {!! $dataFilterPath ? 'data-filter-path="' . $dataFilterPath . '"' : '' !!}
            @if ($dataClearable) data-clearable @endif
        >

        {{-- Dropdown List --}}
        <div class="dropDownParent flex flex-col gap-2 fixed z-50 mt-2 w-full rounded-xl bg-[var(--secondary-bg-color)] border-gray-600 text-[var(--text-color)] p-1.5 border appearance-none focus:ring-2 focus:ring-primary focus:border-transparent max-h-[13rem]">
            <x-input
                data-for="{{ $id }}"
                oninput="searchSelect(this)"
                onblur="validateSelectInput(this)"
                autocomplete="off"
                :value="$isDisabled ? '' : $selectedText"
                :placeholder="$placeholderText"
                onkeydown="selectKeyDown(event, this)"
                :dataClearable="$dataClearable"
            />
            <ul
                class="optionsDropdown overflow-auto my-scrollbar-2 space-y-0.5 grow"
                data-for="{{ $id }}"
            >
                @if ($showDefault === true && $haveOptions)
                    <li
                        data-for="{{ $id }}"
                        data-value=""
                        onmousedown="selectThisOption(this)"
                        class="py-2 px-3 cursor-pointer rounded-lg transition hover:bg-[var(--h-bg-color)] {{ $showDefaultSelected ? 'selected' : '' }}"
                    >
                        -- Select {{ $label }} --
                    </li>
                @endif

                @foreach ($options as $optionValue => $option)
                    <li
                        data-for="{{ $id }}"
                        data-value="{{ $optionValue }}"
                        onmousedown="selectThisOption(this)"
                        @if (isset($option['data_option']))
                            data-option="{{ $option['data_option'] }}"
                        @endif
                        class="py-2 px-3 cursor-pointer rounded-lg transition hover:bg-[var(--h-bg-color)] text-nowrap overflow-x-auto scrollbar-hidden {{ !$isDisabled && $optionValue == $resolvedValue ? 'selected' : '' }}"
                    >
                        {{ $option['text'] }}
                    </li>
                    @if (isset($option['selected']))
                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                selectThisOption(document.querySelector('li[data-value="{{ $optionValue }}"]'));
                            });
                        </script>
                    @endif
                @endforeach
            </ul>
        </div>

        {{-- Optional Button --}}
        @if ($withButton)
            <button onclick="{{ $btnOnclick }}" id="{{ $btnId }}" type="button"
                class="bg-[var(--primary-color)] px-4 rounded-lg hover:bg-[var(--h-primary-color)] transition-all duration-300 ease-in-out cursor-pointer {{ $btnText === '+' ? 'text-lg font-bold' : 'text-nowrap' }} disabled:opacity-50 disabled:cursor-not-allowed">
                {{ $btnText }}
            </button>
        @endif
    </div>

    {{-- Validation --}}
    @error($name)
        <div class="text-[var(--border-error)] text-xs mt-1 transition-all duration-300 ease-in-out">
            {{ $message }}
        </div>
    @enderror

    <div id="{{ $name }}-error"
        class="text-[var(--border-error)] text-xs mt-1 hidden transition-all duration-300 ease-in-out"></div>
</div>
