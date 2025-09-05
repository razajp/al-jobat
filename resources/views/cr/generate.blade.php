@extends('app')
@section('title', 'Generate CR | ' . app('company')->name)
@section('content')
    @php
        $method_options = [
            'cheque' => ['text' => 'Cheque'],
            'slip' => ['text' => 'Slip'],
            'self_cheque' => ['text' => 'Self Cheque'],
            'program' => ['text' => 'Payment Program'],
        ];
    @endphp
    <!-- Main Content -->
    <!-- Progress Bar -->
    <div class="mb-5 max-w-4xl mx-auto">
        <x-search-header heading="Generate CR" link linkText="Show CR" linkHref="{{ route('cr.index') }}"/>
        <x-progress-bar :steps="['Select Payment', 'Add Payment']" :currentStep="1" />
    </div>

    <!-- Form -->
    <form id="form" action="{{ route('cr.store') }}" method="post" enctype="multipart/form-data"
        class="bg-[var(--secondary-bg-color)] text-sm rounded-xl shadow-lg p-8 border border-[var(--glass-border-color)]/20 pt-14 max-w-4xl mx-auto  relative overflow-hidden">
        @csrf
        <x-form-title-bar title="Generate CR" />

        <!-- Step 1: Generate cargo list -->
        <div class="step1 space-y-4 ">
            <div class="grid grid-cols-3 gap-4">
                <!-- voucher_no -->
                <x-input
                    label="Voucher No."
                    name="voucher_no"
                    id="voucher_no"
                    placeholder="Enter Voucher No."
                    required
                    onkeydown="trackVoucherState(event)"
                />

                {{-- cargo date --}}
                <x-input label="Date" name="date" id="date" type="date" validateMax max="{{ today()->toDateString() }}" required disabled/>

                <!-- supplier_name -->
                <x-input
                    label="Supplier Name"
                    id="supplier_name"
                    disabled
                    placeholder="Supplier Name"
                />
            </div>
            {{-- cargo-list-table --}}
            <div id="cargo-list-table" class="w-full text-left text-sm">
                <div class="flex justify-between items-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4 mb-4">
                    <div class="w-[8%]">S.No.</div>
                    <div class="w-1/6">Date</div>
                    <div class="w-[10%]">Method</div>
                    <div class="w-1/6">Reff. No.</div>
                    <div class="w-1/6">Amount</div>
                    <div class="grow">Customer</div>
                    <div class="w-[10%] text-center">Select</div>
                </div>
                <div id="cargo-list" class="h-[20rem] overflow-y-auto my-scrollbar-2">
                    <div class="text-center bg-[var(--h-bg-color)] rounded-lg py-3 px-4">No Payments Added</div>
                </div>
            </div>

            <div class="w-full grid grid-cols-2 gap-4 text-sm mt-5 text-nowrap">
                <div class="flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                    <div class="grow">Total Voucher Payment</div>
                    <div id="finalTotalPayment">0</div>
                </div>
                <div class="flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                    <div class="grow">Total Selected Payment</div>
                    <div id="finalTotalSelectedPayment">0</div>
                </div>
            </div>
        </div>

        <!-- Step 2: view shipment -->
        <div class="step2 hidden space-y-4">
            <div class="grid grid-cols-3 gap-4">
                <!-- method -->
                <x-select
                    label="Method"
                    id="method"
                    :options="$method_options"
                    required
                    showDefault
                    onchange="trackMethodState(this)"
                />

                <!-- payment -->
                <x-select
                    label="Payment"
                    id="payment"
                    :options="$payment_options"
                    required
                    showDefault
                />

                <!-- supplier_name -->
                <x-input
                    label="Amount"
                    id="amount"
                    namr="amount"
                    disabled
                    placeholder="Enter Amount"
                    type="number"
                />
            </div>
            {{-- cargo-list-table --}}
            <div id="cargo-list-table" class="w-full text-left text-sm">
                <div class="flex justify-between items-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4 mb-4">
                    <div class="w-[8%]">S.No.</div>
                    <div class="w-1/6">Date</div>
                    <div class="w-[10%]">Method</div>
                    <div class="w-1/6">Reff. No.</div>
                    <div class="w-1/6">Amount</div>
                    <div class="grow">Customer</div>
                    <div class="w-[10%] text-center">Select</div>
                </div>
                <div id="cargo-list" class="h-[20rem] overflow-y-auto my-scrollbar-2">
                    <div class="text-center bg-[var(--h-bg-color)] rounded-lg py-3 px-4">No Payments Added</div>
                </div>
            </div>

            <div class="w-full grid grid-cols-2 gap-4 text-sm mt-5 text-nowrap">
                <div class="flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                    <div class="grow">Total Selected Payment</div>
                    <div id="finalTotalSelectedPayment">0</div>
                </div>
                <div class="flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                    <div class="grow">Total Added Payment</div>
                    <div id="finalTotalAddedPayment">0</div>
                </div>
            </div>
        </div>
    </form>

    <script>
        let voucher = {};
        let paymentsArray = [];
        const dateDom = document.getElementById('date');
        const supplierNameDom = document.getElementById('supplier_name');
        const cargoListDOM = document.getElementById('cargo-list');
        const finalTotalPaymentDOM = document.getElementById('finalTotalPayment');
        const finalTotalSelectedPaymentDOM = document.querySelectorAll('#finalTotalSelectedPayment');
        let totalVoucherAmount = 0;
        let totalSelectedAmount = 0;

        function trackVoucherState(e) {
            if (e.key == 'Enter') {
                $.ajax({
                    url: '/get-voucher-details',
                    type: 'POST',
                    data: {
                        voucher_no: e.target.value,
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        voucher = response.data;
                        if (voucher) {
                            console.log(voucher);
                            date.disabled = false;
                            date.min = voucher.date;
                            supplierNameDom.value = voucher.supplier_name;

                            paymentsArray = voucher.payments;

                            const messages = document.querySelectorAll('.alert-message');

                            messages.forEach((message) => {
                                if (message) {
                                    message.classList.add('fade-out');
                                    message.addEventListener('animationend', () => {
                                        message.style.display = 'none';
                                    });
                                }
                            });
                        } else {
                            dateDom.value = '';
                            dateDom.disabled = true;
                            supplierNameDom.value = '';
                            paymentsArray = [];

                            messageBox.innerHTML = `
                                <x-alert type="error" :messages="'${response.message}'" />
                            `;
                            messageBoxAnimation()
                        }
                        renderList()
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }
        }

        function renderList() {
            totalVoucherAmount = 0;
            totalSelectedAmount = 0;
            if (paymentsArray.length > 0) {
                let clutter = "";
                paymentsArray.forEach((payment, index) => {
                    totalVoucherAmount += payment.amount;
                    totalSelectedAmount += payment.checked ? payment.amount : 0;
                    clutter += `
                        <div class="flex justify-between items-end border-t border-gray-600 py-3 px-4 cursor-pointer" onclick="selectThisPayment(this, ${index})">
                            <div class="w-[8%]">${index+1}</div>
                            <div class="w-1/6">${formatDate(payment.date)}</div>
                            <div class="w-[10%] capitalize">${payment.method}</div>
                            <div class="w-1/6">${payment.reff_no ?? '-'}</div>
                            <div class="w-1/6">${formatNumbersWithDigits(payment.amount, 1, 1) ?? '-'}</div>
                            <div class="grow">${payment.customer_name ?? '-'}</div>
                            <div class="w-[10%] grid place-items-center">
                                <input ${payment.checked ? 'checked' : ''} type="checkbox" name="selected_card[]"
                                    class="row-checkbox hrink-0 w-3.5 h-3.5 appearance-none border border-gray-400 rounded-sm checked:bg-[var(--primary-color)] checked:border-transparent focus:outline-none transition duration-150 pointer-events-none cursor-pointer"/>
                            </div>
                        </div>
                    `;
                });

                cargoListDOM.innerHTML = clutter;
            } else {
                cargoListDOM.innerHTML =
                    `<div class="text-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4">No Payments Yet</div>`;
            }
            finalTotalPaymentDOM.textContent = formatNumbersWithDigits(totalVoucherAmount, 1, 1);
            finalTotalSelectedPaymentDOM.forEach(elem => {
                elem.textContent = formatNumbersWithDigits(totalSelectedAmount, 1, 1);
            });
        }
        renderList();

        function selectThisPayment(elem, index) {
            let checkBox = elem.querySelector('.row-checkbox');
            checkBox.checked = !checkBox.checked;
            paymentsArray[index].checked = !paymentsArray[index].checked;

            renderList();
        }

        function trackMethodState(elem) {
            if (elem.value != '') {
                $.ajax({
                    url: '/cr/create',
                    type: 'POST',
                    data: {
                        method: elem.value,
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log(response);

                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }
        }

        function validateForNextStep() {
            return true;
        }
    </script>
@endsection
