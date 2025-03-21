@extends('app')
@section('title', 'Add Online Program | ' . app('company')->name)
@section('content')
@php
    $categories_options = [
        'bank_acount' => ['text' => 'Bank Account'],
        'supplier' => ['text' => 'Supplier'],
        'waiting' => ['text' => 'Waiting'],
    ]
@endphp
    <!-- Modal -->
    <div id="articleModal"
        class="hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-black bg-opacity-50 fade-in">
    </div>
    <!-- Main Content -->
    <h1 class="text-3xl font-bold mb-6 text-center text-[--primary-color] fade-in"> Add Online Program </h1>

    <!-- Form -->
    <form id="form" action="{{ route('online-programs.store') }}" method="post"
        class="bg-[--secondary-bg-color] text-sm rounded-xl shadow-lg p-8 border border-[--h-bg-color] pt-12 max-w-3xl mx-auto  relative overflow-hidden">
        @csrf
        <div
            class="form-title text-center absolute top-0 left-0 w-full bg-[--primary-color] py-1 capitalize tracking-wide font-medium text-sm">
            <h4>Add Online Program</h4>
        </div>

        <div class="grid grid-cols-2 gap-4">
            {{-- date --}}
            <x-input label="Date" name="date" id="date" type="date" oninput="trackCustomerState(this)" required />
            
            {{-- cusomer --}}
            <x-select 
                label="Customer"
                name="customer_id"
                id="customer_id"
                :options="$customers_options"
                required
                showDefault
            />
            
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
            
            {{-- amount --}}
            <div class="col-span-2">
                <x-input label="Amount" type="number" name="amount" id="amount" placeholder='Enter Amount' required />
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
        let customerSelect = document.getElementById('customer_id');
        customerSelect.disabled = true;
        function trackCustomerState(dateInputElem) {
            customerSelect.disabled = false;
        }

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
                                break;
                        
                            default:
                                break;
                        }
                        
                        response.forEach(subCat => {
                            clutter += `
                                <option value='${subCat.id}'>
                                    ${subCat.supplier_name}
                                </option>
                            `;
                        });
                        subCategorySelectDom.innerHTML = clutter;

                        switch (value) {
                            case 'supplier':
                                subCategoryLabelDom.textContent = 'Supplier';
                                subCategoryFirstOptDom.textContent = '-- Select Supplier --';
                                subCategorySelectDom.disabled = false;
                                break;
                        
                            default:
                                break;
                        }
                    }
                });
            }
        }
    </script>
@endsection
