@extends('app')
@section('title', 'Add Payment | ' . app('company')->name)
@section('content')
    @php
        $type_options = [
            'cash' => ['text' => 'Cash'],
            'cheque' => ['text' => 'Cheque'],
            'slip' => ['text' => 'Slip'],
            'online' => ['text' => 'Online'],
            'payment_program' => ['text' => 'Payment Program'],
            'adjustment' => ['text' => 'Adjustment'],
        ];
    @endphp
    <!-- Main Content -->
    <h1 class="text-3xl font-bold mb-6 text-center text-[--primary-color] fade-in"> Add Payment </h1>

    <!-- Progress Bar -->
    <div class="mb-5 max-w-3xl mx-auto">
        <x-progress-bar :steps="['Select Customer', 'Enter Payment']" :currentStep="1" />
    </div>

    <!-- Form -->
    <form id="form" action="{{ route('payments.store') }}" method="post"
        class="bg-[--secondary-bg-color] text-sm rounded-xl shadow-lg p-8 border border-[--h-bg-color] pt-12 max-w-3xl mx-auto  relative overflow-hidden">
        @csrf
        <div
            class="form-title text-center absolute top-0 left-0 w-full bg-[--primary-color] py-1 capitalize tracking-wide font-medium text-sm">
            <h4>Add Payment</h4>
        </div>

        <div class="step1 space-y-4 ">
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
                    disabled
                    showDefault
                    onchange="trackTypeState(this)" 
                />
            </div>
        </div>

        <div class="step2 space-y-4 hidden">
            <div id="paymentDetails" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="col-span-full text-center text-[--border-error]">Select Payment Type.</div>
            </div>
        </div>
    </form>

    <script>
        let customerSelectDom = document.getElementById('customer_id');
        let typeSelectDom = document.getElementById('type');
        let dateDom = document.getElementById('date');
        let balanceDom = document.getElementById('balance');
        let paymentDetailsDom = document.getElementById('paymentDetails');
        let selectedCustomer;

        const today = new Date().toISOString().split('T')[0];

        function trackCustomerState() {
            dateDom.value = '';
            balanceDom.value = '';
            typeSelectDom.value = '';
            paymentDetailsDom.innerHTML = `
                <div class="col-span-full text-center text-[--border-error]">Select Payment Type.</div>
            `;

            if (customerSelectDom.value != '') {
                selectedCustomer = JSON.parse(customerSelectDom.options[customerSelectDom.selectedIndex].dataset.option);
                dateDom.disabled = false;
                typeSelectDom.disabled = false;
                dateDom.min = selectedCustomer.date;
                dateDom.max = today;
                balanceDom.value = formatNumbersWithDigits(selectedCustomer.balance, 1, 1);
            } else {
                dateDom.disabled = true;
                typeSelectDom.disabled = true;
            }
        }

        function trackTypeState(elem) {
            if (elem.value == 'cash') {
                paymentDetailsDom.innerHTML = `
                    {{-- amount --}}
                    <x-input label="Amount" type="number" placeholder="Enter amount" name="amount" id="amount" required/>

                    {{-- remarks --}}
                    <x-input label="Remarks" placeholder="Remarks" name="remarks" id="remarks" required/>
                `;
            } else if (elem.value == 'cheque') {
                paymentDetailsDom.innerHTML = `
                    {{-- bank --}}
                    <x-input label="Bank" placeholder="Enter Bank" name="bank" id="bank" required/>

                    {{-- amount --}}
                    <x-input label="Amount" type="number" placeholder="Enter amount" name="amount" id="amount" required/>

                    {{-- cheque_date --}}
                    <x-input label="Cheque Date" type="date" name="cheque_date" id="cheque_date" required/>

                    {{-- cheque_no --}}
                    <x-input label="Cheque No" placeholder="Enter cheque no" name="cheque_no" id="cheque_no" required/>

                    {{-- remarks --}}
                    <x-input label="Remarks" placeholder="Remarks" name="remarks" id="remarks" required/>

                    {{-- clear_date --}}
                    <x-input label="Clear Date" type="date" name="clear_date" id="clear_date" required/>
                `;
            } else if (elem.value == 'slip') {
                paymentDetailsDom.innerHTML = `
                    {{-- customer --}}
                    <x-input label="Customer" placeholder="Enter Customer" name="customer" id="customer" value="${selectedCustomer.customer_name}" disabled required/>

                    {{-- amount --}}
                    <x-input label="Amount" type="number" placeholder="Enter amount" name="amount" id="amount" required/>

                    {{-- slip_date --}}
                    <x-input label="Slip Date" type="date" name="slip_date" id="slip_date" required/>

                    {{-- slip_no --}}
                    <x-input label="Slip No" placeholder="Enter cheque no" name="slip_no" id="slip_no" required/>

                    {{-- remarks --}}
                    <x-input label="Remarks" placeholder="Remarks" name="remarks" id="remarks" required/>

                    {{-- clear_date --}}
                    <x-input label="Clear Date" type="date" name="clear_date" id="clear_date" required/>
                `;
            } else if (elem.value == 'online') {
                paymentDetailsDom.innerHTML = `
                    {{-- account_title --}}
                    <x-input label="A/C Title" type="number" placeholder="Enter account title" name="account_title" id="account_title" required/>
                    
                    {{-- amount --}}
                    <x-input label="Amount" type="number" placeholder="Enter amount" name="amount" id="amount" required/>

                    {{-- transition_id --}}
                    <x-input label="Transition Id" placeholder="Enter cheque no" name="transition_id" id="transition_id" required/>

                    {{-- bank --}}
                    <x-input label="Bank" placeholder="Enter Bank" name="bank" id="bank" required/>

                    <div class="col-span-full">
                        {{-- remarks --}}
                        <x-input label="Remarks" placeholder="Remarks" name="remarks" id="remarks" required/>
                    </div>
                `;
            }  else if (elem.value == 'payment_program') {
                paymentDetailsDom.innerHTML = `
                    {{-- program_no --}}
                    <x-input label="Program No." type="number" placeholder="Enter program no." name="program_no" id="program_no" required/>
                    
                    {{-- category --}}
                    <x-input label="Category" placeholder="category" name="category" id="category" required/>
                    
                    {{-- sub_category --}}
                    <x-input label="Sub Category" placeholder="Sub category" name="sub_category" id="sub_category" required/>
                    
                    {{-- bank_accounts --}}
                    <x-select 
                        label="Bank Accounts"
                        name="bank_account_id"
                        id="bank_accounts"
                        required
                        disabled
                    />

                    {{-- program_amount --}}
                    <x-input label="Program Amount" type="number" placeholder="Program amount" name="program_amount" id="program_amount" required/>

                    {{-- payment_amount --}}
                    <x-input label="Payment Amount" type="number" placeholder="Enter payment amount" name="amount" id="payment_amount" required/>

                    {{-- transition_id --}}
                    <x-input label="Transition Id" placeholder="Enter cheque no" name="transition_id" id="transition_id" required/>

                    {{-- remarks --}}
                    <x-input label="Remarks" placeholder="Remarks" name="remarks" id="remarks" required/>
                `;
                addListnerToProgramNo();
            } else if (elem.value == 'adjustment') {
                paymentDetailsDom.innerHTML = `
                    {{-- amount --}}
                    <x-input label="Amount" type="number" placeholder="Enter amount" name="amount" id="amount" required/>

                    {{-- remarks --}}
                    <x-input label="Remarks" placeholder="Remarks" name="remarks" id="remarks" required/>
                `;
            } else {
                paymentDetailsDom.innerHTML = `
                    <div class="col-span-full text-center text-[--border-error]">Select Payment Type.</div>
                `;
            }
            
            if (elem.value != '') {
                gotoStep(2)
            }
        }

        
        let programNoDom;
        let categoryDom;
        let subCategoryDom;
        let bankAccountsSelectDom;
        let programAmountDom;

        function addListnerToProgramNo() {
            programNoDom = document.getElementById('program_no');
            categoryDom = document.getElementById('category');
            subCategoryDom = document.getElementById('sub_category');
            bankAccountsSelectDom = document.getElementById('bank_accounts');
            programAmountDom = document.getElementById('program_amount');

            programNoDom.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    let programNo = programNoDom.value;
                    let customerId = customerSelectDom.value;
                    if (programNo != '') {
                        $.ajax({
                            url: "{{ route('get-program-details') }}",
                            type: 'POST',
                            data: {
                                program_no: programNo,
                                customer_id: customerId,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                console.log(response);
                                if (response.status === 'success') {
                                    categoryDom.value = response.data.category;
                                    categoryDom.disabled = true;

                                    switch (response.data.category) {
                                        case 'self_account':
                                            subCategoryDom.value = 'Self Account';
                                            subCategoryDom.disabled = true;

                                            bankAccountsSelectDom.innerHTML = '';

                                            const option = document.createElement('option');
                                            option.value = response.data.sub_category.id;
                                            option.textContent = response.data.sub_category.account_title + ' | ' + response.data.sub_category.bank.short_title;

                                            bankAccountsSelectDom.appendChild(option);
                                            bankAccountsSelectDom.disabled = false;
                                            break;
                                        case 'supplier':
                                            subCategoryDom.value = response.data.sub_category.supplier_name;
                                            subCategoryDom.disabled = true;

                                            bankAccountsSelectDom.innerHTML = '';

                                            if (response.data.bank_accounts.length > 0) {
                                                response.data.bank_accounts.forEach(function(account) {
                                                    const option = document.createElement('option');
                                                    option.value = account.id;
                                                    option.textContent = account.account_title + ' | ' + account.bank.short_title;
                                                    bankAccountsSelectDom.appendChild(option);
                                                });
                                                bankAccountsSelectDom.disabled = false;
                                            } else {
                                                const option = document.createElement('option');
                                                option.value = '';
                                                option.textContent = 'No Bank Accounts';
                                                bankAccountsSelectDom.appendChild(option);
                                                bankAccountsSelectDom.disabled = true;
                                            }
                                            
                                            break;
                                        case 'customer':
                                            subCategoryDom.value = response.data.sub_category.customer_name;
                                            subCategoryDom.disabled = true;

                                            bankAccountsSelectDom.innerHTML = '';

                                            if (response.data.bank_accounts.length > 0) {
                                                response.data.bank_accounts.forEach(function(account) {
                                                    const option = document.createElement('option');
                                                    option.value = account.id;
                                                    option.textContent = account.account_title + ' | ' + account.bank.short_title;
                                                    bankAccountsSelectDom.appendChild(option);
                                                });
                                                bankAccountsSelectDom.disabled = false;
                                            } else {
                                                const option = document.createElement('option');
                                                option.value = '';
                                                option.textContent = 'No Bank Accounts';
                                                bankAccountsSelectDom.appendChild(option);
                                                bankAccountsSelectDom.disabled = true;
                                            }
                                            
                                            break;
                                        case 'wating':
                                            subCategoryDom.value = 'wating';
                                            subCategoryDom.disabled = true;
                                            break;
                                        default:
                                            subCategoryDom.value = '';
                                            subCategoryDom.disabled = false;
                                            bankAccountsSelectDom.value = '';
                                    }
                                    
                                    programAmountDom.value = response.data.amount;
                                    programAmountDom.disabled = true;
                                } else {
                                    setDefaultValues();
                                }
                            },
                            error: function() {
                                setDefaultValues();
                            }
                        });
                    } else {
                        setDefaultValues();
                    }
                }
            });
        }

        function setDefaultValues() {
            categoryDom.value = '';
            categoryDom.disabled = false;
            subCategoryDom.value = '';
            subCategoryDom.disabled = false;
            bankAccountsSelectDom.innerHTML = '';
            const option = document.createElement('option');
            option.value = '';
            option.textContent = '-- No options available --';
            bankAccountsSelectDom.appendChild(option);
            bankAccountsSelectDom.disabled = true;
            programAmountDom.value = '';
            programAmountDom.disabled = false;
        }

        function validateForNextStep() {
            return true;
        }
    </script>
@endsection
