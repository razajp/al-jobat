@extends('app')
@section('title', 'Add Payment Program | ' . app('company')->name)
@section('content')
@php
    $categories_options = [
        'self_account' => ['text' => 'Self Account'],
        'supplier' => ['text' => 'Supplier'],
        'customer' => ['text' => 'Customer'],
        'waiting' => ['text' => 'Waiting'],
    ]
@endphp
    <!-- Modal -->
    <div id="articleModal"
        class="hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-[var(--overlay-color)] fade-in">
    </div>
    <!-- Main Content -->
    <h1 class="text-3xl font-bold mb-6 text-center text-[var(--primary-color)] fade-in"> Add Payment Program </h1>

    <!-- Form -->
    <form id="form" action="{{ route('payment-programs.store') }}" method="post"
        class="bg-[var(--secondary-bg-color)] text-sm rounded-xl shadow-lg p-8 border border-[var(--h-bg-color)] pt-12 max-w-3xl mx-auto  relative overflow-hidden">
        @csrf
        <div
            class="form-title text-center absolute top-0 left-0 w-full bg-[var(--primary-color)] py-1 capitalize tracking-wide font-medium text-sm">
            <h4>Add Payment Program</h4>
        </div>

        <div class="grid grid-cols-2 gap-4">
            {{-- order_no --}}
            <x-input label="Order No." name="order_no" id="order_no" placeholder='Enter Order No.' />
            
            {{-- date --}}
            <x-input label="Date" name="date" id="date" type="date" onchange="trackCustomerState(this)" required />

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
                showDefault
            />
            
            {{-- remarks --}}
            <x-input label="Remarks" name="remarks" id="remarks" placeholder="Enter Remarks" />

            {{-- amount --}}
            <x-input label="Amount" type="number" name="amount" id="amount" placeholder='Enter Amount' required />
            <x-input name="program_no" id="program_no" type="hidden" value="{{ $lastProgram->program_no + 1 }}" />
        </div>
        <div class="w-full flex justify-end mt-4">
            <button type="submit"
                class="px-6 py-1 bg-[var(--bg-success)] border border-[var(--bg-success)] text-[var(--text-success)] font-medium text-nowrap rounded-lg hover:bg-[var(--h-bg-success)] transition-all 0.3s ease-in-out">
                <i class='fas fa-save mr-1'></i> Save
            </button>
        </div>
    </form>

    <script>
        let orderNoInpDom = document.getElementById('order_no');
        let dateInpDom = document.getElementById('date');
        let customerSelect = document.getElementById('customer_id');
        let categorySelectDom = document.getElementById('category');
        let amountInpDom = document.getElementById('amount');
        customerSelect.disabled = true;
        categorySelectDom.disabled = true;
        
        function trackCustomerState(dateInputElem) {
            customerSelect.disabled = false;
        }

        customerSelect.addEventListener('change', () => {
            if (customerSelect.value) {
                categorySelectDom.disabled = false;
            } else {
                categorySelectDom.disabled = true;
            }
        })

        let subCategoryLabelDom = document.querySelector('[for=sub_category]');
        let subCategorySelectDom = document.getElementById('subCategory');
        let subCategoryFirstOptDom = subCategorySelectDom.children[0];

        let remarksInputDom = document.getElementById('remarks');
        remarksInputDom.parentElement.parentElement.classList.add("hidden");

        function getCategoryData(value) {
            if (value != "waiting") {
                subCategorySelectDom.parentElement.parentElement.classList.remove("hidden");
                remarksInputDom.parentElement.parentElement.classList.add("hidden");

                $.ajax({
                    url: "/get-category-data",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        category: value,
                    },
                    success: function (response) {
                        let clutter = `
                            <option value=''>
                                -- No option avalaible --
                            </option>
                        `;
                        switch (value) {   
                            case 'self_account':
                                if (response.length > 0) {
                                    clutter = '';
                                    clutter += `
                                        <option value=''>
                                            -- Select Self Account --
                                        </option>
                                    `;
                                    subCategorySelectDom.disabled = false;
                                } else {
                                    subCategorySelectDom.disabled = true;
                                    subCategoryFirstOptDom.textContent = '-- No options available --';
                                }
                        
                                response.forEach(subCat => {
                                    clutter += `
                                        <option value='${subCat.id}'>
                                            ${subCat.account_title} | ${subCat.bank.short_title}
                                        </option>
                                    `;
                                });
                                
                                subCategoryLabelDom.textContent = 'Self Account';
                                subCategoryFirstOptDom.textContent = '-- Select Self Account --';
                                break;
                                
                            case 'supplier':
                                if (response.length > 0) {
                                    clutter = '';
                                    clutter += `
                                        <option value=''>
                                            -- Select Supplier --
                                        </option>
                                    `;
                                    subCategorySelectDom.disabled = false;
                                } else {
                                    subCategorySelectDom.disabled = true;
                                    subCategoryFirstOptDom.textContent = '-- No options available --';
                                }
                        
                                response.forEach(subCat => {
                                    clutter += `
                                        <option value='${subCat.id}'>
                                            ${subCat.supplier_name} | Balance: ${formatNumbersWithDigits(subCat.balance, 1, 1)}
                                        </option>
                                    `;
                                });
                                
                                subCategoryLabelDom.textContent = 'Supplier';
                                subCategoryFirstOptDom.textContent = '-- Select Supplier --';
                                break;
                            
                            case 'customer':
                                clutter = '';
                                clutter += `
                                    <option value=''>
                                        -- Select Customer --
                                    </option>
                                `;
                        
                                response.forEach(subCat => {
                                    if (subCat.id != customerSelect.value) {
                                        clutter += `
                                            <option value='${subCat.id}'>
                                                ${subCat.customer_name} | ${subCat.city} | Baalance: ${formatNumbersWithDigits(subCat.balance, 1, 1)}
                                            </option>
                                        `;
                                        subCategorySelectDom.disabled = false;
                                    }
                                });
                                
                                subCategoryLabelDom.textContent = 'Customer';
                                subCategoryFirstOptDom.textContent = '-- Select Customer --';
                                break;
                        
                            default:
                                break;
                        }

                        subCategorySelectDom.innerHTML = clutter;
                    }
                });
            } else {
                subCategorySelectDom.parentElement.parentElement.classList.add("hidden");
                remarksInputDom.parentElement.parentElement.classList.remove("hidden");
            }
        }

        orderNoInpDom.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                getOrderDetails(orderNoInpDom.value);
            }
        });
        
        let orderNoInpBlurValue = '';

        orderNoInpDom.addEventListener('focus', () => {
            let currentYear = new Date().getFullYear();
            if (orderNoInpDom.value) {
                orderNoInpDom.value = orderNoInpDom.value.split('|')[0].trim();
            } else {
                orderNoInpDom.value = currentYear + '-';
            }
        })

        function getOrderDetails(value) {
            $.ajax({
                url: "/gget-order-details",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    order_no: value,
                    only_order: true,
                },
                success: function (response) {
                    let clutter = '';
                    if (response && !response.error) {
                        orderNoInpBlurValue = `${response.order_no}`

                        categorySelectDom.disabled = false;

                        dateInpDom.value = response.date;
                        dateInpDom.readOnly = true;
                        
                        clutter += `
                            <option value='${response.customer.id}'>
                                ${response.customer.customer_name} | ${response.customer.city} | Balance: ${formatNumbersWithDigits(response.customer.balance, 1, 1)}
                            </option>
                        `;
                        customerSelect.innerHTML = clutter;
                        customerSelect.disabled = false;
                        customerSelect.readOnly = true;

                        amountInpDom.readOnly = true;
                        amountInpDom.value = response.netAmount;

                        orderNoInpDom.blur();
                    } else {
                        dateInpDom.value = '';
                        dateInpDom.readOnly = false;
                        dateInpDom.disabled = true;

                        customerSelect.innerHTML = clutter;
                        customerSelect.disabled = true;
                        customerSelect.readOnly = false;
                        
                        amountInpDom.readOnly = false;
                        amountInpDom.value = 0;
                    }
                }
            });
        }

        orderNoInpDom.addEventListener('blur', () => {
            orderNoInpDom.value = orderNoInpBlurValue;
        })
    </script>
@endsection
