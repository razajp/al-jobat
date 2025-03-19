@extends('app')
@section('title', 'Add Payment | ' . app('company')->name)
@section('content')
    @php
        $type_options = [
            'cash' => ['text' => 'Cash'],
            'cheque' => ['text' => 'Cheque'],
            'slip' => ['text' => 'Slip'],
            'online' => ['text' => 'Online'],
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
                    {{-- amount --}}
                    <x-input label="A/C Title" type="number" placeholder="Enter amount" name="amount" id="amount" required/>
                    
                    {{-- amount --}}
                    <x-input label="Amount" type="number" placeholder="Enter amount" name="amount" id="amount" required/>

                    {{-- transition_id --}}
                    <x-input label="Transition Id" placeholder="Enter cheque no" name="transition_id" id="transition_id" required/>

                    {{-- bank --}}
                    <x-input label="Bank" placeholder="Enter Bank" name="bank" id="bank" required/>

                    {{-- remarks --}}
                    <x-input label="Remarks" placeholder="Remarks" name="remarks" id="remarks" required/>
                `;
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

        function validateForNextStep() {
            return true;
        }
    </script>
@endsection
