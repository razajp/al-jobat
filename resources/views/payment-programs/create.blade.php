@extends('app')
@section('title', 'Add Payment Program | ' . app('company')->name)
@section('content')
@php
    $categories_options = [
        'self_account' => ['text' => 'Self Account'],
        'supplier' => ['text' => 'Supplier'],
        // 'customer' => ['text' => 'Customer'],
        'waiting' => ['text' => 'Waiting'],
    ]
@endphp
    <!-- Main Content -->

    <div class="max-w-3xl mx-auto">
        <x-search-header heading="Add Payment Program" link linkText="Show Payment Programs" linkHref="{{ route('payment-programs.index') }}"/>
    </div>

    <!-- Form -->
    <form id="form" action="{{ route('payment-programs.store') }}" method="post"
        class="bg-[var(--secondary-bg-color)] text-sm rounded-xl shadow-lg p-8 border border-[var(--glass-border-color)]/20 pt-14 max-w-3xl mx-auto  relative overflow-hidden">
        @csrf
        <x-form-title-bar title="Add Payment Program" />

        <div class="grid grid-cols-2 gap-4">
            {{-- date --}}
            <x-input label="Date" name="date" id="date" type="date" onchange="trackDateState(this)" validateMax max="{{ now()->toDateString() }}" required />

            {{-- cusomer --}}
            <x-select
                label="Customer"
                name="customer_id"
                id="customer_id"
                :options="$customers_options"
                onchange="trackCustomerState(this)"
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

            {{-- <x-input name="program_no" id="program_no" type="hidden" value="{{ $lastProgram->program_no + 1 }}" /> --}}

            <div class="col-span-full">
                {{-- amount --}}
                <x-input label="Amount" type="amount" name="amount" id="amount" placeholder='Enter Amount' required dataValidate="required|amount" />
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
        let dateInpDom = document.getElementById('date');
        let customerSelect = document.getElementById('customer_id');
        let categorySelectDom = document.getElementById('category');
        let amountInpDom = document.getElementById('amount');
        customerSelect.disabled = true;
        categorySelectDom.disabled = true;

        function trackDateState(dateInputElem) {
            customerSelect.disabled = false;
        }

        function trackCustomerState(elem) {
            if (elem.value) {
                categorySelectDom.disabled = false;
            } else {
                categorySelectDom.disabled = true;
            }
        }

        let subCategoryLabelDom = document.querySelector('[for=sub_category]');
        let subCategorySelectDom = document.getElementById('subCategory');
        let subCategoryFirstOptDom = subCategorySelectDom.children[0];

        let remarksInputDom = document.getElementById('remarks');
        remarksInputDom.parentElement.parentElement.classList.add("hidden");

        function getCategoryData(value) {
            const subCategorySearchInput = document.getElementById('subCategory');
            const subCategoryHiddenInput = document.querySelector('input.dbInput[data-for="subCategory"]');
            const subCategoryOptionBox = subCategoryHiddenInput.parentElement.querySelector('ul');
            const subCategoryWrapper = subCategorySearchInput.closest('.form-group').parentElement.closest('.form-group');
            const subCategoryLabel = subCategoryWrapper.querySelector('label');

            if (value !== "waiting") {
                subCategoryWrapper.classList.remove("hidden");
                remarksInputDom.parentElement.parentElement.classList.add("hidden");

                $.ajax({
                    url: "/get-category-data",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        category: value,
                    },
                    success: function (response) {
                        let items = [];

                        switch (value) {
                            case 'self_account':
                                subCategoryLabel.textContent = 'Self Account';
                                if (response.length > 0) {
                                    items.push(`<li data-for="subCategory" data-value="" onmousedown="selectThisOption(this)" class="py-2 px-3 cursor-pointer rounded-lg hover:bg-[var(--h-bg-color)]">-- Select Self Account --</li>`);
                                    response.forEach(acc => {
                                        items.push(`<li data-for="subCategory" data-value="${acc.id}" onmousedown="selectThisOption(this)" class="py-2 px-3 cursor-pointer rounded-lg hover:bg-[var(--h-bg-color)]">${acc.account_title} | ${acc.bank.short_title}</li>`);
                                    });
                                    subCategorySearchInput.disabled = false;
                                } else {
                                    items.push(`<li class="py-2 px-3 text-gray-400">-- No options available --</li>`);
                                    subCategorySearchInput.disabled = true;
                                }
                                break;

                            case 'supplier':
                                subCategoryLabel.textContent = 'Supplier';
                                if (response.length > 0) {
                                    items.push(`<li data-for="subCategory" data-value="" onmousedown="selectThisOption(this)" class="py-2 px-3 cursor-pointer rounded-lg hover:bg-[var(--h-bg-color)]">-- Select Supplier --</li>`);
                                    response.forEach(sup => {
                                        items.push(`<li data-for="subCategory" data-value="${sup.id}" onmousedown="selectThisOption(this)" class="py-2 px-3 cursor-pointer rounded-lg hover:bg-[var(--h-bg-color)]">${sup.supplier_name} | Balance: ${formatNumbersWithDigits(sup.balance, 1, 1)}</li>`);
                                    });
                                    subCategorySearchInput.disabled = false;
                                } else {
                                    items.push(`<li class="py-2 px-3 text-gray-400">-- No options available --</li>`);
                                    subCategorySearchInput.disabled = true;
                                }
                                break;

                            case 'customer':
                                subCategoryLabel.textContent = 'Customer';
                                items.push(`<li data-for="subCategory" data-value="" onmousedown="selectThisOption(this)" class="py-2 px-3 cursor-pointer rounded-lg hover:bg-[var(--h-bg-color)]">-- Select Customer --</li>`);
                                response.forEach(cus => {
                                    if (cus.id != customerSelect.value) {
                                        items.push(`<li data-for="subCategory" data-value="${cus.id}" onmousedown="selectThisOption(this)" class="py-2 px-3 cursor-pointer rounded-lg hover:bg-[var(--h-bg-color)]">${cus.customer_name} | ${cus.city.title} | Balance: ${formatNumbersWithDigits(cus.balance, 1, 1)}</li>`);
                                    }
                                });
                                subCategorySearchInput.disabled = false;
                                break;
                        }

                        // ✅ Inject options in the box
                        subCategoryOptionBox.innerHTML = items.join('');

                        // ✅ Clear previous selection
                        subCategorySearchInput.value = '';
                        subCategoryHiddenInput.value = '';
                    },
                    error: function (xhr) {
                        console.error("❌ Error:", xhr.responseText);
                        subCategoryOptionBox.innerHTML = `<li class="py-2 px-3 text-red-500">Error loading options</li>`;
                        subCategorySearchInput.disabled = true;
                    }
                });
            } else {
                // Show remarks input instead of dropdown
                subCategoryWrapper.classList.add("hidden");
                remarksInputDom.parentElement.parentElement.classList.remove("hidden");
            }
        }
    </script>
@endsection
