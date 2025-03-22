@extends('app')
@section('title', 'Add Bank | ' . app('company')->name)
@section('content')
@php
    $categories_options = [
        'self' => ['text' => 'Self'],
        'supplier' => ['text' => 'Supplier'],
        'customer' => ['text' => 'Customer'],
    ];

    $banks_options = [
        'meezan_bank' => ['text' => 'Meezan Bank'],
        'habib_bak' => ['text' => 'Habib Bank'],
        'bakn_alfalah' => ['text' => 'Bank Al-falah'],
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
                
                {{-- cusomer --}}
                <x-select 
                    label="Disabled"
                    name="sub_category"
                    id="subCategory"
                    disabled
                    required
                    showDefault
                />
                
                <x-select 
                    label="Bank"
                    name="bank"
                    id="bank"
                    :options="$banks_options"
                    required
                    showDefault
                />

                <!-- customer_name -->
                <x-input
                    label="Account Title"
                    name="account_title" 
                    id="account_title" 
                    placeholder="Enter account title" 
                    required 
                />

                <!-- customer_name -->
                <x-input
                    label="Account No."
                    name="account_no" 
                    id="account_no"
                    type="number"
                    placeholder="Enter account no." 
                    required 
                />

                <!-- customer_name -->
                <x-input
                    label="Date"
                    name="date" 
                    id="date"
                    type="date"
                    required 
                />
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
        let subCategoryLabelDom = document.querySelector('[for=sub_category]');
        let subCategorySelectDom = document.getElementById('subCategory');
        let subCategoryFirstOptDom = subCategorySelectDom.children[0];
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
                                clutter += `
                                    <option value=''>
                                        -- Select Owner --
                                    </option>
                                `;

                                response.forEach(subCat => {
                                    clutter += `
                                        <option value='${subCat.id}'>
                                            ${subCat.name}
                                        </option>
                                    `;
                                });

                                subCategoryLabelDom.textContent = 'Owner';
                                subCategoryFirstOptDom.textContent = '-- Select Owner --';
                                subCategorySelectDom.disabled = false;
                                break;
                        
                            default:
                                break;
                        }
                        subCategorySelectDom.innerHTML = clutter;
                    }
                });
            }
        }
    </script>
@endsection
