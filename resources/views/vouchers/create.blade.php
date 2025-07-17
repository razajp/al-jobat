@extends('app')
@section('title', 'Generate Voucher | ' . app('company')->name)
@section('content')

@php
    $voucherType = Auth::user()->voucher_type;
    
    $steps = [
        $voucherType == 'supplier' ? 'Select Supplier' : 'Select Date',
        'Enter Payment',
        'Preview',
    ];
    
    $method_options = [
        'cash' => ['text' => 'Cash'],
        'cheque' => ['text' => 'Cheque'],
        'slip' => ['text' => 'Slip'],
    ];

    if ($voucherType == 'supplier') {
        // Insert 'program' at 3rd position (index 3)
        $method_options = array_slice($method_options, 0, 3, true)
            + ['program' => ['text' => 'Payment Program']]
            + array_slice($method_options, 3, null, true);
    }

    // Add remaining methods
    $method_options += [
        'self_cheque' => ['text' => 'Self Cheque'],
        'atm' => ['text' => 'ATM'],
        'adjustment' => ['text' => 'Adjustment'],
    ];
@endphp

    <div class="switch-btn-container flex absolute top-3 md:top-17 left-3 md:left-5 z-4">
        <div class="switch-btn relative flex border-3 border-[var(--secondary-bg-color)] bg-[var(--secondary-bg-color)] rounded-2xl overflow-hidden">
            <!-- Highlight rectangle -->
            <div id="highlight" class="absolute h-full rounded-xl bg-[var(--bg-color)] transition-all duration-300 ease-in-out z-0"></div>
            
            <!-- Buttons -->
            <button id="supplierBtn" type="button" class="relative z-10 px-3.5 md:px-5 py-1.5 md:py-2 cursor-pointer rounded-xl transition-colors duration-300" onclick="setVoucherType(this, 'supplier')">
                <div class="hidden md:block">Supplier</div>
                <div class="block md:hidden"><i class="fas fa-cart-shopping text-xs"></i></div>
            </button>
            <button id="selfAccountBtn" type="button" class="relative z-10 px-3.5 md:px-5 py-1.5 md:py-2 cursor-pointer rounded-xl transition-colors duration-300" onclick="setVoucherType(this, 'self_account')">
                <div class="hidden md:block">Self Account</div>
                <div class="block md:hidden"><i class="fas fa-box-open text-xs"></i></div>
            </button>
        </div>
    </div>

    <script>
        let btnTypeGlobal = "supplier";

        function setVoucherType(btn, btnType) {
            doHide = true;
            // check if its already selected
            if (btnTypeGlobal == btnType) {
                return;
            }

            $.ajax({
                url: "/set-voucher-type",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    voucher_type: btnType
                },
                success: function () {
                    location.reload();
                },
                error: function () {
                    alert("Failed to update vaoucher type.");
                    $(btn).prop("disabled", false);
                }
            });

            moveHighlight(btn, btnType);
        }

        function moveHighlight(btn, btnType) {
            const highlight = document.getElementById("highlight");
            const rect = btn.getBoundingClientRect();
            
            const parentRect = btn.parentElement.getBoundingClientRect();
        
            // Move and resize the highlight
            highlight.style.width = `${rect.width}px`;
            highlight.style.left = `${rect.left - parentRect.left - 3}px`;

            btnTypeGlobal = btnType;
        }
    
        // Initialize highlight on load
        window.onload = () => {
            @if($voucherType == 'supplier')
                const activeBtn = document.querySelector("#supplierBtn");
                moveHighlight(activeBtn, "supplier");
            @else
                const activeBtn = document.querySelector("#selfAccountBtn");
                moveHighlight(activeBtn, "self_account");
            @endif
        };
    </script>

    <!-- Progress Bar -->
    <div class="mb-5 max-w-4xl mx-auto">
        <x-search-header heading="Generate Voucher" link linkText="Show Vouchers"
            linkHref="{{ route('vouchers.index') }}" />
        <x-progress-bar :steps="$steps" :currentStep="1" />
    </div>

    <!-- Form -->
    <form id="form" action="{{ route('vouchers.store') }}" method="post"
        class="bg-[var(--secondary-bg-color)] text-sm rounded-xl shadow-lg p-8 border border-[var(--glass-border-color)]/20 pt-14 max-w-4xl mx-auto  relative overflow-hidden">
        @csrf
        <x-form-title-bar title="Generate Voucher" />

        <div class="step1 space-y-4 ">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if ($voucherType == 'supplier')
                    {{-- supplier --}}
                    <x-select class="col-span-2" label="Supplier" name="supplier_id" id="supplier_id" :options="$suppliers_options" required showDefault
                        onchange="trackSupplierState()" />

                    {{-- balance --}}
                    <x-input label="Balance" placeholder="Select supplier first" name="balance" id="balance" disabled />

                    {{-- date --}}
                    <x-input label="Date" name="date" id="date" type="date" required disabled
                        onchange="trackDateState(this)" />
                @else
                    <div class="col-span-full">
                        {{-- date --}}
                        <x-input label="Date" name="date" id="date" type="date" required
                            onchange="trackDateState(this)" />
                    </div>
                @endif
            </div>
        </div>

        <div class="step2 space-y-4 hidden">
            <div class="flex flex-col space-y-4 gap-4">
                {{-- method --}}
                <x-select label="Method" id="method" :options="$method_options" required showDefault
                    onchange="trackMethodState(this)" withButton btnId="enterDetailsBtn" btnText="Enter Details"
                    btnOnclick="trackMethodState(this.previousElementSibling)" />
            </div>
            {{-- payment showing --}}
            <div id="payment-table" class="w-full text-left text-sm">
                <div class="flex justify-between items-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4 mb-4">
                    <div class="w-[7%]">S.No</div>
                    @if ($voucherType == 'self_account')
                        <div class="w-1/3">Account Title</div>
                    @endif
                    <div class="w-1/5">Method</div>
                    <div class="w-1/5">Remarks</div>
                    <div class="w-[15%]">Amount</div>
                    <div class="w-[10%] text-center">Action</div>
                </div>
                <div id="payment-list" class="h-[20rem] overflow-y-auto my-scrollbar-2">
                    <div class="text-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4">No Payment Added</div>
                </div>
                <input type="hidden" name="payment_details_array" id="payment_details_array">
            </div>

            <div class="flex w-full text-sm mt-5 text-nowrap">
                <div
                    class="total-payment flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                    <div class="grow">Total Payment - Rs.</div>
                    <div id="finalTotalPayment">0</div>
                </div>
            </div>
        </div>

        <div class="step3 hidden space-y-4 text-black h-[35rem] overflow-y-auto my-scrollbar-2 bg-white rounded-md">
            <div id="preview-container" class="w-[210mm] h-[297mm] mx-auto overflow-hidden relative">
                <div id="preview" class="preview flex flex-col h-full">
                    <h1 class="text-[var(--border-error)] font-medium text-center mt-5">No Preview avalaible.</h1>
                </div>
            </div>
        </div>
    </form>

    <script>
        let supplierSelectDom = document.getElementById('supplier_id');
        let methodSelectDom = document.getElementById('method');
        let dateDom = document.getElementById('date');
        let balanceDom = document.getElementById('balance');
        let paymentDetailsDom = document.getElementById('paymentDetails');
        let finalTotalPaymentDom = document.getElementById('finalTotalPayment');
        let paymentListDom = document.getElementById('payment-list');
        const paymentDetailsArrayDom = document.getElementById("payment_details_array");

        selectedSupplierData = null;
        let totalPayment = 0;

        let paymentDetailsArray = [];
        let allPayments = [];

        let selectedSupplier;

        const today = new Date().toISOString().split('T')[0];

        function trackSupplierState() {
            dateDom.value = '';
            balanceDom.value = '';
            methodSelectDom.value = '';

            paymentDetailsArray = [];
            renderList();

            if (supplierSelectDom.value != '') {
                selectedSupplier = JSON.parse(supplierSelectDom.parentElement.parentElement.parentElement.querySelector("ul li.selected").dataset.option);
                dateDom.disabled = false;
                methodSelectDom.disabled = false;
                dateDom.min = selectedSupplier.date.toString().split('T')[0];
                dateDom.max = today;
                balanceDom.value = formatNumbersWithDigits(selectedSupplier.balance, 1, 1);
                selectedSupplierData = selectedSupplier;
            } else {
                dateDom.disabled = true;
                methodSelectDom.disabled = true;
            }
        }

        function trackDateState(elem) {
            paymentDetailsArray = [];
            methodSelectDom.value = '';
            renderList();

            if (elem.value != '') {
                gotoStep(2);
            }
        }

        const enterDetailsBtn = document.getElementById("enterDetailsBtn");
        enterDetailsBtn.disabled = true;

        function trackChequeState(elem) {
            let selectedCheque = JSON.parse(elem.options[elem.selectedIndex].dataset.option);
            let amountInpDom = elem.closest('form').querySelector('input[name="amount"]');
            
            amountInpDom.value = selectedCheque.amount;
        }

        function trackSlipState(elem) {
            let selectedSlip = JSON.parse(elem.options[elem.selectedIndex].dataset.option);
            let amountInpDom = elem.closest('form').querySelector('input[name="amount"]');

            amountInpDom.value = selectedSlip.amount;
        }

        let selectedDom;
        let availableChequesArray = [];
        
        function setSelectedAccount(elem) {
            let hiddenAccountInSelfAccount = elem.closest('form').querySelector(`ul[data-for="self_account_id"]`);
            hiddenAccountInSelfAccount.querySelectorAll('li').forEach(li => {
                if (li.style.display == 'none') {
                    li.style.display = 'block';
                }
            })

            let selectedOption = elem.nextElementSibling.querySelector('li.selected');
            let selectedAccount = JSON.parse(selectedOption.getAttribute('data-option')) || ''
            elem.closest('form').querySelector('input[name="selected"]').value = JSON.stringify(selectedAccount);

            availableChequesArray = selectedAccount.available_cheques;

            if (elem.closest('form').querySelector('input[name="cheque_no"]')) {
                fetchChequeNumbers();
            }
            
            const amountInput = elem.closest('form').querySelector('input[name="amount"]');

            const matchingPayments = paymentDetailsArray.filter(item => 
                item.bank_account_id == selectedAccount.id
            );

            const totalAmount = matchingPayments.reduce((sum, item) => {
                return sum + parseFloat(item.amount || 0);
            }, 0);

            amountInput.dataset.validate += `|max:${selectedAccount.balance - totalAmount}`;

            let selectedAccountInSelfAccount = elem.closest('form').querySelector(`ul[data-for="self_account_id"] li[data-value="${selectedAccount.id}"]`);

            if (selectedAccountInSelfAccount) {
                selectedAccountInSelfAccount.style.display = 'none';
            }
        }

        function updateSelectedAccount(elem) {
            let hiddenAccountInSelfAccount = elem.closest('form').querySelector(`ul[data-for="bank_account_id"]`);
            hiddenAccountInSelfAccount.querySelectorAll('li').forEach(li => {
                if (li.style.display == 'none') {
                    li.style.display = 'block';
                }
            })

            let selectedOption = elem.nextElementSibling.querySelector('li.selected');
            let selectedAccount = JSON.parse(selectedOption.getAttribute('data-option')) || ''

            let selectedAccountInBankAccount = elem.closest('form').querySelector(`ul[data-for="bank_account_id"] li[data-value="${selectedAccount.id}"]`);

            if (selectedAccountInBankAccount) {
                selectedAccountInBankAccount.style.display = 'none';
            }
        }

        function fetchChequeNumbers() {
            const chequeNoSelect = document.querySelector("#cheque_no");
            const chequeNoDropdown = document.querySelector("ul.optionsDropdown[data-for='cheque_no']");

            const usedChequeNumbers = paymentDetailsArray.map(p => String(p.cheque_no));
            const filteredCheques = availableChequesArray.filter(chequeNo => !usedChequeNumbers.includes(String(chequeNo)));

            let clutter = `
                <li data-for="cheque_no" data-value="" onmousedown="selectThisOption(this)" class="py-2 px-3 cursor-pointer rounded-lg transition hover:bg-[var(--h-bg-color)] selected">
                    -- Select Cheque Number --
                </li>
                ${filteredCheques.map(chequeNo => `
                    <li data-for="cheque_no" data-value="${chequeNo}" onmousedown="selectThisOption(this)" class="py-2 px-3 cursor-pointer rounded-lg transition hover:bg-[var(--h-bg-color)] text-nowrap overflow-scroll my-scrollbar-2">
                        ${chequeNo}
                    </li>
                `).join('')}
            `;

            chequeNoDropdown.innerHTML = clutter;
            chequeNoSelect.disabled = false;
        }

        function trackMethodState(elem) {
            let fieldsData = [];

            if (elem.value == 'cash') {
                fieldsData.push(
                    {
                        category: 'input',
                        name: 'amount',
                        label: 'Amount',
                        type: 'number',
                        required: true,
                        placeholder: 'Enter amount',
                    },
                    @if($voucherType == 'self_account')
                    {
                        category: 'explicitHtml',
                        html: `
                            <x-select class="" label="Self Account" name="self_account_id" id="self_account_id" :options="$self_accounts_options" required showDefault />
                        `,
                    },
                    @endif
                );
            } else if (elem.value == 'cheque') {
                fieldsData.push(
                    {
                        category: 'select',
                        name: 'cheque_id',
                        label: 'Cheque',
                        required: true,
                        options: [@json($cheques_options)],
                        onchange: 'trackChequeState(this)'
                    },
                    @if($voucherType == 'self_account')
                    {
                        category: 'explicitHtml',
                        html: `
                            <x-select class="" label="Self Account" name="self_account_id" id="self_account_id" :options="$self_accounts_options" required showDefault />
                        `,
                    },
                    @endif
                    {
                        category: 'input',
                        name: 'amount',
                        label: 'Amount',
                        type: 'number',
                        required: true,
                        placeholder: 'Enter amount',
                        readonly: true,
                    },
                    {
                        category: 'input',
                        name: 'selected',
                        type: 'hidden',
                    }
                );
            } else if (elem.value == 'slip') {
                fieldsData.push(
                    {
                        category: 'select',
                        name: 'slip_id',
                        label: 'Slip',
                        required: true,
                        options: [@json($slips_options)],
                        onchange: 'trackSlipState(this)',
                    },
                    @if($voucherType == 'self_account')
                    {
                        category: 'explicitHtml',
                        html: `
                            <x-select class="" label="Self Account" name="self_account_id" id="self_account_id" :options="$self_accounts_options" required showDefault />
                        `,
                    },
                    @endif
                    {
                        category: 'input',
                        name: 'amount',
                        label: 'Amount',
                        type: 'number',
                        required: true,
                        placeholder: 'Enter amount',
                        readonly: true,
                    },
                    {
                        category: 'input',
                        name: 'selected',
                        type: 'hidden',
                    }
                );
            } else if (elem.value == 'program') {
                fieldsData.push(
                    {
                        category: 'select',
                        name: 'program_id',
                        id: 'program',
                        label: 'Program',
                        required: true,
                        options: [],
                    },
                    {
                        category: 'input',
                        name: 'amount',
                        id: 'amount',
                        label: 'Amount',
                        type: 'number',
                        required: true,
                        placeholder: 'Enter amount',
                        readonly: true,
                    },
                    {
                        category: 'input',
                        name: 'selected',
                        id: 'selected',
                        type: 'hidden',
                    },
                    {
                        category: 'input',
                        name: 'payment_id',
                        id: 'payment_id',
                        type: 'hidden',
                    },
                );
            } else if (elem.value == 'self_cheque') {
                console.log(availableChequesArray);
                
                fieldsData.push(
                    {
                        category: 'explicitHtml',
                        html: `
                            <x-select class="" label="Self Account" name="bank_account_id" id="bank_account_id" :options="$self_accounts_options" required onchange="setSelectedAccount(this)" showDefault />
                        `,
                    },
                    {
                        category: 'explicitHtml',
                        html: `
                            <x-select class="" label="Cheque No." name="cheque_no" id="cheque_no" required showDefault />
                        `,
                    },
                    {
                        category: 'explicitHtml',
                        html: `
                            <x-input label="Amount" name="amount" id="amount" type="number" placeholder="Enter amount" required dataValidate="required" oninput="validateInput(this)"/>
                        `,
                    },
                    @if($voucherType == 'self_account')
                    {
                        category: 'explicitHtml',
                        html: `
                            <x-select class="" label="Self Account" name="self_account_id" id="self_account_id" :options="$self_accounts_options" onchange="updateSelectedAccount(this)" required showDefault />
                        `,
                    },
                    {
                        category: 'explicitHtml',
                        html: `
                            <x-input label="Cheque Date" name="cheque_date" id="cheque_date" type="date" placeholder="Enter cheque date" required/>
                        `,
                    },
                    @endif
                    {
                        category: 'input',
                        name: 'selected',
                        type: 'hidden',
                    }
                );
            } else if (elem.value == 'atm') {
                fieldsData.push(
                    {
                        category: 'explicitHtml',
                        html: `
                            <x-select class="" label="Self Account" name="bank_account_id" id="bank_account_id" :options="$self_accounts_options" required onchange="setSelectedAccount(this)" showDefault />
                        `,
                    },
                    {
                        category: 'input',
                        name: 'reff_no',
                        label: 'Reff. No.',
                        type: 'number',
                        required: true,
                        placeholder: 'Enter reff no.',
                    },
                    {
                        category: 'explicitHtml',
                        html: `
                            <x-input label="Amount" name="amount" id="amount" type="number" placeholder="Enter amount" required dataValidate="required" oninput="validateInput(this)"/>
                        `,
                    },
                    @if($voucherType == 'self_account')
                    {
                        category: 'explicitHtml',
                        html: `
                            <x-select class="" label="Self Account" name="self_account_id" id="self_account_id" :options="$self_accounts_options" onchange="updateSelectedAccount(this)" required showDefault />
                        `,
                    },
                    @endif
                    {
                        category: 'input',
                        name: 'selected',
                        type: 'hidden',
                    }
                );
            } else if (elem.value == 'adjustment') {
                fieldsData.push(
                    {
                        category: 'input',
                        name: 'amount',
                        label: 'Amount',
                        type: 'number',
                        required: true,
                        placeholder: 'Enter amount',
                    },
                    @if($voucherType == 'self_account')
                    {
                        category: 'explicitHtml',
                        html: `
                            <x-select class="" label="Self Account" name="self_account_id" id="self_account_id" :options="$self_accounts_options" required showDefault />
                        `,
                    },
                    @endif
                );
            }

            if (elem.value != '') {
                fieldsData.push({
                    category: 'input',
                    name: 'remarks',
                    label: 'Remarks',
                    type: 'text',
                    required: true,
                    placeholder: 'Enter remarks',
                    enterToSubmitListener: true,
                    full: false,
                });
                
                const visibleIndexes = fieldsData
                .map((field, index) => field.type !== 'hidden' ? index : null)
                .filter(index => index !== null);

                if (visibleIndexes.length > 0) {
                const lastVisibleIndex = visibleIndexes[visibleIndexes.length - 1];
                fieldsData[lastVisibleIndex].full = visibleIndexes.length % 2 === 1;
                }
                
                let modalData = {
                    id: 'modalForm',
                    class: 'h-auto',
                    name: 'Payment Details',
                    fields: fieldsData,
                    fieldsGridCount: '2',
                    bottomActions: [
                        {id: 'add-payment-details', text: 'Add Payment', onclick: 'addPaymentDetails()'},
                    ],
                    defaultListener: false,
                }

                createModal(modalData);

                let amountInpDom = document.getElementById('amount');
                selectedDom = document.getElementById('selected');

                let allSelfAccounts = @json($self_accounts);

                const filteredAccounts = allSelfAccounts.filter(account => {
                    return new Date(account.date) <= new Date(dateDom.value);
                });

                if (elem.value == 'program') {
                    let paymentSelectDom = document.getElementById('program');

                    let allPayments = selectedSupplier.payments;

                    const filteredPayments = allPayments.filter(payment => {
                        return new Date(payment.date) <= new Date(dateDom.value);
                    });

                    filteredPayments.forEach(payment => {
                        paymentSelectDom.innerHTML += `<option value="${payment.id}" data-option='${JSON.stringify(payment)}'>${payment.amount} | ${payment.program.customer.customer_name}</option>`;
                    })

                    if (filteredPayments.length > 0) {
                        paymentSelectDom.disabled = false;
                    }

                    paymentSelectDom.addEventListener('change', () => {
                        let selectedOption = paymentSelectDom.options[paymentSelectDom.selectedIndex];
                        let selectedPayment = JSON.parse(selectedOption.getAttribute('data-option')) || '';

                        selectedDom.value = JSON.stringify(selectedPayment);
                        document.getElementById('amount').value = selectedPayment.amount;
                        document.getElementById('payment_id').value = selectedPayment.id;
                    })
                }
            }
        }

        function addPaymentDetails() {
            let detail = {};
            let allDetail = {};
            const inputs = document.querySelectorAll('#modalForm input:not([disabled])');

            inputs.forEach(input => {
                const name = input.getAttribute('name');
                if (name != null) {
                    const value = input.value;

                    if (name == "amount") {
                        detail[name] = parseInt(value);
                        allDetail[name] = parseInt(value);
                    } else {
                        detail[name] = value;
                        allDetail[name] = value;
                    }
                } else {
                    const value = JSON.parse(input.value);

                    allDetail[name ?? 'selected'] = value;
                }
            });

            const selectBankAccount = document.querySelector("#modalForm select");
            if (selectBankAccount) {
                detail[selectBankAccount.getAttribute('name')] = selectBankAccount.value;
            }

            if (isNaN(detail.amount) || detail.amount <= 0) {
                detail = {};
            }

            if (Object.keys(detail).length > 0) {
                totalPayment += detail.amount;
                detail['method'] = methodSelectDom.value;
                allDetail['method'] = methodSelectDom.value;
                paymentDetailsArray.push(detail);
                allPayments.push(allDetail);
                renderList();
            }
            closeModal('modalForm');
        }

        function renderList() {
            if (paymentDetailsArray.length > 0) {
                let clutter = "";
                paymentDetailsArray.forEach((paymentDetail, index) => {
                    clutter += `
                        <div class="flex justify-between items-center border-t border-gray-600 py-3 px-4">
                            <div class="w-[7%]">${index+1}</div>
                            @if ($voucherType == 'self_account')
                                <div class="w-1/3 capitalize">${paymentDetail.self_account_id_name}</div>
                            @endif
                            <div class="w-1/5 capitalize">${paymentDetail.method}</div>
                            <div class="w-1/5 capitalize">${paymentDetail.remarks && paymentDetail.remarks.trim() !== '' ? paymentDetail.remarks : '-'}</div>
                            <div class="w-[15%]">${formatNumbersWithDigits(paymentDetail.amount, 1, 1)}</div>
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

        let lastVoucher = @json($last_voucher);

        function generateVoucherNo() {
            // Split the voucher string into left and right parts
            let parts = lastVoucher.voucher_no.split('/');
            let left = parseInt(parts[0], 10);
            let right = parseInt(parts[1], 10);

            // Increment logic
            left += 1;
            if (parseInt(parts[0], 10) === 100) {
                right += 1;
                left = 1; // not 01 - we format it later
            }

            // Format with leading zeros
            let newLeft = left.toString().padStart(2, '0');   // Always 2 digits
            let newRight = right.toString().padStart(3, '0'); // Always 3 digits

            // Return formatted voucher number
            return `${newLeft}/${newRight}`;
        }

        let companyData = @json(app('company'));
        const previewDom = document.getElementById('preview');
        function generateVoucherPreview() {
            let voucherNo = generateVoucherNo();
            const dateInpDom = document.getElementById("date");

            if (allPayments.length > 0) {
                previewDom.innerHTML = `
                    <div id="preview-document" class="preview-document flex flex-col h-full">
                        <div id="preview-banner" class="preview-banner w-full flex justify-between items-center mt-8 pl-5 pr-8">
                            <div class="left">
                                <div class="company-logo">
                                    <img src="{{ asset('images/${companyData.logo}') }}" alt="Track Point"
                                        class="w-[12rem]" />
                                </div>
                            </div>
                            <div class="right">
                                <div>
                                    <h1 class="text-2xl font-medium text-[var(--primary-color)] pr-2">Voucher</h1>
                                    <div class='mt-1'>${ companyData.phone_number }</div>
                                </div>
                            </div>
                        </div>
                        <hr class="w-full my-3 border-gray-600">
                        <div id="preview-header" class="preview-header w-full flex justify-between px-5">
                            <div class="left my-auto pr-3 text-sm text-gray-600 space-y-1.5">
                                <div class="voucher-date leading-none">Date: ${formatDate(dateInpDom.value)}</div>
                                <div class="voucher-number leading-none">Voucher No.: ${voucherNo}</div>
                                <input type="hidden" name="voucher_no" value="${voucherNo}" />
                            </div>
                            @if ($voucherType == 'supplier')
                                <div class="center my-auto">
                                    <div class="supplier-name capitalize font-semibold text-md">Supplier Name: ${selectedSupplier.supplier_name}</div>
                                </div>
                            @endif
                            <div class="right my-auto pr-3 text-sm text-gray-600 space-y-1.5">
                                <div class="preview-copy leading-none">Voucher Copy: Supplier</div>
                                <div class="preview-doc leading-none">Document: Voucher</div>
                            </div>
                        </div>
                        <hr class="w-full my-3 border-gray-600">
                        <div id="preview-body" class="preview-body w-[95%] grow mx-auto">
                            <div class="preview-table w-full">
                                <div class="table w-full border border-gray-600 rounded-lg pb-2.5 overflow-hidden">
                                    <div class="thead w-full">
                                        <div class="tr flex justify-between w-full px-4 py-1.5 bg-[var(--primary-color)] text-white">
                                            <div class="th text-sm font-medium w-[7%]">S.No</div>
                                            <div class="th text-sm font-medium w-[11%]">Method</div>
                                            <div class="th text-sm font-medium w-1/5">Customer</div>
                                            <div class="th text-sm font-medium w-1/4">Account</div>
                                            <div class="th text-sm font-medium w-[17%]">Date</div>
                                            <div class="th text-sm font-medium w-[11%]">Reff. No.</div>
                                            <div class="th text-sm font-medium w-[10%]">Amount</div>
                                        </div>
                                    </div>
                                    <div id="tbody" class="tbody w-full">
                                        ${paymentDetailsArray.map((payment, index) => {
                                            const hrClass = index === 0 ? "mb-2.5" : "my-2.5";
                                            return `
                                                    <div>
                                                        <hr class="w-full ${hrClass} border-gray-600">
                                                        <div class="tr flex justify-between w-full px-4">
                                                            <div class="td text-sm font-semibold w-[7%]">${index + 1}.</div>
                                                            <div class="td text-sm font-semibold w-[11%] capitalize">${payment.method ?? '-'}</div>
                                                            <div class="td text-sm font-semibold w-1/5">${payment.selected?.program?.customer.customer_name ?? '-'}</div>
                                                            <div class="td text-sm font-semibold w-1/4">${(payment.selected?.bank_account?.account_title ?? '-') + ' | ' + (payment.selected?.bank_account?.bank.short_title ?? '-')}</div>
                                                            <div class="td text-sm font-semibold w-[17%]">${formatDate(dateInpDom.value) ?? '-'}</div>
                                                            <div class="td text-sm font-semibold w-[11%]">${payment.selected?.cheque_no ?? payment.selected?.slip_no ?? payment.selected?.transaction_id ?? '-'}</div>
                                                            <div class="td text-sm font-semibold w-[10%]">${formatNumbersWithDigits(payment.amount, 1, 1) ?? '-'}</div>
                                                        </div>
                                                    </div>
                                                `;
                                        }).join('')}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr class="w-full my-3 border-gray-600">
                        <div class="flex flex-col space-y-2">
                            <div id="total" class="tr flex justify-between w-full px-2 gap-2 text-sm">
                                @if ($voucherType == 'supplier')
                                    <div class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                                        <div class="text-nowrap">Previous Balance - Rs</div>
                                        <div class="w-1/4 text-right grow">${formatNumbersWithDigits(selectedSupplier.balance, 1, 1)}</div>
                                    </div>
                                @endif
                                <div class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                                    <div class="text-nowrap">Total Payment - Rs</div>
                                    <div class="w-1/4 text-right grow">${formatNumbersWithDigits(totalPayment, 1, 1)}</div>
                                </div>
                                @if ($voucherType == 'supplier')
                                    <div class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                                        <div class="text-nowrap">Current Balance - Rs</div>
                                        <div class="w-1/4 text-right grow">${formatNumbersWithDigits(selectedSupplier.balance - totalPayment, 1, 1)}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <hr class="w-full my-3 border-gray-600">
                        <div class="tfooter flex w-full text-sm px-4 justify-between mb-4 text-gray-600">
                            <P class="leading-none">${ companyData.name } | ${ companyData.address }</P>
                            <p class="leading-none text-sm">&copy; 2025 Spark Pair | +92 316 5825495</p>
                        </div>
                    </div>
                `;
            } else {
                previewDom.innerHTML = `
                    <h1 class="text-[var(--border-error)] font-medium text-center mt-5">No Preview avalaible.</h1>
                `;
            }
        }

        function validateForNextStep() {
            generateVoucherPreview();
            return true;
        }
    </script>
@endsection
