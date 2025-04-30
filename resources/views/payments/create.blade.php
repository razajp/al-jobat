@extends('app')
@section('title', 'Add Payment | ' . app('company')->name)
@section('content')
    @php
        $method_options = [
            'cash' => ['text' => 'Cash'],
            'cheque' => ['text' => 'Cheque'],
            'slip' => ['text' => 'Slip'],
            'online' => ['text' => 'Online'],
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
                    class="px-4 py-2 bg-[var(--secondary-bg-color)] border border-gray-600 text-[var(--secondary-text)] rounded-lg hover:bg-[var(--h-bg-color)] transition-all 0.3s ease-in-out">
                    Close
                </button>
                <button type="button"
                    class="px-4 py-2 bg-[var(--bg-success)] border border-[var(--bg-success)] text-[var(--text-success)] font-medium rounded-lg hover:bg-[var(--h-bg-success)] transition-all 0.3s ease-in-out">
                    Add
                </button>
            </x-slot>
        </x-modal>
    </div>
    <!-- Payment Program Modal -->
    <div id="paymentProgramModal"
        class="hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-[var(--overlay-color)] fade-in">
        <x-modal id="paymentProgramModalForm" classForBody="p-5 max-w-6xl h-[45rem]" closeAction="closePaymentProgramModal">
            <div class="flex items-start relative h-full">
                <div class="flex-1 h-full overflow-y-auto my-scrollbar-2 flex flex-col">
                </div>
            </div>
        </x-modal>
    </div>
    <!-- Main Content -->
    <h1 class="text-3xl font-bold mb-6 text-center text-[var(--primary-color)] fade-in"> Add Payment </h1>

    <!-- Progress Bar -->
    <div class="mb-5 max-w-3xl mx-auto">
        <x-progress-bar :steps="['Select Customer', 'Enter Payment']" :currentStep="1" />
    </div>

    <!-- Form -->
    <form id="form" action="{{ route('payments.store') }}" method="post"
        class="bg-[var(--secondary-bg-color)] text-sm rounded-xl shadow-lg p-8 border border-[var(--h-bg-color)] pt-12 max-w-3xl mx-auto  relative overflow-hidden">
        @csrf
        <div
            class="form-title text-center absolute top-0 left-0 w-full bg-[var(--primary-color)] py-1 capitalize tracking-wide font-medium text-sm">
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
                    name="method"
                    id="method"
                    :options="$method_options"
                    required
                    showDefault
                    onchange="trackMethodState(this)" 
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
            </div>

            <div class="flex w-full grid grid-cols-1 md:grid-cols-3 gap-3 text-sm mt-5 text-nowrap">
                <div class="total-qty flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                    <div class="grow">Total Quantity - Pcs</div>
                    <div id="finalOrderedQuantity">0</div>
                </div>
                <div class="final flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                    <div class="grow">Total Amount - Rs.</div>
                    <div id="finalOrderAmount">0.0</div>
                </div>
                <div class="final flex justify-between items-center bg-[var(--h-bg-color)] border border-gray-600 rounded-lg py-2 px-4 w-full">
                    <label for="discount" class="grow">Discount - %</label>
                    <input type="text" name="discount" id="discount" value="0"
                        class="text-right bg-transparent outline-none w-1/2 border-none" />
                </div>
                <div class="total-qty flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                    <div class="grow">Previous Balance - Rs.</div>
                    <div id="finalPreviousBalance">0</div>
                </div>
                <div class="final flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                    <div class="grow">Net Amount - Rs.</div>
                    <input type="text" name="netAmount" id="finalNetAmount" value="0.0" readonly
                        class="text-right bg-transparent outline-none w-1/2 border-none" />
                </div>
                <div class="final flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                    <div class="grow">Current Balance - Rs.</div>
                    <div id="finalCurrentBalance">0.0</div>
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

        selectedCustomerData = null;
        
        let isModalOpened = false;

        function generateArticlesModal() {
            if (selectedCustomerData != null) {
                console.log(selectedCustomerData.payment_programs);
                
                let programHTML = '';

                if (selectedCustomerData.payment_programs.length > 0) {
                    programHTML = `
                        <div class='overflow-y-auto my-scrollbar-2 pt-2 grow'>
                            <div class="card_container grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                                ${selectedCustomerData.payment_programs.map((program) => {
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
                                        <div data-json='${JSON.stringify(program)}' id='${program.id}' onclick='selectThisProgram(this)'
                                            class="contextMenuToggle modalToggle card relative border border-gray-600 shadow rounded-xl min-w-[100px] flex gap-4 py-4 px-5 cursor-pointer overflow-hidden fade-in">
                                            <div>
                                                <h3 class="text-xl font-bold text-white">${program.order_no ?? program.program_no ?? '-'}</h3>
                                                <ul class="text-sm">
                                                    <li class="capitalize"><strong>Category:</strong> ${program.category}</li>
                                                    <li><strong>Beneficiary:</strong> ${beneficiary}</li>
                                                    <li><strong>Amount:</strong> ${formatNumbersWithDigits(program.amount, 1, 1)}</li>
                                                </ul>
                                            </div>
                                        </div>
                                    `;
                                }).join('')}
                            </div>
                        </div>
                    `;
                }

                modalDom.innerHTML = `
                    <x-modal id="articlesModalForm" classForBody="p-5 max-w-6xl h-[45rem]" closeAction="closeModal">
                        <div class="flex items-start relative h-full">
                            <div class="flex-1 h-full overflow-y-auto my-scrollbar-2 flex flex-col">
                                <h5 id="name" class="text-2xl my-1 text-[var(--text-color)] capitalize font-semibold">Articles</h5>
                                <hr class="border-gray-600 my-3">
                                ${programHTML}
                            </div>
                        </div>
                    </x-modal>
                `;

                openModal();
            }
        }

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
            if (id === 'articlesModalForm') {
                closeModal();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && isModalOpened) {
                closeModal();
            }
        });
        
        let selectedCustomer;

        const today = new Date().toISOString().split('T')[0];

        function trackCustomerState() {
            typeSelectDom.options[2].dataset.option = '';
            dateDom.value = '';
            balanceDom.value = '';
            methodSelectDom.value = '';

            if (customerSelectDom.value != '') {
                selectedCustomer = JSON.parse(customerSelectDom.options[customerSelectDom.selectedIndex].dataset.option);
                dateDom.disabled = false;
                methodSelectDom.disabled = false;
                dateDom.min = selectedCustomer.date;
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

        function trackTypeState(elem) {
            if (elem.value != '') {
                gotoStep(2)
            }
        }

        function trackMethodState(elem) {
            if (elem.value != '') {
                paymentDetailsDom.innerHTML = `
                    <x-search-header heading="${elem.value}"/>
                `;
            } else {
                paymentDetailsDom.innerHTML = '';
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
            } else if (elem.value == 'online') {
                paymentDetailsDom.innerHTML += `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-1">
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
        
        function validateForNextStep() {
            return true;
        }
    </script>
@endsection
