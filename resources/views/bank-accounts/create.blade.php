@extends('app')
@section('title', 'Add Bank Account | ' . app('company')->name)
@section('content')
@php
    $categories_options = [
        'self' => ['text' => 'Self'],
        'supplier' => ['text' => 'Supplier'],
        'customer' => ['text' => 'Customer'],
    ];
@endphp
    <div class="max-w-3xl mx-auto">
        <x-search-header heading="Add Bank Account" link linkText="Show Bank Accounts" linkHref="{{ route('bank-accounts.index') }}"/>
    </div>
    <!-- Form -->
    <form id="form" action="{{ route('bank-accounts.store') }}" method="post" enctype="multipart/form-data"
        class="bg-[var(--secondary-bg-color)] text-sm rounded-xl shadow-lg p-8 border border-[var(--glass-border-color)]/20 pt-14 max-w-3xl mx-auto  relative overflow-hidden">
        @csrf
        <x-form-title-bar title="Add Bank Account" />

        <!-- Step 1: Basic Information -->
        <div class="step space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- category --}}
                <x-select 
                    label="Category"
                    name="category"
                    id="category"
                    :options="$categories_options"
                    onchange="getCategoryData(this.value)"
                    required
                    showDefault
                />

                <!-- account_no  -->
                <x-input
                    label="Account No."
                    name="account_no" 
                    id="account_no"
                    type="number"
                    placeholder="Enter account no." 
                />
                
                {{-- sub_category --}}
                <x-select 
                    label="Disabled"
                    name="sub_category"
                    id="subCategory"
                    disabled
                    showDefault
                />
                
                {{-- bank --}}
                <x-select 
                    label="Bank"
                    name="bank_id"
                    id="bank"
                    :options="$bank_options"
                    required
                    showDefault
                />

                <!-- account_title -->
                <x-input
                    label="Account Title"
                    name="account_title" 
                    id="account_title" 
                    placeholder="Enter account title" 
                    capitalized
                    required 
                />

                <!-- date -->
                <x-input
                    label="Date"
                    name="date" 
                    id="date"
                    type="date"
                    required 
                />

                <!-- remarks -->
                <x-input
                    label="Remarks"
                    name="remarks" 
                    id="remarks"
                    placeholder="Enter remerks" 
                />

                <!-- Cheque Book Serial Input -->
                <div id="cheque_book_serial" class="form-group">
                    <label for="cheque_book_serial_start" class="block font-medium text-[var(--secondary-text)] mb-2">
                        Cheque Book Serial (Start - End)
                    </label>
                
                    <div class="flex gap-4">
                        <!-- Start Serial Input -->
                        <input 
                            type="number" 
                            id="cheque_book_serial_start" 
                            name="cheque_book_serial[start]" 
                            placeholder="Start" 
                            class="w-full rounded-lg bg-[var(--h-bg-color)] border-gray-600 text-[var(--text-color)] px-3 py-2 border focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300 ease-in-out"
                        />
                
                        <!-- End Serial Input -->
                        <input 
                            type="number" 
                            id="cheque_book_serial_end" 
                            name="cheque_book_serial[end]" 
                            placeholder="End" 
                            class="w-full rounded-lg bg-[var(--h-bg-color)] border-gray-600 text-[var(--text-color)] px-3 py-2 border focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300 ease-in-out"
                        />
                    </div>
                
                    <!-- Error Message -->
                    <div id="cheque_book_serial_error" class="text-[var(--border-error)] text-xs mt-1 hidden"></div>
                </div>
            </div>
        </div>
        
        <div class="w-full flex justify-end mt-4">
            <button type="submit"
                class="px-6 py-1 bg-[var(--bg-success)] border border-[var(--bg-success)] text-[var(--text-success)] font-medium text-nowrap rounded-lg hover:bg-[var(--h-bg-success)] transition-all duration-300 ease-in-out cursor-pointer">
                <i class='fas fa-save mr-1'></i> Save
            </button>
        </div>
    </form>

    
    <script>
        document.getElementById('cheque_book_serial_end').addEventListener('input', () => {
            const start = parseInt(document.getElementById('cheque_book_serial_start').value);
            const end = parseInt(document.getElementById('cheque_book_serial_end').value);
            const errorDiv = document.getElementById('cheque_book_serial_error');

            if (end < start) {
                errorDiv.innerText = 'End serial must be greater than or equal to start serial.';
                errorDiv.classList.remove('hidden');
            } else {
                errorDiv.innerText = '';
                errorDiv.classList.add('hidden');
            }
        });

        let subCategoryLabelDom = document.querySelector('[for=sub_category]');
        let accountNoLabelDom = document.querySelector('[for=account_no]');
        let chequeBookSerialDom = document.getElementById('cheque_book_serial');
        let remarksLabelDom = document.querySelector('[for=remarks]');
        let subCategorySelectDom = document.getElementById('subCategory');
        let subCategorySelectOptionsDom = subCategorySelectDom.parentElement.parentElement.parentElement.querySelector('.optionsDropdown');
        let subCategoryFirstOptDom = subCategorySelectDom.children[0];
        accountNoLabelDom.parentElement.classList.add('hidden');
        chequeBookSerialDom.classList.add('hidden');
        function getCategoryData(value) {
            if (value != "waiting") {
                $.ajax({
                    url: "/get-category-data",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        category: value,
                    },
                    success: function (response) {
                        let clutter = '';
                        switch (value) {
                            case 'supplier':
                                subCategoryLabelDom.parentElement.classList.remove('hidden');
                                remarksLabelDom.parentElement.classList.remove('hidden');
                                accountNoLabelDom.parentElement.classList.add('hidden');
                                chequeBookSerialDom.classList.add('hidden');
                                clutter += `
                                    <li data-for="subCategory" data-value="" onmousedown="selectThisOption(this)" class="py-2 px-3 cursor-pointer rounded-lg transition hover:bg-[var(--h-bg-color)]">
                                        -- Select Supplier --
                                    </li>
                                `;
                        
                                response.forEach(subCat => {
                                    clutter += `
                                        <li data-for="subCategory" data-value="${subCat.id}" onmousedown="selectThisOption(this)" class="py-2 px-3 cursor-pointer rounded-lg transition hover:bg-[var(--h-bg-color)] text-nowrap overflow-scroll my-scrollbar-2">
                                            ${subCat.supplier_name}
                                        </li>
                                    `;
                                });
                                
                                subCategoryLabelDom.textContent = 'Supplier';
                                // subCategoryFirstOptDom.textContent = '-- Select Supplier --';
                                subCategorySelectDom.disabled = false;
                                break;

                            case 'customer':
                                subCategoryLabelDom.parentElement.classList.remove('hidden');
                                remarksLabelDom.parentElement.classList.remove('hidden');
                                accountNoLabelDom.parentElement.classList.add('hidden');
                                chequeBookSerialDom.classList.add('hidden');
                                clutter += `
                                    <li data-for="subCategory" data-value="" onmousedown="selectThisOption(this)" class="py-2 px-3 cursor-pointer rounded-lg transition hover:bg-[var(--h-bg-color)]">
                                        -- Select Customer --
                                    </li>
                                `;
                        
                                response.forEach(subCat => {
                                    clutter += `
                                        <li data-for="subCategory" data-value="${subCat.id}" onmousedown="selectThisOption(this)" class="py-2 px-3 cursor-pointer rounded-lg transition hover:bg-[var(--h-bg-color)] text-nowrap overflow-scroll my-scrollbar-2">
                                            ${subCat.customer_name}
                                        </li>
                                    `;
                                });

                                subCategoryLabelDom.textContent = 'Customer';
                                // subCategoryFirstOptDom.textContent = '-- Select Customer --';
                                subCategorySelectDom.disabled = false;
                                break;
                                
                            case 'self':
                                subCategoryLabelDom.parentElement.classList.add('hidden');
                                remarksLabelDom.parentElement.classList.add('hidden');
                                accountNoLabelDom.parentElement.classList.remove('hidden');
                                chequeBookSerialDom.classList.remove('hidden');
                                break;
                                
                            default:
                                subCategoryLabelDom.parentElement.classList.remove('hidden');
                                remarksLabelDom.parentElement.classList.remove('hidden');
                                accountNoLabelDom.parentElement.classList.add('hidden');
                                chequeBookSerialDom.classList.add('hidden');
                                clutter += `
                                    <option value=''>
                                        -- No options available --
                                    </option>
                                `;

                                // subCategoryFirstOptDom.textContent = '-- No Options --';
                                subCategoryLabelDom.textContent = 'Disabled';
                                subCategorySelectDom.disabled = true;
                                break;
                        }
                        subCategorySelectOptionsDom.innerHTML = clutter;
                    }
                });
            }
        }
    </script>
@endsection
