@extends('app')
@section('title', 'Add Utility Bill | ' . app('company')->name)
@section('content')
    <!-- Main Content -->
    <div class="max-w-3xl mx-auto">
        <x-search-header heading="Add Utility Bill" link linkText="Show Utility Bills" linkHref="{{ route('utility-bills.index') }}"/>
    </div>

    <!-- Form -->
    <form id="form" action="{{ route('utility-bills.store') }}" method="post"
        class="bg-[var(--secondary-bg-color)] text-sm rounded-xl shadow-lg p-8 border border-[var(--glass-border-color)]/20 pt-14 max-w-3xl mx-auto  relative overflow-hidden">
        @csrf
        <x-form-title-bar title="Add Utility Bill" />

        <div class="grid grid-cols-2 gap-4">
            {{-- bill_type --}}
            <x-select
                label="Bill Type"
                name="bill_type_id"
                id="bill_type"
                :options="$bill_type_options"
                onchange="trackBillType(this)"
                required
                showDefault
            />

            {{-- location --}}
            <x-select
                label="Location"
                name="location_id"
                id="location"
                :options="$location_options"
                onchange="trackLocation(this)"
                required
                showDefault
                disabled
            />

            {{-- account --}}
            <x-select
                label="Account"
                name="account_id"
                id="account"
                :options="[]"
                onchange="trackAccount(this)"
                required
                showDefault
            />

            {{-- month --}}
            <x-input label="Month" name="month" id="month" type="month" required disabled />

            {{-- units --}}
            <x-input label="Units" name="units" id="units" type="number" placeholder="Enter Units" disabled />

            {{-- amount --}}
            <x-input label="Amount" name="amount" id="amount" type="amount" required dataValidate="required|amount" placeholder="Enter Amount" disabled />

            <div class="col-span-full">
                {{-- due_date --}}
                <x-input label="Due Date" name="due_date" id="due_date" type="date" required disabled />
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
        let billTypeSelectDom = document.getElementById('bill_type');
        let locationSelectDom = document.getElementById('location');
        let accountSelectDom = document.getElementById('account');
        let monthInpDom = document.getElementById('month');
        let unitsInpDom = document.getElementById('units');
        let amountInpDom = document.getElementById('amount');
        let dueDateInpDom = document.getElementById('due_date');

        let selectedBillTypeId = 0;
        let selectedLocationId = 0;

        function trackBillType(elem) {
            selectedBillTypeId = 0;
            if (elem.value != '') {
                locationSelectDom.disabled = false;
                selectedBillTypeId = elem.closest('.selectParent').querySelector('ul li.selected').dataset.value;
            } else {
                locationSelectDom.disabled = true;
                selectThisOption(locationSelectDom.closest('.selectParent').querySelector('ul li'));
            }
        }

        function trackLocation(elem) {
            selectedLocationId = 0;
            if (elem.value != '' && billTypeSelectDom.value != '') {
                accountSelectDom.disabled = false;
                selectedLocationId = elem.closest('.selectParent').querySelector('ul li.selected').dataset.value;
                getUtilityAccounts();
            } else {
                accountSelectDom.disabled = true;
                selectThisOption(accountSelectDom.closest('.selectParent').querySelector('ul li'));
            }
        }

        function trackAccount(elem) {
            if (elem.value != '') {
                monthInpDom.disabled = false;
                unitsInpDom.disabled = false;
                amountInpDom.disabled = false;
                dueDateInpDom.disabled = false;
            } else {
                monthInpDom.disabled = true;
                unitsInpDom.disabled = true;
                amountInpDom.disabled = true;
                dueDateInpDom.disabled = true;
            }
        }

        function getUtilityAccounts() {
            $.ajax({
                    url: '/get-utility-accounts',
                    type: 'POST',
                    data: {
                        bill_type_id: selectedBillTypeId,
                        location_id: selectedLocationId,
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            let ul = accountSelectDom.closest('.selectParent').querySelector('ul');
                            ul.innerHTML = `
                                <li data-for="account" data-value="" onmousedown="selectThisOption(this)" class="py-2 px-3 cursor-pointer rounded-lg transition hover:bg-[var(--h-bg-color)]">
                                    -- Select Account --
                                </li>
                            `;

                            response.data.forEach(account => {
                                ul.innerHTML += `
                                    <li data-for="account" data-value="${account.id}" onmousedown="selectThisOption(this)" class="py-2 px-3 cursor-pointer rounded-lg transition hover:bg-[var(--h-bg-color)] text-nowrap">
                                        ${account.account_title}
                                    </li>
                                `;
                            })

                            selectThisOption(accountSelectDom.closest('.selectParent').querySelector('ul li'));
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
        }
    </script>
@endsection
