@extends('app')
@section('title', 'Add Payment | ' . app('company')->name)
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
    <!-- Modal -->
    <div id="modal"
        class="hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-[var(--overlay-color)] fade-in">
        <x-modal id="modalForm" classForBody="p-4" closeAction="closeModal">
            <div class="flex items-start relative h-full">
                <div id="paymentDetails" class="flex-1 overflow-y-auto my-scrollbar-2">
                </div>
            </div>
            <!-- Modal Action Slot -->
            <x-slot name="actions">
                <button onclick="closeModal()" type="button"
                    class="px-4 py-2 bg-[var(--secondary-bg-color)] border border-gray-600 text-[var(--secondary-text)] rounded-lg hover:bg-[var(--h-bg-color)] transition-all duration-300 ease-in-out">
                    Close
                </button>
                <button onclick="addPaymentDetails()" type="button"
                    class="px-4 py-2 bg-[var(--bg-success)] border border-[var(--bg-success)] text-[var(--text-success)] font-medium rounded-lg hover:bg-[var(--h-bg-success)] transition-all duration-300 ease-in-out">
                    Add
                </button>
            </x-slot>
        </x-modal>
    </div>
    <!-- Payment Program Modal -->
    <div id="paymentpaProgramModal"
        class="hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-[var(--overlay-color)] fade-in">
        <x-modal id="paymentProgramsModalForm" classForBody="p-5 max-w-6xl h-[45rem]" closeAction="closeProgramModal">
            <div class="flex items-start relative h-full">
                <div id="paymentProgramsContainer" class="flex-1 h-full overflow-y-auto my-scrollbar-2 flex flex-col">
                </div>
            </div>
            <!-- Modal Action Slot -->
            <x-slot name="actions">
                <button onclick="closeProgramModal()" type="button"
                    class="px-4 py-2 bg-[var(--secondary-bg-color)] border border-gray-600 text-[var(--secondary-text)] rounded-lg hover:bg-[var(--h-bg-color)] transition-all duration-300 ease-in-out">
                    Close
                </button>
            </x-slot>
        </x-modal>
    </div>

    <!-- Progress Bar -->
    <div class="mb-5 max-w-3xl mx-auto">
        <x-search-header heading="Add Payment" link linkText="Show Payments" linkHref="{{ route('payments.index') }}"/>
        <x-progress-bar :steps="['Select Customer', 'Enter Payment']" :currentStep="1" />
    </div>

    <!-- Form -->
    <form id="form" action="{{ route('payments.store') }}" method="post"
        class="bg-[var(--secondary-bg-color)] text-sm rounded-xl shadow-lg p-8 border border-[var(--h-bg-color)] pt-12 max-w-3xl mx-auto  relative overflow-hidden">
        @csrf
        <x-form-title-bar title="Add Payment" />

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
                    showDefault
                    onchange="trackTypeState(this)" 
                />
            </div>
        </div>

        <div class="step2 space-y-4 hidden">
            <div class="flex flex-col space-y-4 gap-4">
                {{-- method --}}
                <x-select 
                    label="Method"
                    id="method"
                    :options="$method_options"
                    required
                    showDefault
                    onchange="trackMethodState(this)"
                    withButton
                    btnId="enterDetailsBtn"
                    btnText="Enter Details"
                    btnOnclick="trackMethodState(this.previousElementSibling)"
                />
            </div>
            {{-- payment showing --}}
            <div id="payment-table" class="w-full text-left text-sm">
                <div class="flex justify-between items-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4 mb-4">
                    <div class="w-[7%]">S.No</div>
                    <div class="w-1/5">Method</div>
                    <div class="w-1/5">Remarks</div>
                    <div class="w-1/5">Amount</div>
                    <div class="w-[10%] text-center">Action</div>
                </div>
                <div id="payment-list" class="h-[20rem] overflow-y-auto my-scrollbar-2">
                    <div class="text-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4">No Payment Added</div>
                </div>
                <input type="hidden" name="payment_details_array" id="payment_details_array">
            </div>

            <input type="hidden" name="program_id" id="programInp">
            <div class="flex w-full text-sm mt-5 text-nowrap">
                <div class="total-payment flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                    <div class="grow">Total Payment - Rs.</div>
                    <div id="finalTotalPayment">0</div>
                </div>
            </div>
        </div>
    </form>

    <script>
        const modalDom = document.getElementById("modal");
        let customerSelectDom = document.getElementById('customer_id');
        let methodSelectDom = document.getElementById('method');
        let typeSelectDom = document.getElementById('type');
        let dateDom = document.getElementById('date');
        let balanceDom = document.getElementById('balance');
        let paymentDetailsDom = document.getElementById('paymentDetails');
        let finalTotalPaymentDom = document.getElementById('finalTotalPayment');
        let paymentListDom = document.getElementById('payment-list');
        const paymentProgramsContainer = document.getElementById("paymentProgramsContainer");
        const programInpDom = document.getElementById("programInp");
        const paymentDetailsArrayDom = document.getElementById("payment_details_array");

        selectedCustomerData = null;
        let totalPayment = 0;
        let selectedProgramData;
        
        let paymentDetailsArray = [];
        let isModalOpened = false;
        let isProgramModalOpened = false;

        function openModal() {
            isModalOpened = true;
            closeAllDropdowns();
            document.getElementById('modal').classList.remove('hidden');
        }

        function closeModal() {
            isModalOpened = false;
            let modal = document.getElementById('modal');
            modal.classList.add('fade-out');

            modal.addEventListener('animationend', () => {
                modal.classList.add('hidden');
                modal.classList.remove('fade-out');
            }, {
                once: true
            });
        }

        document.addEventListener('mousedown', (e) => {
            const {
                id
            } = e.target;
            if (id === 'modalForm') {
                closeModal();
            } else if (id == "paymentProgramsModalForm") {
                closeProgramModal();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && isModalOpened) {
                closeModal();
                closeProgramModal();
            }
        });
        
        let selectedCustomer;

        const today = new Date().toISOString().split('T')[0];

        function trackCustomerState() {
            typeSelectDom.options[2].dataset.option = '';
            dateDom.value = '';
            balanceDom.value = '';
            methodSelectDom.value = '';
            typeSelectDom.value = '';

            paymentDetailsArray = [];
            renderList();

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
                        option.dataset.option = JSON.stringify(selectedCustomer.payment_programs);
                        typeSelectDom.value = option.value;
                        break; 
                    }
                }
                trackTypeState(typeSelectDom, true);

                // select Program
                selectThisProgram(selectedCustomer.payment_programs)
            }
        });

        function trackTypeState(elem, isNoModal) {
            paymentDetailsArray = [];
            methodSelectDom.value = '';
            methodSelectDom.querySelector("option[value='program']")?.remove();
            renderList();

            if (elem.value != '' && elem.value != 'payment_program') {
                gotoStep(2);
            }
            
            if (elem.value == "payment_program" && !isNoModal) {
                generatePaymentProgramModal(elem.options[elem.selectedIndex]);
            }

            if (isNoModal) {
                gotoStep(2);
            }
        }

        function generatePaymentProgramModal(item) {
            let programs = JSON.parse(item.dataset.option);
            let programHTML;

            if (programs.length > 0) {
                programHTML = `
                    <x-search-header heading="Payment Programs"/>
                    <div class='overflow-y-auto my-scrollbar-2 pt-2 grow'>
                        <div class="card_container grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                            ${programs.map((program) => {
                                let beneficiary = '-';
                                if (program.category) {
                                    if (program.category === 'supplier' && program.sub_category?.supplier_name) {
                                        beneficiary = program.sub_category.supplier_name;
                                    } else if (program.category === 'customer' && program.sub_category?.customer_name) {
                                        beneficiary = program.sub_category.customer_name;
                                    } else if (program.category === 'self_account' && program.sub_category?.account_title) {
                                        beneficiary = program.sub_category.account_title;
                                    } else if (program.category === 'waiting' && program.remarks) {
                                        beneficiary = program.remarks;
                                    }
                                }
                                return `
                                    <div data-json='${JSON.stringify(program)}' id='${program.id}' onclick='selectThisProgram(JSON.parse(this.dataset.json))'
                                        class="contextMenuToggle modalToggle card relative border border-gray-600 shadow rounded-xl min-w-[100px] flex gap-4 py-4 px-5 cursor-pointer overflow-hidden fade-in">
                                        <div>
                                            <ul class="text-sm">
                                                <li class="capitalize"><strong>Date:</strong> ${program.date}</li>
                                                <li class="capitalize"><strong>Category:</strong> ${program.category}</li>
                                                <li><strong>Beneficiary:</strong> ${beneficiary}</li>
                                                <li><strong>Amount:</strong> ${formatNumbersWithDigits(program.amount, 1, 1)}</li>
                                            </ul>
                                        </div>
                                        <button type="button"
                                            class="absolute bottom-0 right-0 rounded-full w-[25%] aspect-square flex items-center justify-center bg-[var(--h-bg-color)] text-lg translate-x-1/4 translate-y-1/4 transition-all duration-200 ease-in-out cursor-pointer">
                                            <i class='fas fa-arrow-right text-2xl -rotate-45'></i>
                                        </button>
                                    </div>
                                `;
                            }).join('')}
                        </div>
                    </div>
                `;
                gotoStep(2)
            } else {
                programHTML = `
                    <x-search-header heading="Payment Programs"/>
                    <div class='overflow-y-auto my-scrollbar-2 pt-2 grow'>
                        <div class="text-center text-[var(--border-error)]">Program Not Found.</div>
                    </div>
                `;
            }

            paymentProgramsContainer.innerHTML = programHTML;

            openProgramModal();
        }
        
        function openProgramModal() {
            isProgramModalOpened = true;
            closeAllDropdowns();
            document.getElementById('paymentpaProgramModal').classList.remove('hidden');
        }

        function closeProgramModal() {
            isProgramModalOpened = false;
            let modal = document.getElementById('paymentpaProgramModal');
            modal.classList.add('fade-out');

            modal.addEventListener('animationend', () => {
                modal.classList.add('hidden');
                modal.classList.remove('fade-out');
            }, {
                once: true
            });
        }

        const enterDetailsBtn = document.getElementById("enterDetailsBtn");
        enterDetailsBtn.disabled = true;

        function trackMethodState(elem) {
            if (elem.value != '') {
                enterDetailsBtn.disabled = false;

                paymentDetailsDom.innerHTML = `
                    <x-search-header heading="${elem.value}"/>
                `;
            } else {
                paymentDetailsDom.innerHTML = '';
                
                enterDetailsBtn.disabled = true;
            }
            
            if (elem.value == 'cash') {
                paymentDetailsDom.innerHTML += `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-1">
                        {{-- amount --}}
                        <x-input label="Amount" type="number" placeholder="Enter amount" name="amount" id="amount" required/>

                        {{-- remarks --}}
                        <x-input label="Remarks" placeholder="Remarks" name="remarks" id="remarks" required/>
                    </div>
                `;
            } else if (elem.value == 'cheque') {
                paymentDetailsDom.innerHTML += `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-1">
                        {{-- bank --}}
                        <x-select label="Bank" name="bank" id="bank" :options="$banks_options" required showDefault />

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
                    </div>
                `;
            } else if (elem.value == 'slip') {
                paymentDetailsDom.innerHTML += `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-1">
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
                    </div>
                `;
            } else if (elem.value == 'adjustment') {
                paymentDetailsDom.innerHTML += `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-1">
                        {{-- amount --}}
                        <x-input label="Amount" type="number" placeholder="Enter amount" name="amount" id="amount" required/>

                        {{-- remarks --}}
                        <x-input label="Remarks" placeholder="Remarks" name="remarks" id="remarks" required/>
                    </div>
                `;
            } else if (elem.value == 'program') {
                paymentDetailsDom.innerHTML += `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-1">
                        {{-- category --}}
                        <x-input label="Category" value="${selectedProgramData.category}" id="category" disabled required/>
                        
                        {{-- beneficiary --}}
                        <x-input label="Beneficiary" value="${selectedProgramData.beneficiary}" id="beneficiary" disabled required/>

                        {{-- program date --}}
                        <x-input label="Program Date" value="${selectedProgramData.date}" id="program_date" disabled required/>

                        {{-- program amount --}}
                        <x-input label="Program Amount" type="number" value="${selectedProgramData.amount}" id="program_amount" disabled required/>

                        {{-- amount --}}
                        <x-input label="Amount" type="number" placeholder="Enter amount" name="amount" id="amount" required/>
                        
                        {{-- bank account --}}
                        <x-select label="Bank Accounts" name="bank_account_id" id="bank_accounts" required showDefault />
                        
                        {{-- transaction id --}}
                        <x-input label="Transaction Id" name="transaction_id" id="transaction_id" placeholder="Enter Transaction Id" required />

                        {{-- remarks --}}
                        <x-input label="Remarks" placeholder="Remarks" name="remarks" id="remarks" required/>
                    </div>
                `;
            } else {
                paymentDetailsDom.innerHTML += `
                    <div class="text-center text-[var(--border-error)]">Select Payment Type.</div>
                `;
            }
            
            if (elem.value != '') {
                gotoStep(2)
                openModal()
            }
        }

        function selectThisProgram(programDate) {
            closeProgramModal();
            selectedProgramData = programDate;
            programInpDom.value = selectedProgramData.id;
            if (selectedProgramData.category != 'waiting') {
                methodSelectDom.innerHTML += `<option data-option="" value="program"> Program </option>`;
                methodSelectDom.value = 'program';
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
                trackMethodState(methodSelectDom);
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
        }

        function addPaymentDetails() {
            closeModal();
            let detail = {};
            const inputs = paymentDetailsDom.querySelectorAll('input:not([disabled])');

            inputs.forEach(input => {
                const name = input.getAttribute('name');
                const value = input.value;
                
                if (name == "amount") {
                    detail[name] = parseInt(value);
                } else {
                    detail[name] = value;
                }
            });

            const selectBankAccount = paymentDetailsDom.querySelector("select");
            if (selectBankAccount) {
                detail[selectBankAccount.getAttribute('name')] = selectBankAccount.value;
            }

            if (isNaN(detail.amount) || detail.amount <= 0) {
                detail = {};
            }
            
            if (Object.keys(detail).length > 0) {
                totalPayment += detail.amount;
                detail['method'] = methodSelectDom.value;
                paymentDetailsArray.push(detail);
                renderList();
            }
        }

        function renderList() {
            if (paymentDetailsArray.length > 0) {
                let clutter = "";
                paymentDetailsArray.forEach((paymentDetail, index) => {
                    clutter += `
                        <div class="flex justify-between items-center border-t border-gray-600 py-3 px-4">
                            <div class="w-[7%]">${index+1}</div>
                            <div class="w-1/5 capitalize">${paymentDetail.method}</div>
                            <div class="w-1/5 capitalize">${paymentDetail.remarks && paymentDetail.remarks.trim() !== '' ? paymentDetail.remarks : '-'}</div>
                            <div class="w-1/5">${formatNumbersWithDigits(paymentDetail.amount, 1, 1)}</div>
                            <div class="w-[10%] text-center">
                                <button onclick="deselectThisPayment(${index})" type="button" class="text-[var(--danger-color)] text-xs px-2 py-1 rounded-lg hover:text-[var(--h-danger-color)] transition-all duration-300 ease-in-out">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    `;
                });

                paymentListDom.innerHTML = clutter;

                paymentDetailsArrayDom.value = JSON.stringify(paymentDetailsArray);
            } else {
                paymentListDom.innerHTML =
                `<div class="text-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4">No Payment Yet</div>`;
            }
            finalTotalPaymentDom.textContent = formatNumbersWithDigits(totalPayment, 1, 1);
        }

        function deselectThisPayment(index) {
            totalPayment -= paymentDetailsArray[index].amount;
            paymentDetailsArray.splice(index, 1);
            renderList();
        }
        
        function validateForNextStep() {
            return true;
        }
    </script>
@endsection
