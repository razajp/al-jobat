@extends('app')
@section('title', 'Add Employee Payment | ' . app('company')->name)
@section('content')
    @php
        $category_options = [
            'staff' => ['text' => 'Staff'],
            'worker' => ['text' => 'Worker'],
        ];
        $method_options = [
            'cash' => ['text' => 'Cash'],
            'adjustment' => ['text' => 'Adjustment'],
        ];
    @endphp
    <!-- Progress Bar -->
    <div class="mb-5 max-w-4xl mx-auto">
        <x-search-header heading="Add Employee Payment" link linkText="Show Payments" linkHref="{{ route('employee-payments.index') }}"/>
    </div>

    <div class="row max-w-4xl mx-auto flex gap-4">
        <!-- Form -->
        <form id="form" action="{{ route('employee-payments.store') }}" method="post"
            class="bg-[var(--secondary-bg-color)] text-sm rounded-xl shadow-lg p-7 border border-[var(--h-bg-color)] pt-12 mx-auto relative overflow-hidden w-full">
            @csrf
            <x-form-title-bar title="Add Employee Payment" />

            <div class="step space-y-4 overflow-y-auto max-h-[65vh] p-1 my-scrollbar-2">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- category --}}
                    <x-select
                        label="Category"
                        name="category"
                        id="category"
                        :options="$category_options"
                        required
                        showDefault
                        onchange="trackCategoryState(this)"
                    />

                    {{-- employee_name --}}
                    <x-select
                        label="Employee Name"
                        name="employee_id"
                        id="employee"
                        required
                        showDefault
                        onchange="trackEmployeeState(this)"
                    />

                    {{-- balance --}}
                    <x-input label="Balance" placeholder="Select employee first" name="balance" type="amount" id="balance" dataValidate="amount" disabled />

                    {{-- date --}}
                    <x-input label="Date" name="date" id="date" type="date" validateMax max="{{ now()->toDateString() }}" required disabled />

                    {{-- method --}}
                    <x-select
                        label="Method"
                        name="method"
                        id="method"
                        :options="$method_options"
                        required
                        disabled
                        showDefault
                    />

                    {{-- amount --}}
                    <x-input label="Amount" name="amount" id="amount" type="amount" required disabled dataValidate="requied|amount" placeholder="Enter amount" />
                </div>
            </div>
            <div class="w-full flex justify-end mt-4">
                <button type="submit"
                    class="px-10 py-2 bg-[var(--bg-success)] border border-[var(--bg-success)] text-[var(--text-success)] font-medium text-nowrap rounded-lg hover:bg-[var(--h-bg-success)] hover:border-[var(--border-success)] hover:scale-90 transition-all duration-300 ease-in-out cursor-pointer">
                    <i class='fas fa-save mr-1'></i> Save
                </button>
            </div>
        </form>
    </div>

    <script>
        let employeeSelectDom = document.getElementById('employee');
        let balanceDom = document.getElementById('balance');
        let dateSelectDom = document.getElementById('date');
        let methodSelectDom = document.getElementById('method');
        let amoutnInpDom = document.getElementById('amount');

        function trackCategoryState(elem) {
            if (elem.value !== '') {
                $.ajax({
                    url: '/get-employees-by-category',
                    type: 'POST',
                    data: {
                        category: elem.value,
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            let allEmployees = response.data;

                            let employeeUL = employeeSelectDom.closest('.selectParent').querySelector('ul');
                            employeeUL.innerHTML = `
                                <li data-for="employee" data-value="" onmousedown="selectThisOption(this)" class="py-2 px-3 cursor-pointer rounded-lg transition hover:bg-[var(--h-bg-color)] text-nowrap overflow-x-auto scrollbar-hidden capitalize">-- Select ${elem.value} --</li>
                            `;

                            allEmployees.forEach(employee => {
                                employeeUL.innerHTML += `
                                    <li data-for="employee" data-value="${employee.id}" data-option='${JSON.stringify(employee)}' onmousedown="selectThisOption(this)" class="py-2 px-3 cursor-pointer rounded-lg transition hover:bg-[var(--h-bg-color)] text-nowrap overflow-x-auto scrollbar-hidden capitalize">${employee.employee_name} | ${formatNumbersWithDigits(employee.balance, 1, 1)} | ${employee.type.title}</li>
                                `;
                            });

                            selectThisOption(employeeUL.querySelector('li'));
                            employeeSelectDom.disabled = false;
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }
        }

        function trackEmployeeState(elem) {
            if (elem.value !== '') {
                let selectedEmployee = JSON.parse(elem.closest('.selectParent').querySelector('ul li.selected').dataset.option || '{}');
                balanceDom.value = selectedEmployee.balance;
                dateSelectDom.disabled = false;
                dateSelectDom.min = selectedEmployee.joining_date;
                methodSelectDom.disabled = false;
                amoutnInpDom.disabled = false;
            } else {
                balanceDom.value = '';
                dateSelectDom.disabled = true;
                methodSelectDom.disabled = true;
                amoutnInpDom.disabled = true;
            }
        }
    </script>
@endsection
