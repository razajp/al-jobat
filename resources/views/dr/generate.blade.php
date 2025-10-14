@extends('app')
@section('title', 'Generate DR | ' . app('company')->name)
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
    <div class="mb-5 max-w-5xl mx-auto">
        <x-search-header heading="Generate DR" link linkText="Show DR" linkHref="{{ route('dr.index') }}"/>
        <x-progress-bar :steps="['Select Payment', 'Add Payment']" :currentStep="1" />
    </div>

    <!-- Form -->
    <form id="form" action="{{ route('dr.store') }}" method="post" enctype="multipart/form-data"
        class="bg-[var(--secondary-bg-color)] text-sm rounded-xl shadow-lg p-8 border border-[var(--glass-border-color)]/20 pt-14 max-w-5xl mx-auto  relative overflow-hidden">
        @csrf
        <x-form-title-bar title="Generate DR" />

        <!-- Step 1: Generate cargo list -->
        <div class="step1 space-y-4 ">
            <div class="flex items-end gap-4">
                <div class="grow">
                    <!-- customer -->
                    <x-select
                        label="Customer"
                        id="customer"
                        name="customer_id"
                        :options="$customer_options"
                        showDefault
                        onchange="trackCustomerState(this)"
                    />
                </div>

                <div class="w-1/4">
                    {{-- date --}}
                    <x-input label="Date" name="date" id="date" type="date" validateMax max="{{ today()->toDateString() }}" required/>
                </div>

                <button id="selectPaymentBtn" type="button" class="bg-[var(--primary-color)] px-4 py-2 rounded-lg hover:bg-[var(--h-primary-color)] transition-all duration-300 ease-in-out text-nowrap cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed" disabled onclick="getPayments()">Select Payments</button>
            </div>
            <input type="hidden" name="returnPayments" id="selectedPaymentsArray">
            {{-- show-payment-table --}}
            <div id="show-payment-table" class="w-full text-left text-sm">
                <div class="flex justify-between items-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4 mb-4">
                    <div class="w-[8%]">S.No.</div>
                    <div class="w-1/6">Date</div>
                    <div class="w-[10%]">Method</div>
                    <div class="w-1/6">Reff. No.</div>
                    <div class="w-1/6">Amount</div>
                    <div class="w-1/6">Issued</div>
                    <div class="w-[10%] text-center">Select</div>
                </div>
                <div id="show-payments" class="h-[20rem] overflow-y-auto my-scrollbar-2">
                    <div class="text-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4">No Payments Added</div>
                </div>
            </div>

            <div class="w-full grid grid-cols-2 gap-4 text-sm mt-5 text-nowrap">
                <div class="flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                    <div class="grow">Total Selected Payments</div>
                    <div id="finalSelectedPayments">0</div>
                </div>
                <div class="flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                    <div class="grow">Total Selected Amount</div>
                    <div class="finalTotalSelectedAmount">0</div>
                </div>
            </div>
        </div>

        <!-- Step 2: view shipment -->
        <div class="step2 hidden space-y-4">
            <div class="flex items-end gap-4">
                <div class="grow">
                    <!-- method -->
                    <x-select
                        label="Method"
                        id="method"
                        required
                        showDefault
                        :options="$method_options"
                        onchange="trackMethodState(this)"
                    />
                </div>

                <button id="addPaymentBtn" type="button" class="bg-[var(--primary-color)] px-4 py-2 rounded-lg hover:bg-[var(--h-primary-color)] transition-all duration-300 ease-in-out text-nowrap cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed" onclick="addPayment()">Add Payment</button>
            </div>
            <input type="hidden" name="newPayments" id="addedPaymentsArray">
            {{-- add-payment-table --}}
            <div id="add-payment-table" class="w-full text-left text-sm">
                <div class="grid grid-cols-6 bg-[var(--h-bg-color)] rounded-lg py-2 px-4 mb-4">
                    <div>S.No.</div>
                    <div>Method</div>
                    <div class="col-span-2">Payment</div>
                    <div>Amount</div>
                    <div class="text-center">Action</div>
                </div>
                <div id="add-payment" class="h-[20rem] overflow-y-auto my-scrollbar-2">
                    <div class="text-center bg-[var(--h-bg-color)] rounded-lg py-3 px-4">No Payments Added</div>
                </div>
            </div>

            <div class="w-full grid grid-cols-2 gap-4 text-sm mt-5 text-nowrap">
                <div class="flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                    <div class="grow">Total Selected Amount</div>
                    <div class="finalTotalSelectedAmount">0</div>
                </div>
                <div class="flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                    <div class="grow">Total Added Payment</div>
                    <div id="finalTotalAddedPayment">0</div>
                </div>
            </div>
        </div>
    </form>
    <script>
        payments = [];
        selectedPayments = [];

        function trackCustomerState(elem) {
            const selectPaymentBtn = document.getElementById('selectPaymentBtn');
            if (elem.value) {
                selectPaymentBtn.disabled = false;
            } else {
                selectPaymentBtn.disabled = true;
            }
        }

        function getPayments() {
            $.ajax({
                url: '/dr/get-payments',
                method: 'GET',
                data: {
                    customer_id: document.querySelector('input[data-for="customer"]').value,
                },
                success: function(response) {
                    if (response.status === 'success') {
                        payments = response.data;
                        renderList();
                    } else {
                        console.error('Failed to fetch payments');
                    }
                },
                error: function(xhr) {
                    // Handle any errors that occur during the request
                    console.error(xhr.responseText);
                }
            });
        }

        function renderList() {
            const showPaymentsDom = document.getElementById('show-payments');
            showPaymentsDom.innerHTML = '';

            if (payments.length === 0) {
                showPaymentsDom.innerHTML = '<div class="text-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4">No Payments Added</div>';
                return;
            }

            payments.forEach((payment, index) => {
                showPaymentsDom.innerHTML += `
                    <div id="${payment.id}" class="flex justify-between items-center border-b border-gray-600 py-2 px-4 cursor-pointer" onclick="togglePaymentSelection(this)">
                        <div class="w-[8%]">${index + 1}.</div>
                        <div class="w-1/6">${formatDate(payment.date)}</div>
                        <div class="w-[10%]">${payment.method}</div>
                        <div class="w-1/6">${payment.cheque_no || payment.slip_no}</div>
                        <div class="w-1/6">${formatNumbersWithDigits(payment.amount, 1, 1)}</div>
                        <div class="w-1/6">${payment.is_return ? 'Return' : 'Not Issued'}</div>
                        <div class="w-[10%] grid place-items-center">
                            <input ${payment.checked ? 'checked' : ''} type="checkbox" class="row-checkbox hrink-0 w-3.5 h-3.5 appearance-none border border-gray-400 rounded-sm checked:bg-[var(--primary-color)] checked:border-transparent focus:outline-none transition duration-150 pointer-events-none cursor-pointer"/>
                        </div>
                    </div>
                `;
            })

            const finalSelectedPayments = document.getElementById('finalSelectedPayments');
            finalSelectedPayments.innerText = selectedPayments.length;
            const finalTotalSelectedAmount = document.querySelectorAll('.finalTotalSelectedAmount');
            const totalAmount = payments.filter(p => p.checked).reduce((sum, p) => sum + parseFloat(p.amount), 0);
            finalTotalSelectedAmount.forEach(element => {
                element.innerText = formatNumbersWithDigits(totalAmount, 1, 1);
            });;
            document.getElementById('selectedPaymentsArray').value = JSON.stringify(selectedPayments);
        }

        function togglePaymentSelection(row) {
            const paymentId = row.id;
            const payment = payments.find(p => p.id == paymentId);
            if (payment) {
                payment.checked = !payment.checked;
                const checkbox = row.querySelector('input[type="checkbox"]');
                if (checkbox) {
                    checkbox.checked = payment.checked;
                }
            }

            if (selectedPayments.includes(paymentId)) {
                selectedPayments = selectedPayments.filter(id => id !== paymentId);
            } else {
                selectedPayments.push(paymentId);
            }

            renderList();
        }

        validateForNextStep = () => {
            return true;
        }
    </script>
@endsection
