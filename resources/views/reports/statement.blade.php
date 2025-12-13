@extends('app')
@section('title', 'Statement | ' . $client_company->name)
@section('content')
@php
    $companyData = $client_company;
    $statementType = Auth::user()->statement_type;
@endphp
    <div class="switch-btn-container flex absolute top-3 md:top-17 left-3 md:left-5 z-4">
        <div class="switch-btn relative flex border-3 border-[var(--secondary-bg-color)] bg-[var(--secondary-bg-color)] rounded-2xl overflow-hidden">
            <!-- Highlight rectangle -->
            <div id="highlight" class="absolute h-full rounded-xl bg-[var(--bg-color)] transition-all duration-300 ease-in-out z-0"></div>

            <!-- Buttons -->
            <button id="summarizedBtn" type="button" class="relative z-10 px-3.5 md:px-5 py-1.5 md:py-2 cursor-pointer rounded-xl transition-colors duration-300" onclick="setVoucherType(this, 'summarized')">
                <div class="hidden md:block">Summarized</div>
                <div class="block md:hidden"><i class="fas fa-cart-shopping text-xs"></i></div>
            </button>
            <button id="detailedBtn" type="button" class="relative z-10 px-3.5 md:px-5 py-1.5 md:py-2 cursor-pointer rounded-xl transition-colors duration-300" onclick="setVoucherType(this, 'detailed')">
                <div class="hidden md:block">Detailed</div>
                <div class="block md:hidden"><i class="fas fa-box-open text-xs"></i></div>
            </button>
        </div>
    </div>

    <script>
        let btnTypeGlobal = "summarized";

        function setVoucherType(btn, btnType) {
            doHide = true;
            // check if its already selected
            if (btnTypeGlobal == btnType) {
                return;
            }

            $.ajax({
                url: "/set-statement-type",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    statement_type: btnType
                },
                success: function () {
                    location.reload();
                },
                error: function () {
                    alert("Failed to update statement type.");
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
            @if($statementType == 'summarized')
                const activeBtn = document.querySelector("#summarizedBtn");
                moveHighlight(activeBtn, "summarized");
            @else
                const activeBtn = document.querySelector("#detailedBtn");
                moveHighlight(activeBtn, "detailed");
            @endif
        };
    </script>

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
                        'supplier' => ['text' => 'Supplier'],
                        'bank_account' => ['text' => 'Bank Account'],
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
                        onchange="updateDateConstraints()"
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
                        onchange="updateDateConstraints()"
                    />
                </div>
            </div>
        </div>

        <!-- Step 2: view order -->
        <div class="step2 hidden space-y-4 text-black h-[35rem] overflow-y-auto my-scrollbar-2">
            @if (isset($data))
                @php
                    $statements = collect($data['statements']);
                    $balance = $data['opening_balance'];

                    // Pehle page ke liye 26 rows lo
                    $firstPage = $statements->take(26);

                    // Bachi hui rows ko 29-29 ke chunks mai tod do
                    $otherPages = $statements->skip(26)->chunk(29);
                @endphp

                {{-- First Page (26 rows) --}}
                <div id="preview-container" class="h-full relative">
                    <div class="preview-page w-[210mm] h-[297mm] mx-auto overflow-hidden relative bg-white p-[0.19in] rounded-md">
                        <div id="preview" class="preview flex flex-col h-full">
                            <div id="preview-document" class="preview-document flex flex-col h-full px-2">

                                {{-- Company Logo + Banner --}}
                                <div id="preview-banner" class="preview-banner w-full flex justify-between items-center pl-5 pr-8">
                                    <div class="left">
                                        <div class="company-logo">
                                            <img src="{{ asset('images/'.$companyData->logo) }}" alt="garmentsos-pro"
                                                class="w-[12rem]" />
                                        </div>
                                    </div>
                                    <div class="right">
                                        <div>
                                            <h1 class="text-2xl font-medium text-[var(--primary-color)] pr-2 capitalize">{{ $data['category' ]}} Statement</h1>
                                            <div class='mt-1 text-sm'>{{ $companyData->phone_number }}</div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="w-full my-3 border-gray-700">

                                {{-- Header Info --}}
                                <div id="preview-header" class="preview-header w-full flex justify-between px-5">
                                    <div class="left my-auto pr-3 text-sm text-gray-800 space-y-1.5">
                                        <div class="date-range leading-none">Date: {{ $data['date'] }}</div>
                                        <div class="opening-balance leading-none">Opening Balance: Rs.{{ number_format($data['opening_balance']) }}</div>
                                        <div class="closing-balance leading-none">Closing Balance: Rs.{{ number_format($data['closing_balance']) }}</div>
                                    </div>
                                    <div class="center my-auto">
                                        <div class="name capitalize font-semibold text-md">{{ $data['name'] }}</div>
                                    </div>
                                    <div class="right my-auto pr-3 text-sm text-gray-800 space-y-1.5">
                                        <div class="total-bill leading-none">Total Bill: {{ number_format($data['totals']['bill']) }}</div>
                                        <div class="total-payment leading-none">Total Payment: {{ number_format($data['totals']['payment']) }}</div>
                                        <div class="total-balance leading-none">Total Balance: {{ number_format($data['totals']['balance']) }}</div>
                                    </div>
                                </div>

                                <hr class="w-full my-3 border-gray-700">

                                {{-- Table --}}
                                <div id="preview-body" class="preview-body w-[95%] grow mx-auto">
                                    <div class="preview-table w-full">
                                        <div class="table w-full border border-gray-700 rounded-lg pb-2.5 overflow-hidden text-xs">
                                            {{-- Table Header --}}
                                            <div class="thead w-full">
                                                <div class="tr flex justify-between w-full px-4 py-1.5 bg-[var(--primary-color)] text-white text-center">
                                                    <div class="th font-medium w-[4%]">S.No</div>
                                                    <div class="th font-medium w-[12%]">Date</div>
                                                    @if($statementType == 'detailed')
                                                        <div class="th font-medium w-[12%]">Reff. No.</div>
                                                        <div class="th font-medium w-[10%]">Method</div>
                                                        <div class="th font-medium w-[31%]">Description</div>
                                                    @endif
                                                    <div class="th font-medium w-[9%]">Bill</div>
                                                    <div class="th font-medium w-[9%]">Payment</div>
                                                    <div class="th font-medium w-[9%]">Balance</div>
                                                </div>
                                            </div>

                                            {{-- Table Body --}}
                                            <div id="tbody" class="tbody w-full">
                                                @foreach ($firstPage as $statement)
                                                    @php
                                                        if ($statement['type'] == 'invoice') {
                                                            $balance += $statement['bill'];
                                                        } elseif ($statement['type'] == 'payment') {
                                                            $balance -= $statement['payment'];
                                                        }

                                                        if ($loop->iteration == 1) {
                                                            $hrClass = 'mb-2';
                                                        } else {
                                                            $hrClass = 'my-2';
                                                        }
                                                    @endphp
                                                    <div>
                                                        <hr class="w-full {{ $hrClass }} border-gray-700">
                                                        <div class="tr flex justify-between w-full px-4 text-center">
                                                            <div class="td font-semibold w-[4%]">{{ $loop->iteration }}.</div>
                                                            <div class="td font-medium w-[12%]">{{ $statement['date']->format('d-M-Y') }}</div>
                                                            @if($statementType == 'detailed')
                                                                <div class="td font-medium w-[12%]">{{ $statement['reff_no'] }}</div>
                                                                <div class="td font-medium w-[10%] capitalize">{{ $statement['method'] ?? "-" }}</div>
                                                                <div class="td font-medium w-[31%] text-nowrap overflow-hidden">{{ $statement['description'] ?? "-" }}</div>
                                                            @endif
                                                            <div class="td font-medium w-[9%]">{{ number_format($statement['bill']) ?? "-" }}</div>
                                                            <div class="td font-medium w-[9%]">{{ number_format($statement['payment']) ?? "-" }}</div>
                                                            <div class="td font-medium w-[9%]">{{ number_format($balance) }}</div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Footer --}}
                                <hr class="w-full my-3 border-gray-700">
                                <div class="tfooter flex w-full text-sm px-4 justify-between text-gray-800 leading-none text-xs">
                                    <p>Powered by SparkPair &copy; 2025 SparkPair | +92 316 5825495</p>
                                    <p>Page 1 of {{ 1 + $otherPages->count() }}</p>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- Other Pages (29 rows each) --}}
                    @foreach ($otherPages as $pageIndex => $chunk)
                        <hr class="w-full my-3 border-gray-500">
                        <div class="preview-page w-[210mm] h-[297mm] mx-auto overflow-hidden relative bg-white p-[0.19in] rounded-md">
                            <div id="preview" class="preview flex flex-col h-full">
                                <div id="preview-document" class="preview-document flex flex-col h-full px-2">

                                    {{-- Banner --}}
                                    <div id="preview-banner" class="preview-banner w-full flex justify-between items-center pl-5 pr-8">
                                        <div class="left">
                                            <div class="company-logo">
                                                <img src="{{ asset('images/'.$companyData->logo) }}" alt="garmentsos-pro"
                                                    class="w-[10.5rem]" />
                                            </div>
                                        </div>
                                        <div class="right">
                                            <div>
                                                <h1 class="text-xl font-medium text-[var(--primary-color)] pr-2 leading-none capitalize">{{ $data['category' ]}} Statement</h1>
                                                <div class='text-xs'>{{ $data['name'] }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="w-full mt-1.5 mb-3 border-gray-700">

                                    {{-- Table --}}
                                    <div id="preview-body" class="preview-body w-[95%] grow mx-auto">
                                        <div class="preview-table w-full">
                                            <div class="table w-full border border-gray-700 rounded-lg pb-2.5 overflow-hidden text-xs">
                                                {{-- Table Header --}}
                                                <div class="thead w-full">
                                                    <div class="tr flex justify-between w-full px-4 py-1.5 bg-[var(--primary-color)] text-white text-center">
                                                        <div class="th font-medium w-[4%]">S.No</div>
                                                        <div class="th font-medium w-[12%]">Date</div>
                                                        @if($statementType == 'detailed')
                                                            <div class="th font-medium w-[12%]">Reff. No.</div>
                                                            <div class="th font-medium w-[10%]">Method</div>
                                                            <div class="th font-medium w-[31%]">Description</div>
                                                        @endif
                                                        <div class="th font-medium w-[9%]">Bill</div>
                                                        <div class="th font-medium w-[9%]">Payment</div>
                                                        <div class="th font-medium w-[9%]">Balance</div>
                                                    </div>
                                                </div>

                                                {{-- Table Body --}}
                                                <div id="tbody" class="tbody w-full">
                                                    @foreach ($chunk as $statement)
                                                        @php
                                                            if ($statement['type'] == 'invoice') {
                                                                $balance += $statement['bill'];
                                                            } elseif ($statement['type'] == 'payment') {
                                                                $balance -= $statement['payment'];
                                                            }

                                                            if ($loop->iteration == 1) {
                                                                $hrClass = 'mb-2';
                                                            } else {
                                                                $hrClass = 'my-2';
                                                            }
                                                        @endphp
                                                        <div>
                                                            <hr class="w-full {{ $hrClass }} border-gray-700">
                                                            <div class="tr flex justify-between w-full px-4 text-center">
                                                                <div class="td font-semibold w-[4%]">{{ $loop->iteration + 26 + ($pageIndex * 29) }}.</div>
                                                                <div class="td font-medium w-[12%]">{{ $statement['date']->format('d-M-Y') }}</div>
                                                                @if($statementType == 'detailed')
                                                                    <div class="td font-medium w-[12%]">{{ $statement['reff_no'] }}</div>
                                                                    <div class="td font-medium w-[10%] capitalize">{{ $statement['method'] ?? "-" }}</div>
                                                                    <div class="td font-medium w-[31%] text-nowrap overflow-hidden">{{ $statement['description'] ?? "-" }}</div>
                                                                @endif
                                                                <div class="td font-medium w-[9%]">{{ number_format($statement['bill']) ?? "-" }}</div>
                                                                <div class="td font-medium w-[9%]">{{ number_format($statement['payment']) ?? "-" }}</div>
                                                                <div class="td font-medium w-[9%]">{{ number_format($balance) }}</div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Footer --}}
                                    <hr class="w-full my-3 border-gray-700">
                                    <div class="tfooter flex w-full text-sm px-4 justify-between text-gray-800 leading-none text-xs">
                                        <p>Powered by SparkPair &copy; 2025 SparkPair | +92 316 5825495</p>
                                        <p>Page {{ $pageIndex + 2 }} of {{ 1 + $otherPages->count() }}</p>
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </form>

    <script>
        const today = new Date();
        const nameSelect = document.getElementById('nameSelect');
        const nameSelectDropDown = nameSelect.parentElement.parentElement.parentElement.querySelector('ul.optionsDropdown');
        const rangeSelect = document.getElementById('range');
        const rangeSelectDropDown = rangeSelect.parentElement.parentElement.parentElement.querySelector('ul.optionsDropdown');

        const dateFrom = document.getElementById('date_from');
        const dateTo = document.getElementById('date_to');
        let regDate;

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
                            let displayText = '';
                            if (category === 'customer') {
                                displayText = item.customer_name + ' | ' + item.city?.short_title;
                            } else if (category === 'supplier') {
                                displayText = item.supplier_name || '';
                            } else if (category === 'bank_account') {
                                displayText = item.account_title || '';
                            }

                            clutter += `
                                <li data-for="nameSelect" data-value="${item.id}" data-reg-date="${item.date}"
                                    onmousedown="selectThisOption(this)"
                                    class="py-2 px-3 cursor-pointer rounded-lg transition hover:bg-[var(--h-bg-color)] text-nowrap overflow-x-auto scrollbar-hidden">
                                    ${displayText}
                                </li>
                            `;
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
            if (nameSelectDbInput.value) {
                let selectedName = nameSelectDbInput.nextElementSibling.querySelector(`li[data-value="${nameSelectDbInput.value}"]`);

                if (!selectedName) return;

                let rawRegDate = new Date(selectedName.dataset.regDate);
                const d = new Date(rawRegDate);
                regDate = d.toISOString().split("T")[0];
                dateFrom.min = regDate;
                let today = new Date();

                // Helper function to calculate months difference
                function monthDiff(d1, d2) {
                    let months = (d2.getFullYear() - d1.getFullYear()) * 12;
                    months -= d1.getMonth();
                    months += d2.getMonth();
                    return months <= 0 ? 0 : months;
                }

                let monthsSinceReg = monthDiff(rawRegDate, today);

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
                        class="py-2 px-3 cursor-pointer rounded-lg transition hover:bg-[var(--h-bg-color)] text-nowrap overflow-x-auto scrollbar-hidden">
                        ${r.label}
                    </li>`).join("");

                rangeSelectDropDown.innerHTML = clutter;

                // Enable range select
                rangeSelect.disabled = false;
            } else {
                rangeSelect.value = '';
                rangeSelect.disabled = true;
            }
        }

        function updateDateConstraints() {
            const dateFrom = document.getElementById('date_from');
            const dateTo = document.getElementById('date_to');

            if (dateFrom.value) {
                // "to" ka min = "from"
                dateTo.min = dateFrom.value;
            }

            if (dateTo.value) {
                // "from" ka max = "to"
                dateFrom.max = dateTo.value;
            }
        }

        // Helper: local YYYY-MM-DD (without UTC shift)
        const formatDateLocal = (d) => {
            const y = d.getFullYear();
            const m = String(d.getMonth() + 1).padStart(2, "0");
            const day = String(d.getDate()).padStart(2, "0");
            return `${y}-${m}-${day}`;
        };

        function isLastDayOfMonth(date) {
            return date.getDate() === new Date(date.getFullYear(), date.getMonth() + 1, 0).getDate();
        }

        function applyRange(rangeValue) {
            let from = null, to = null;

            // check if today is last date of month
            const todayIsLast = isLastDayOfMonth(today);

            switch (rangeValue) {
                case "custom":
                    dateFrom.value = regDate;
                    dateTo.value = new Date().toISOString().split("T")[0];
                    dateFrom.disabled = false;
                    dateTo.disabled = false;
                    return;

                case "current_month":
                    from = new Date(today.getFullYear(), today.getMonth(), 1);
                    to = today; // only till today
                    break;

                case "last_month":
                    from = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                    to = today; // include last month fully + current month till today
                    break;

                case "last_three_months":
                    from = new Date(today.getFullYear(), today.getMonth() - 2, 1);
                    to = today;
                    break;

                case "last_six_months":
                    from = new Date(today.getFullYear(), today.getMonth() - 5, 1);
                    to = today;
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
                    type: '{{ $statementType }}',
                    category: category,
                    id: id,
                    date_from: dateFrom !== '' ? dateFrom : regDate,
                    date_to: dateTo !== '' ? dateTo : today.toISOString().split("T")[0],
                },
                success: function(response) {
                    renderStatement(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching statement:', error);
                }
            });
        }

        function renderStatement(response) {
            // Parse the HTML string into a jQuery object
            const $responseHtml = $(response);

            // Find the .step2 element inside the response
            const $previewInResponse = $responseHtml.find('.step2');

            if ($previewInResponse.length) {
                // Replace the current page's .step2 innerHTML
                $('.step2').html($previewInResponse.html());
            } else {
                console.warn('.step2 not found in response HTML.');
            }
        }

        function onClickOnPrintBtn() {
            const preview = document.getElementById('preview-container'); // preview content

            // ✅ Clone so that original DOM safe rahe
            let clone = preview.cloneNode(true);

            // ✅ Sirf direct child <hr> (pages ke beech) remove karo
            clone.querySelectorAll(":scope > hr").forEach(hr => hr.remove());

            // Agar pehle se iframe hai to usko hatao
            let oldIframe = document.getElementById('printIframe');
            if (oldIframe) {
                oldIframe.remove();
            }

            // Naya iframe banao
            let printIframe = document.createElement('iframe');
            printIframe.id = "printIframe";
            printIframe.style.position = "absolute";
            printIframe.style.width = "0px";
            printIframe.style.height = "0px";
            printIframe.style.border = "none";
            printIframe.style.display = "none";

            document.body.appendChild(printIframe);

            let printDocument = printIframe.contentDocument || printIframe.contentWindow.document;
            printDocument.open();

            // ✅ Copy styles from current page
            const headContent = document.head.innerHTML;

            printDocument.write(`
                <html>
                    <head>
                        <title>Print Statement</title>
                        ${headContent}
                        <style>
                            @page {
                                size: A4;
                                margin: 0;
                            }

                            body {
                                margin: 0;
                                padding: 0;
                                background: #fff;
                            }
                        </style>
                    </head>
                    <body>
                        ${clone.innerHTML} <!-- ✅ only outside <hr> removed -->
                    </body>
                </html>
            `);

            printDocument.close();

            // Print jab iframe load ho jaye
            printIframe.onload = () => {
                printIframe.contentWindow.focus();
                printIframe.contentWindow.print();
            };
        }

        function validateForNextStep() {
            getStatement();
            return true;
        }
    </script>
@endsection
