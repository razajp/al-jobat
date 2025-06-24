@extends('app')
@section('title', 'Add Customer Payment | ' . app('company')->name)
@section('content')
    @php
        $method_options = [
            'cash' => ['text' => 'Cash'],
            'cheque' => ['text' => 'Cheque'],
            'slip' => ['text' => 'Slip'],
            'adjustment' => ['text' => 'Adjustment'],
        ];
        $type_options = [
            'normal' => ['text' => 'Normal'],
            'payment_program' => ['text' => 'Payment Program'],
            'recovery' => ['text' => 'Recovery'],
        ]
    @endphp
    <!-- Progress Bar -->
    <div class="mb-5 max-w-3xl mx-auto">
        <x-search-header heading="Add Customer Payment" link linkText="Show Payments" linkHref="{{ route('customer-payments.index') }}"/>
    </div>

    <!-- Form -->
    <form id="form" action="{{ route('customer-payments.store') }}" method="post"
        class="bg-[var(--secondary-bg-color)] text-sm rounded-xl shadow-lg p-8 border border-[var(--h-bg-color)] pt-12 max-w-3xl mx-auto  relative overflow-hidden">
        @csrf
        <x-form-title-bar title="Add Customer Payment" />

        <div class="step space-y-4 ">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- customer --}}
                <x-select 
                    label="Customer"
                    name="customer_id"
                    id="customer_id"
                    :options="$customers_options"
                    required
                    showDefault
                    onchange="trackCustomerState()" 
                />
                
                {{-- balance --}}
                <x-input label="Balance" placeholder="Select customer first" name="balance" id="balance" disabled />
                
                {{-- date --}}
                <x-input label="Date" name="date" id="date" type="date" required disabled />

                {{-- type --}}
                <x-select 
                    label="Type"
                    name="type"
                    id="type"
                    :options="$type_options"
                    required
                    showDefault
                    onchange="trackTypeState(this)"
                />
                
                <div class="col-span-full">
                    {{-- method --}}
                    <x-select 
                        label="Method"
                        name="method"
                        id="method"
                        :options="$method_options"
                        required
                        showDefault
                        onchange="trackMethodState(this)"
                    />
                    
                    <hr class="border-gray-600 my-3">

                    <div id="details" class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    </div>
                </div>
            </div>
        </div>
        <div class="w-full flex justify-end mt-4">
            <button type="submit"
                class="px-6 py-1 bg-[var(--bg-success)] border border-[var(--bg-success)] text-[var(--text-success)] font-medium text-nowrap rounded-lg hover:bg-[var(--h-bg-success)] transition-all 0.3s ease-in-out cursor-pointer">
                <i class='fas fa-save mr-1'></i> Save
            </button>
        </div>
    </form>

    <script>
        let customerSelectDom = document.getElementById('customer_id');
        let methodSelectDom = document.getElementById('method');
        let typeSelectDom = document.getElementById('type');
        let dateDom = document.getElementById('date');
        let balanceDom = document.getElementById('balance');
        let detailsDom = document.getElementById('details');

        selectedCustomerData = null;
        let selectedProgramData = {};
        
        let selectedCustomer;

        const today = new Date().toISOString().split('T')[0];

        function trackCustomerState() {
            typeSelectDom.options[2].dataset.option = '';
            dateDom.value = '';
            balanceDom.value = '';
            methodSelectDom.value = '';
            typeSelectDom.value = '';

            if (customerSelectDom.value != '') {
                selectedCustomer = JSON.parse(customerSelectDom.options[customerSelectDom.selectedIndex].dataset.option);
                dateDom.disabled = false;
                methodSelectDom.disabled = false;
                dateDom.min = selectedCustomer.date.toString().split('T')[0];
                dateDom.max = today;
                balanceDom.value = formatNumbersWithDigits(selectedCustomer.balance, 1, 1);
                selectedCustomerData = selectedCustomer;
                typeSelectDom.options[2].dataset.option = JSON.stringify(selectedCustomer.payment_programs) ?? '';
            } else {
                dateDom.disabled = true;
                methodSelectDom.disabled = true;
                typeSelectDom.options[2].dataset.option = '';
            }

            methodSelectDom.querySelector("option[value='program']")?.remove();
        }

        window.addEventListener('DOMContentLoaded', () => {
            const url = new URL(window.location.href);

            // Clean the URL after initial load (remove query params)
            if (url.searchParams.has('program_id') || url.searchParams.has('source')) {
                // reset url
                url.search = ''; // remove all query parameters
                window.history.replaceState({}, document.title, url.toString());

                // select customer
                for (const option of customerSelectDom.options) {
                    if (option.value.trim() !== '') {
                        customerSelectDom.value = option.value;
                        break; 
                    }
                }
                trackCustomerState();

                // set date today
                const today = new Date();
                const yyyy = today.getFullYear();
                const mm = String(today.getMonth() + 1).padStart(2, '0');
                const dd = String(today.getDate()).padStart(2, '0');
                dateDom.value = `${yyyy}-${mm}-${dd}`;

                // select type
                for (const option of typeSelectDom.options) {
                    if (option.value.trim() === 'payment_program') {
                        option.dataset.option = JSON.stringify([selectedCustomer.payment_programs]);
                        typeSelectDom.value = option.value;
                        break; 
                    }
                }

                trackTypeState(typeSelectDom, true);
            }
        });

        function trackTypeState(elem, isNoModal) {
            methodSelectDom.value = '';
            if (elem.value == 'payment_program') {
                methodSelectDom.innerHTML += `<option data-option="" value="program"> Program </option>`;
            } else {
                methodSelectDom.querySelector("option[value='program']")?.remove();
            }
        }

        function trackMethodState(elem) {
            if (elem.value == 'cash') {
                detailsDom.innerHTML = `
                    {{-- amount --}}
                    <x-input label="Amount" type="number" placeholder="Enter amount" name="amount" id="amount" required/>

                    {{-- remarks --}}
                    <x-input label="Remarks" placeholder="Remarks" name="remarks" id="remarks"/>
                `;
            } else if (elem.value == 'cheque') {
                detailsDom.innerHTML = `
                    {{-- bank --}}
                    <x-select label="Bank" name="bank_id" id="bank" :options="$banks_options" required showDefault />

                    {{-- amount --}}
                    <x-input label="Amount" type="number" placeholder="Enter amount" name="amount" id="amount" required/>

                    {{-- cheque_date --}}
                    <x-input label="Cheque Date" type="date" name="cheque_date" id="cheque_date" required/>

                    {{-- cheque_no --}}
                    <x-input label="Cheque No" placeholder="Enter cheque no" name="cheque_no" id="cheque_no" required/>

                    {{-- remarks --}}
                    <x-input label="Remarks" placeholder="Remarks" name="remarks" id="remarks"/>

                    {{-- clear_date --}}
                    <x-input label="Clear Date" type="date" name="clear_date" id="clear_date"/>
                `;
            } else if (elem.value == 'slip') {
                detailsDom.innerHTML = `
                    {{-- customer --}}
                    <x-input label="Customer" placeholder="Enter Customer" name="customer" id="customer" value="${selectedCustomer.customer_name}" disabled required/>

                    {{-- amount --}}
                    <x-input label="Amount" type="number" placeholder="Enter amount" name="amount" id="amount" required/>

                    {{-- slip_date --}}
                    <x-input label="Slip Date" type="date" name="slip_date" id="slip_date" required/>

                    {{-- slip_no --}}
                    <x-input label="Slip No" placeholder="Enter cheque no" name="slip_no" id="slip_no" required/>

                    {{-- remarks --}}
                    <x-input label="Remarks" placeholder="Remarks" name="remarks" id="remarks"/>

                    {{-- clear_date --}}
                    <x-input label="Clear Date" type="date" name="clear_date" id="clear_date" required/>
                `;
            } else if (elem.value == 'adjustment') {
                detailsDom.innerHTML = `
                    {{-- amount --}}
                    <x-input label="Amount" type="number" placeholder="Enter amount" name="amount" id="amount" required/>

                    {{-- remarks --}}
                    <x-input label="Remarks" placeholder="Remarks" name="remarks" id="remarks"/>
                `;
            } else if (elem.value == 'program') {
                let allProgramsArray = JSON.parse(typeSelectDom.options[typeSelectDom.selectedIndex].dataset.option);
                
                detailsDom.innerHTML = `
                    <div class="col-span-full">
                        {{-- payment_programs --}}
                        <x-select 
                            label="Payment Programs"
                            name="program_id"
                            id="payment_programs"
                            required
                            onchange="trackProgramState(this)"
                        />
                    </div>
                `;

                const programSelectDom = document.getElementById('payment_programs');
                if (allProgramsArray.length > 0) {
                    programSelectDom.disabled = false;
                    programSelectDom.innerHTML = '<option value="" >-- Select payment program --</option>';
                    allProgramsArray.forEach(program => {
                        programSelectDom.innerHTML += `<option value="${program.id}" data-option='${JSON.stringify(program)}' >${program.program_no ?? program.order_no}</option>`;
                    });
                } else {
                    programSelectDom.disabled = false;
                    programSelectDom.innerHTML = `<option value="">-- No options avalaible --</option>`;
                }
            }
        }
        
        function trackProgramState(elem) {
            while (details.children.length > 1) {
                details.removeChild(details.lastChild);
            }

            selectedProgramData = JSON.parse(elem.options[elem.selectedIndex].dataset.option);

            if (selectedProgramData.category != 'waiting') {
                let beneficiary = '-';
                if (selectedProgramData.category) {
                    if (selectedProgramData.category === 'supplier' && selectedProgramData.sub_category?.supplier_name) {
                        beneficiary = selectedProgramData.sub_category.supplier_name;
                    } else if (selectedProgramData.category === 'customer' && selectedProgramData.sub_category?.customer_name) {
                        beneficiary = selectedProgramData.sub_category.customer_name;
                    } else if (selectedProgramData.category === 'self_account' && selectedProgramData.sub_category?.account_title) {
                        beneficiary = selectedProgramData.sub_category.account_title;
                    } else if (selectedProgramData.category === 'waiting' && selectedProgramData.remarks) {
                        beneficiary = selectedProgramData.remarks;
                    }
                }
                selectedProgramData.beneficiary = beneficiary
            }

            detailsDom.innerHTML += `
                {{-- category --}}
                <x-input label="Category" value="${selectedProgramData.category}" disabled/>
                
                {{-- beneficiary --}}
                <x-input label="Beneficiary" value="${selectedProgramData.beneficiary}" disabled/>

                {{-- program date --}}
                <x-input label="Program Date" value="${selectedProgramData.date}" disabled/>

                {{-- program amount --}}
                <x-input label="Program Balance" type="number" value="${selectedProgramData.balance}" disabled/>

                {{-- amount --}}
                <x-input label="Amount" type="number" placeholder="Enter amount" name="amount" id="amount" required/>
                
                {{-- bank account --}}
                <x-select label="Bank Accounts" name="bank_account_id" id="bank_accounts" required showDefault />
                
                {{-- transaction id --}}
                <x-input label="Transaction Id" name="transaction_id" id="transaction_id" placeholder="Enter Transaction Id" required />

                {{-- remarks --}}
                <x-input label="Remarks" placeholder="Remarks" name="remarks" id="remarks"/>
            `;

            let bankAccountData = selectedProgramData.sub_category.bank_accounts;
            
            if (bankAccountData) {
                let bankAccountsSelect = document.getElementById('bank_accounts');
                bankAccountsSelect.disabled = false;
                bankAccountsSelect.innerHTML = '<option value="">-- Select Bank Account --</option>';
                if (bankAccountData.length > 0) {
                    bankAccountData.forEach(account => {
                        bankAccountsSelect.innerHTML += `<option value="${account.id}">${account.account_title} | ${account.bank.short_title}</option>`;
                    });
                } else {
                    bankAccountsSelect.innerHTML += `<option value="${bankAccountData.id}">${bankAccountData.account_title} | ${bankAccountData.bank.short_title}</option>`;
                }
            }
        }
    </script>
@endsection
