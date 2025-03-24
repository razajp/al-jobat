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
    <h1 class="text-3xl font-bold mb-5 text-center text-[--primary-color] fade-in"> Add Bank </h1>

    <!-- Form -->
    <form id="form" action="{{ route('bank-accounts.store') }}" method="post" enctype="multipart/form-data"
        class="bg-[--secondary-bg-color] text-sm rounded-xl shadow-lg p-8 border border-[--h-bg-color] pt-12 max-w-2xl mx-auto  relative overflow-hidden">
        @csrf
        <div
            class="form-title text-center absolute top-0 left-0 w-full bg-[--primary-color] py-1 capitalize tracking-wide font-medium text-sm">
            <h4>Add New Bank</h4>
        </div>
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
                    <label for="cheque_book_serial_start" class="block font-medium text-[--secondary-text] mb-2">
                        Cheque Book Serial (Start - End)
                    </label>
                
                    <div class="flex gap-4">
                        <!-- Start Serial Input -->
                        <input 
                            type="number" 
                            id="cheque_book_serial_start" 
                            name="cheque_book_serial[start]" 
                            placeholder="Start" 
                            class="w-full rounded-lg bg-[--h-bg-color] border-gray-600 text-[--text-color] px-3 py-2 border focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300 ease-in-out"
                            required
                        />
                
                        <!-- End Serial Input -->
                        <input 
                            type="number" 
                            id="cheque_book_serial_end" 
                            name="cheque_book_serial[end]" 
                            placeholder="End" 
                            class="w-full rounded-lg bg-[--h-bg-color] border-gray-600 text-[--text-color] px-3 py-2 border focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300 ease-in-out"
                            required
                        />
                    </div>
                
                    <!-- Error Message -->
                    <div id="cheque_book_serial_error" class="text-[--border-error] text-xs mt-1 hidden"></div>
                </div>
            </div>
        </div>
        
        <div class="w-full flex justify-end mt-4">
            <button type="submit"
                class="px-6 py-1 bg-[--bg-success] border border-[--bg-success] text-[--text-success] font-medium text-nowrap rounded-lg hover:bg-[--h-bg-success] transition-all 0.3s ease-in-out">
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
                                    <option value=''>
                                        -- Select Supplier --
                                    </option>
                                `;
                        
                                response.forEach(subCat => {
                                    clutter += `
                                        <option value='${subCat.id}'>
                                            ${subCat.supplier_name}
                                        </option>
                                    `;
                                });
                                
                                subCategoryLabelDom.textContent = 'Supplier';
                                subCategoryFirstOptDom.textContent = '-- Select Supplier --';
                                subCategorySelectDom.disabled = false;
                                break;

                            case 'customer':
                                subCategoryLabelDom.parentElement.classList.remove('hidden');
                                remarksLabelDom.parentElement.classList.remove('hidden');
                                accountNoLabelDom.parentElement.classList.add('hidden');
                                chequeBookSerialDom.classList.add('hidden');
                                clutter += `
                                    <option value=''>
                                        -- Select Customer --
                                    </option>
                                `;
                        
                                response.forEach(subCat => {
                                    clutter += `
                                        <option value='${subCat.id}'>
                                            ${subCat.customer_name}
                                        </option>
                                    `;
                                });

                                subCategoryLabelDom.textContent = 'Customer';
                                subCategoryFirstOptDom.textContent = '-- Select Customer --';
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

                                subCategoryFirstOptDom.textContent = '-- No Options --';
                                subCategoryLabelDom.textContent = 'Disabled';
                                subCategorySelectDom.disabled = true;
                                break;
                        }
                        subCategorySelectDom.innerHTML = clutter;
                    }
                });
            }
        }
    </script>
@endsection
