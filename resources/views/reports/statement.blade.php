@extends('app')
@section('title', 'Generate Order | ' . app('company')->name)
@section('content')
    <!-- Main Content -->
    <!-- Progress Bar -->
    <div class="mb-5 max-w-4xl mx-auto">
        <x-search-header heading="Statement"/>
        <x-progress-bar :steps="['Generate Statement', 'Preview']" :currentStep="1" />
    </div>

    <!-- Form -->
    <form id="form" action="{{ route('orders.store') }}" method="post" enctype="multipart/form-data"
        class="bg-[var(--secondary-bg-color)] text-sm rounded-xl shadow-lg p-8 border border-[var(--glass-border-color)]/20 pt-14 max-w-4xl mx-auto  relative overflow-hidden">
        @csrf
        <x-form-title-bar title="Generate Statement" />

        <!-- Step 1: Generate Staement -->
        <div class="step1 space-y-4 ">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- category --}}
                <x-select
                    label="Category"
                    name="category"
                    id="category"
                    :options="[
                        'supplier' => ['text' => 'Supplier'],
                        'customer' => ['text' => 'Customer'],
                    ]"
                    showDefault
                    onchange="fetchNames(this.value)"
                />

                {{-- name --}}
                <x-select
                    label="Name"
                    name="name"
                    id="nameSelect"
                    :options="[]"
                    showDefault
                    onchange="nameChanged(this)"
                />

                <div class="col-span-full grid grid-cols-3 gap-4">
                    {{-- RangeFilter --}}
                    <x-select
                        label="Range"
                        name="range"
                        id="range"
                        :options="[
                            'current_month' => ['text' => 'Current Month'],
                            'last_month' => ['text' => 'Last Month'],
                            'last_three_months' => ['text' => 'Last Three Months'],
                            'last_six_months' => ['text' => 'Last Six Months'],
                            'custom' => ['text' => 'Custom'],
                        ]"
                        showDefault
                        required
                        disabled
                        onchange="applyRange(this.value)"
                    />

                    <!-- date_from -->
                    <x-input
                        label="Date From"
                        name="date_from"
                        id="date_from"
                        validateMax
                        max="{{ now()->toDateString() }}"
                        type="date"
                        required
                        disabled
                    />

                    <!-- date_to -->
                    <x-input
                        label="Date To"
                        name="date_to"
                        id="date_to"
                        validateMax
                        max="{{ now()->toDateString() }}"
                        type="date"
                        required
                        disabled
                    />
                </div>
            </div>
        </div>

        <!-- Step 2: view order -->
        <div class="step2 hidden space-y-4 text-black h-[35rem] overflow-y-auto my-scrollbar-2 bg-white rounded-md">
            <div id="preview-container" class="w-[210mm] h-[297mm] mx-auto overflow-hidden relative">
                <div id="preview" class="preview flex flex-col h-full">
                    <h1 class="text-[var(--border-error)] font-medium text-center mt-5">No Preview avalaible.</h1>
                </div>
            </div>
        </div>
    </form>

    <script>
        const nameSelect = document.getElementById('nameSelect');
        const nameSelectDropDown = nameSelect.parentElement.parentElement.parentElement.querySelector('ul.optionsDropdown');
        const rangeSelect = document.getElementById('range');
        const rangeSelectDropDown = rangeSelect.parentElement.parentElement.parentElement.querySelector('ul.optionsDropdown');

        const dateFrom = document.getElementById('date_from');
        const dateTo = document.getElementById('date_to');

        function fetchNames(category) {
            if (!category) {
                return;
            }

            $.ajax({
                url: "{{ route('reports.statement.get-names') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    category: category,
                },
                success: function(response) {
                    if (response.length > 0) {
                        let clutter = `
                            <li data-for="nameSelect" data-value="" onmousedown="selectThisOption(this)" class="py-2 px-3 cursor-pointer rounded-lg transition hover:bg-[var(--h-bg-color)]">
                                -- Select Name --
                            </li>
                        `;

                        response.forEach(function(item) {
                            clutter += `
                                <li data-for="nameSelect" data-value="${item.id}" data-reg-date="${item.date}" onmousedown="selectThisOption(this)" class="py-2 px-3 cursor-pointer rounded-lg transition hover:bg-[var(--h-bg-color)] text-nowrap overflow-scroll my-scrollbar-2">
                                    ${category == 'customer' ? item.customer_name : category == 'supplier' ? item.supplier_name : ''}
                                </li>
                            `
                        });

                        nameSelectDropDown.innerHTML = clutter;

                        nameSelect.disabled = false; // Enable the select if names are found
                    } else {
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = `
                            <li data-for="nameSelect" data-value="" onmousedown="selectThisOption(this)" class="py-2 px-3 cursor-pointer rounded-lg transition hover:bg-[var(--h-bg-color)]">
                                -- No Names Found --
                            </li>
                        `;
                        nameSelectDropDown.appendChild(option);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching names:', error);
                }
            });
        }

        function nameChanged(nameSelectDbInput) {
            let selectedName = nameSelectDbInput.nextElementSibling.querySelector(`li[data-value="${nameSelectDbInput.value}"]`);

            if (!selectedName) return;

            // Get registration date
            let regDate = new Date(selectedName.dataset.regDate);
            let today = new Date();

            // Helper function to calculate months difference
            function monthDiff(d1, d2) {
                let months = (d2.getFullYear() - d1.getFullYear()) * 12;
                months -= d1.getMonth();
                months += d2.getMonth();
                return months <= 0 ? 0 : months;
            }

            let monthsSinceReg = monthDiff(regDate, today);

            // Build available ranges
            let ranges = [];

            if (monthsSinceReg >= 0) ranges.push({ value: "current_month", label: "Current Month" });
            if (monthsSinceReg >= 1) ranges.push({ value: "last_month", label: "Last Month" });
            if (monthsSinceReg >= 3) ranges.push({ value: "last_three_months", label: "Last Three Months" });
            if (monthsSinceReg >= 6) ranges.push({ value: "last_six_months", label: "Last Six Months" });

            // Custom always appears
            ranges.push({ value: "custom", label: "Custom" });

            // Render dropdown
            let clutter = ranges.map(r => `
                <li data-for="range" data-value="${r.value}"
                    onmousedown="selectThisOption(this)"
                    class="py-2 px-3 cursor-pointer rounded-lg transition hover:bg-[var(--h-bg-color)] text-nowrap overflow-scroll my-scrollbar-2">
                    ${r.label}
                </li>`).join("");

            rangeSelectDropDown.innerHTML = clutter;

            // Enable range select
            rangeSelect.disabled = false;
        }

        // Helper: local YYYY-MM-DD (without UTC shift)
        const formatDateLocal = (d) => {
        const y = d.getFullYear();
        const m = String(d.getMonth() + 1).padStart(2, "0");
        const day = String(d.getDate()).padStart(2, "0");
        return `${y}-${m}-${day}`;
        };

        function applyRange(rangeValue) {
        const today = new Date();
        let from = null, to = null;

        switch (rangeValue) {
            case "custom":
            dateFrom.value = '';
            dateTo.value = '';
            dateFrom.disabled = false;
            dateTo.disabled = false;
            return;

            case "current_month":
            from = new Date(today.getFullYear(), today.getMonth(), 1);
            to = today;
            break;

            case "last_month":
            from = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            to   = new Date(today.getFullYear(), today.getMonth(), 0);
            break;

            case "last_three_months":
            from = new Date(today.getFullYear(), today.getMonth() - 3, 1);
            to   = new Date(today.getFullYear(), today.getMonth(), 0);
            break;

            case "last_six_months":
            from = new Date(today.getFullYear(), today.getMonth() - 6, 1);
            to   = new Date(today.getFullYear(), today.getMonth(), 0);
            break;

            default:
            dateFrom.value = "";
            dateTo.value = "";
            dateFrom.disabled = true;
            dateTo.disabled = true;
            return;
        }

        dateFrom.value = formatDateLocal(from);
        dateTo.value = formatDateLocal(to);

        // non-custom ranges disabled
        dateFrom.disabled = true;
        dateTo.disabled = true;
        }


        function getStatement() {
            const category = document.querySelector('ul[data-for="category"] li.selected').textContent.trim().toLowerCase();
            const id = document.querySelector('input[data-for="nameSelect"]').value;
            const dateFrom = document.getElementById('date_from').value;
            const dateTo = document.getElementById('date_to').value;

            $.ajax({
                url: "{{ route('reports.statement') }}",
                type: 'GET',
                data: {
                    _token: "{{ csrf_token() }}",
                    withData: false,
                    category: category,
                    id: id,
                    date_from: dateFrom,
                    date_to: dateTo,
                },
                success: function(response) {
                    console.log(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching names:', error);
                }
            });
        }

        function validateForNextStep() {
            getStatement();
            return true;
        }
    </script>
@endsection
