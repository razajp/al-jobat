@extends('app')
@section('title', 'Manage Salary | ' . $client_company->name)
@section('content')
@php
    $type_options = [
        'absent' => ['text' => 'Absent'],
        'late' => ['text' => 'Late'],
    ];
@endphp
    <!-- Main Content -->
    <!-- Progress Bar -->
    <div class="mb-5 max-w-6xl mx-auto">
        <x-search-header heading="Manage Salary" link linkText="Generate Slip" linkHref="{{ route('attendances.generate-slip') }}"/>
    </div>

    <!-- Form -->
    <form id="form" action="{{ route('attendances.manage-salary-post') }}" method="post" enctype="multipart/form-data"
        class="bg-[var(--secondary-bg-color)] text-sm rounded-xl shadow-lg p-8 border border-[var(--glass-border-color)]/20 pt-14 max-w-6xl mx-auto relative overflow-hidden">
        @csrf
        <x-form-title-bar title="Manage Salary" />

        <div class="space-y-4 ">
            <div class="flex items-end gap-4">
                <div class="grow grid grid-cols-4 gap-4">
                    {{-- month --}}
                    <x-input label="Month" name="month" id="month" type="month" required />

                    {{-- employee --}}
                    <x-select label="Employee" name="employee_id" id="employee" :options="$employee_options" showDefault required onchange="trackEmployeeState(this)" />

                    {{-- type --}}
                    <x-select label="Attendance Type" name="type" id="type" :options="$type_options" showDefault required />

                    {{-- count --}}
                    <x-input label="Count" name="count" id="count" type="number" placeholder="Enter count" onkeydown="if(event.key === 'Enter'){ event.preventDefault(); addTypeWithCount(); }" />
                </div>

                <button id="addBtn" type="button" class="bg-[var(--primary-color)] px-4 py-2 rounded-lg hover:bg-[var(--h-primary-color)] transition-all duration-300 ease-in-out text-nowrap cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed" onclick="addTypeWithCount()">Add</button>
            </div>
            {{-- types-table --}}
            <div id="types-table" class="w-full text-left text-sm">
                <div class="grid grid-cols-4 bg-[var(--h-bg-color)] rounded-lg py-2 px-4 mb-4">
                    <div>S.No.</div>
                    <div>Type</div>
                    <div>Count</div>
                    <div class="text-center">Action</div>
                </div>
                <div id="type-list" class="h-[20rem] overflow-y-auto my-scrollbar-2">
                    <div class="text-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4">No Type Added</div>
                </div>
            </div>

            <input type="hidden" name="types_array" id="types" value="">
            <div class="w-full grid grid-cols-4 gap-4 text-sm mt-5 text-nowrap">
                <div class="arears flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                    <div class="grow">Arears - Rs.</div>
                    <input class="text-end w-full" readonly type="amount" data-validate="amount" id="finalArears" />
                </div>
                <div class="salary flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                    <div class="grow">Salary - Rs.</div>
                    <input class="text-end w-full" readonly type="amount" data-validate="amount" id="finalSalary" />
                </div>
                <div class="deduction flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                    <div class="grow">Deduction - Rs.</div>
                    <input class="text-end w-full" readonly type="amount" data-validate="amount" id="finalDeduction" />
                </div>
                <div class="balance flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                    <div class="grow">Balance - Rs.</div>
                    <input class="text-end w-full" readonly name="amount" type="amount" data-validate="amount" id="finalBalance" />
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
        let addedTypes = [];
        let selectedEmployee = {};
        let finalArears = 0;
        let finalSalary = 0;
        let finalDeduction = 0;

        function trackEmployeeState(elem) {
            selectedEmployee = JSON.parse(elem.closest('.selectParent').querySelector('li.selected').dataset.option || '{}');
            finalArears = selectedEmployee.balance || 0;
            finalSalary = selectedEmployee.salary || 0;
            renderFinals();
        }

        function renderFinals() {
            calculateDeduction();
            document.getElementById('finalArears').value = finalArears;
            document.getElementById('finalSalary').value = finalSalary;
            document.getElementById('finalDeduction').value = finalDeduction;
            document.getElementById('finalBalance').value = finalArears + finalSalary - finalDeduction;
            validateAllInputs()
        }
        renderFinals();

        function addTypeWithCount() {
            let typeSelectDom = document.getElementById('type');
            let countInpDom = document.getElementById('count');

            if (typeSelectDom.value !== '' && typeSelectDom.value !== '-- Select Attendance Type --' && countInpDom.value > 0) {
                addedTypes = addedTypes.filter(t => t.type !== typeSelectDom.value);
                let type = {};

                type.type = typeSelectDom.value;
                type.count = countInpDom.value;

                addedTypes.push(type);

                renderTypes();
                renderFinals();

                selectThisOption(typeSelectDom.closest('.selectParent').querySelector('ul li'));
                countInpDom.value = '';
            }
        }

        function renderTypes() {
            let typeListDom = document.getElementById('type-list');

            if (addedTypes.length > 0) {
                typeListDom.innerHTML = '';
                addedTypes.forEach((type, index) => {
                    typeListDom.innerHTML += `
                        <div class="grid grid-cols-4 border-t border-gray-600 py-3 px-4">
                            <div>${index+1}</div>
                            <div class="capitalize" >${type.type}</div>
                            <div>${type.count}</div>
                            <div class="text-center">
                                <button onclick="deselectThisType(${index})" type="button" class="text-[var(--danger-color)] cursor-pointer text-xs px-2 py-1 rounded-lg hover:text-[var(--h-danger-color)] transition-all duration-300 ease-in-out">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    `;
                })
            } else {
                typeListDom.innerHTML = `
                    <div class="text-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4">No Type Added</div>
                `;
            }
            document.getElementById('types').value = JSON.stringify(addedTypes);
        }
        renderTypes();

        function deselectThisType(index) {
            addedTypes.splice(index, 1);
            renderTypes();
            renderFinals();
        }

        function calculateDeduction() {
            finalDeduction = 0
            addedTypes.forEach(type => {
                if (type.type == 'Absent') {
                    finalDeduction += (finalSalary / 30) * type.count;
                } else if (type.type == 'Late') {
                    const equivalentAbsentDays = Math.floor(type.count / 4);
                    finalDeduction += (finalSalary / 30) * equivalentAbsentDays;
                }
            })
        }
    </script>
@endsection
