@extends('app')
@section('title', 'Statement | ' . app('company')->name)
@php
    $companyData = app('company');
@endphp
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
                        'customer' => ['text' => 'Customer'],
                        // 'supplier' => ['text' => 'Supplier'],
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
                    @if (isset($data))
                        <div id="preview-document" class="preview-document flex flex-col h-full px-2">
                            <div id="preview-banner" class="preview-banner w-full flex justify-between items-center mt-4 pl-5 pr-8">
                                <div class="left">
                                    <div class="company-logo">
                                        <img src="{{ asset('images/'.$companyData->logo) }}" alt="Track Point"
                                            class="w-[12rem]" />
                                    </div>
                                </div>
                                <div class="right">
                                    <div>
                                        <h1 class="text-2xl font-medium text-[var(--primary-color)] pr-2">Statement</h1>
                                        <div class='mt-1 text-sm'>{{ $companyData->phone_number }}</div>
                                    </div>
                                </div>
                            </div>
                            <hr class="w-full my-3 border-gray-700">
                            <div id="preview-header" class="preview-header w-full flex justify-between px-5">
                                <div class="left my-auto pr-3 text-sm text-gray-800 space-y-1.5">
                                    <div class="date-range leading-none">Date: {{ $data['date'] }}</div>
                                    <div class="opening-balance leading-none">Opening Balance: Rs.{{ $data['opening_balance'] }}</div>
                                    <div class="closing-balance leading-none">Closing Balance: Rs.{{ $data['closing_balance'] }}</div>
                                </div>
                                <div class="center my-auto">
                                    <div class="name capitalize font-semibold text-md">Customer Name: {{ $data['name'] }}</div>
                                </div>
                                <div class="right my-auto pr-3 text-sm text-gray-800 space-y-1.5">
                                    <div class="total-bill leading-none">Total Bill: {{ $data['totals']['bill'] }}</div>
                                    <div class="total-payment leading-none">Total Payment: {{ $data['totals']['payment'] }}</div>
                                    <div class="total-balance leading-none">Total Balance: {{ $data['totals']['balance'] }}</div>
                                </div>
                            </div>
                            <hr class="w-full my-3 border-gray-700">
                            <div id="preview-body" class="preview-body w-[95%] grow mx-auto">
                                <div class="preview-table w-full">
                                    <div class="table w-full border border-gray-700 rounded-lg pb-2.5 overflow-hidden text-xs">
                                        <div class="thead w-full">
                                            <div class="tr flex justify-between w-full px-4 py-1.5 bg-[var(--primary-color)] text-white text-center">
                                                <div class="th font-medium w-[4%]">S.No</div>
                                                <div class="th font-medium w-[12%]">Date</div>
                                                <div class="th font-medium w-[12%]">Reff. No.</div>
                                                <div class="th font-medium w-[10%]">Method</div>
                                                <div class="th font-medium w-[31%]">Account</div>
                                                <div class="th font-medium w-[9%]">Bill</div>
                                                <div class="th font-medium w-[9%]">Payment</div>
                                                <div class="th font-medium w-[9%]">Balance</div>
                                            </div>
                                        </div>
                                        <div id="tbody" class="tbody w-full">
                                            @php
                                                $balance = 0;
                                            @endphp
                                            @foreach ($data['statements'] as $statement)
                                                @php
                                                    if ($statement['type'] == 'invoice') {
                                                        $balance += $statement['bill'];
                                                    } elseif ($statement['type'] == 'payment') {
                                                        $balance -= $statement['payment'];
                                                    }

                                                    if ($loop->iteration == 1) {
                                                        $hrClass = 'mb-2.5';
                                                    } else {
                                                        $hrClass = 'my-2.5';
                                                    }
                                                @endphp

                                                <div>
                                                    <hr class="w-full {{ $hrClass }} border-gray-700">
                                                    <div class="tr flex justify-between w-full px-4 text-center">
                                                        <div class="td font-semibold w-[4%] text-center">{{ $loop->iteration }}.</div>
                                                        <div class="td font-medium w-[12%] text-center">{{ $statement['date']->format('d-M-Y') }}</div>
                                                        <div class="td font-medium w-[12%] text-center">{{ $statement['reff_no'] }}</div>
                                                        <div class="td font-medium w-[10%]">{{ $statement['method'] ?? "-" }}</div>
                                                        <div class="td font-medium w-[31%] text-nowrap overflow-hidden">{{ $statement['account'] ?? "-" }}</div>
                                                        <div class="td font-medium w-[9%] text-center">{{ number_format($statement['bill']) ?? "-" }}</div>
                                                        <div class="td font-medium w-[9%] text-center">{{ number_format($statement['payment']) ?? "-" }}</div>
                                                        <div class="td font-medium w-[9%] text-center">{{ number_format($balance) }}</div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr class="w-full my-3 border-gray-700">
                            <div class="tfooter flex w-full text-sm px-4 justify-between mb-4 text-gray-800">
                                <P class="leading-none">Powered by SparkPair</P>
                                <p class="leading-none text-sm">&copy; 2025 Spark Pair | +92 316 5825495</p>
                            </div>
                        </div>
                    @endif
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

        function validateForNextStep() {
            getStatement();
            return true;
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
                    renderStatement(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching names:', error);
                }
            });
        }

        function renderStatement(response) {
            // Parse the HTML string into a jQuery object
            const $responseHtml = $(response);

            // Find the #preview element inside the response
            const $previewInResponse = $responseHtml.find('#preview');

            if ($previewInResponse.length) {
                // Replace the current page's #preview innerHTML
                $('#preview').html($previewInResponse.html());
            } else {
                console.warn('#preview not found in response HTML.');
            }
        }

        function onClickOnPrintBtn() {
            const preview = document.getElementById('preview-container'); // preview content

            // Pehle se agar koi iframe hai to usko remove karein
            let oldIframe = document.getElementById('printIframe');
            if (oldIframe) {
                oldIframe.remove();
            }

            // Naya iframe banayein
            let printIframe = document.createElement('iframe');
            printIframe.id = "printIframe";
            printIframe.style.position = "absolute";
            printIframe.style.width = "0px";
            printIframe.style.height = "0px";
            printIframe.style.border = "none";
            printIframe.style.display = "none"; // ✅ Hide iframe

            // Iframe ko body me add karein
            document.body.appendChild(printIframe);

            let printDocument = printIframe.contentDocument || printIframe.contentWindow.document;
            printDocument.open();

            // ✅ Current page ke CSS styles bhi iframe me inject karenge
            const headContent = document.head.innerHTML;

            printDocument.write(`
                <html>
                    <head>
                        <title>Print Cargo List</title>
                        ${headContent} <!-- Copy current styles -->
                        <style>
                            @media print {

                                body {
                                    margin: 0;
                                    padding: 0;
                                    width: 210mm; /* A4 width */
                                    height: 297mm; /* A4 height */

                                }

                                .preview-container, .preview-container * {
                                    page-break-inside: avoid;
                                }
                            }
                        </style>
                    </head>
                    <body>
                        <div class="preview-container pt-3">${preview.innerHTML}</div> <!-- Add the preview content, only innerHTML -->
                    </body>
                </html>
            `);

            printDocument.close();

            // Wait for iframe to load and print
            printIframe.onload = () => {
                // Listen for after print in the iframe's window
                printIframe.contentWindow.onafterprint = () => {
                    console.log("Print dialog closed");
                };

                setTimeout(() => {
                    printIframe.contentWindow.focus();
                    printIframe.contentWindow.print();
                }, 1000);
            };
        }
    </script>
@endsection
