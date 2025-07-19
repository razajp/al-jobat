@extends('app')
@section('title', 'Show Customer Payments | ' . app('company')->name)
@section('content')
@php
    $searchFields = [
        "Customer Name" => [
            "id" => "customer_name",
            "type" => "text",
            "placeholder" => "Enter customer name",
            "oninput" => "runDynamicFilter()",
            "dataFilterPath" => "name",
        ],
        "Type" => [
            "id" => "type",
            "type" => "select",
            "options" => [
                        'normal' => ['text' => 'Normal'],
                        'payment program' => ['text' => 'Payment Program'],
                        'recovery' => ['text' => 'Recovery'],
                    ],
            "onchange" => "runDynamicFilter()",
            "dataFilterPath" => "details.Type",
        ],
        "Method" => [
            "id" => "method",
            "type" => "select",
            "options" => [
                        'cash' => ['text' => 'Cash'],
                        'cheque' => ['text' => 'Cheque'],
                        'slip' => ['text' => 'Slip'],
                        'program' => ['text' => 'Program'],
                        'adjustment' => ['text' => 'Adjustment'],
                    ],
            "onchange" => "runDynamicFilter()",
            "dataFilterPath" => "details.Method",
        ],
        "Issued" => [
            "id" => "issued",
            "type" => "select",
            "options" => [
                        'Issued' => ['text' => 'Issued'],
                        'Not Issued' => ['text' => 'Not Issued'],
                    ],
            "onchange" => "runDynamicFilter()",
            "dataFilterPath" => "issued",
        ],
        "Status" => [
            "id" => "status",
            "type" => "select",
            "options" => [
                        'Cleared' => ['text' => 'Cleared'],
                        'Pending' => ['text' => 'Pending'],
                    ],
            "onchange" => "runDynamicFilter()",
            "dataFilterPath" => "clearStatus",
        ],
        "Date Range" => [
            "id" => "date_range_start",
            "type" => "date",
            "id2" => "date_range_end",
            "type2" => "date",
            "oninput" => "runDynamicFilter()",
            "dataFilterPath" => "date",
        ]
    ];
@endphp
    <div class="w-[80%] mx-auto">
        <x-search-header heading="Customer Payments" :search_fields=$searchFields/>
    </div>
    
    <!-- Main Content -->
    <section class="text-center mx-auto ">
        <div
            class="show-box mx-auto w-[80%] h-[70vh] bg-[var(--secondary-bg-color)] border border-[var(--glass-border-color)]/20 rounded-xl shadow overflow-y-auto pt-8.5 relative">
            <x-form-title-bar title="Show Customer Payments" changeLayoutBtn layout="{{ $authLayout }}" />

            @if (count($payments) > 0)
                <div class="absolute bottom-3 right-3 flex items-center gap-2 w-fll z-50">
                    <x-section-navigation-button link="{{ route('customer-payments.create') }}" title="Add New Payment" icon="fa-plus" />
                </div>

                <div class="details h-full z-40">
                    <div class="container-parent h-full overflow-y-auto my-scrollbar-2">
                        <div class="card_container px-3 h-full flex flex-col">
                            <div id="table-head" class="grid grid-cols-5 bg-[var(--h-bg-color)] rounded-lg font-medium py-2 hidden mt-4 mx-2">
                                <div class="text-center">Customer</div>
                                <div class="text-center">Type</div>
                                <div class="text-center">Method</div>
                                <div class="text-center">Date</div>
                                <div class="text-center">Amount</div>
                            </div>
                            <p id="noItemsError" style="display: none" class="text-sm text-[var(--border-error)] mt-3">No items found</p>
                            <div>
                                <div class="search_container grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 overflow-y-auto grow my-scrollbar-2">
                                    {{-- class="search_container overflow-y-auto grow my-scrollbar-2"> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="no-article-message w-full h-full flex flex-col items-center justify-center gap-2">
                    <h1 class="text-sm text-[var(--secondary-text)] capitalize">No Payment Found</h1>
                    <a href="{{ route('customer-payments.create') }}"
                        class="text-sm bg-[var(--primary-color)] text-[var(--text-color)] px-4 py-2 rounded-md hover:bg-[var(--h-primary-color)] hover:scale-105 hover:mb-2 transition-all duration-300 ease-in-out font-semibold">Add
                        New</a>
                </div>
            @endif
        </div>
    </section>

    <script>
        let companyData = @json(app('company'));
        let authLayout = '{{ $authLayout }}';

        function createRow(data) {
            return `
            <div id="${data.id}" oncontextmenu='${data.oncontextmenu || ""}' onclick='${data.onclick || ""}'
                class="item row relative group grid text- grid-cols-5 border-b border-[var(--h-bg-color)] items-center py-2 cursor-pointer hover:bg-[var(--h-secondary-bg-color)] transition-all fade-in ease-in-out"
                data-json='${JSON.stringify(data)}'>

                <span class="text-center">${data.name}</span>
                <span class="text-center">${data.details["Type"]}</span>
                <span class="text-center">${data.details["Method"]}</span>
                <span class="text-center">${data.details['Date']}</span>
                <span class="text-center">${data.details['Amount']}</span>
            </div>`;
        }

        const fetchedData = @json($payments);
        let allDataArray = fetchedData.map(item => {
            return {
                id: item.id,
                name: item.customer.customer_name,
                details: {
                    'Type': item.type.replace('_', ' '),
                    'Method': item.method,
                    'Date': formatDate(item.date),
                    'Amount': formatNumbersWithDigits(item.amount, 1, 1),
                },
                data: item,
                ...((item.method == 'cheque' || item.method == 'slip') && { issued: item.issued }),
                ...((item.method == 'cheque' || item.method == 'slip') && (item.clear_date ? { clearStatus: 'Cleared'} : { clearStatus: 'Pending'} )),
                oncontextmenu: "generateContextMenu(event)",
                onclick: "generateModal(this)",
                visible: true,
            };
        });

        function generateClearModal(data) {
            console.log(data);

            let modalData = {
                id: 'clearModal',
                class: 'h-auto',
                name: 'Clear Payment',
                method: 'POST',
                action: `/customer-payments/${data.id}/clear`,
                fields: [
                    {
                        category: 'input',
                        name: 'clear_date',
                        label: 'Clear Date',
                        type: 'date',
                        min: (data.cheque_date || data.slip_date)?.split('T')[0],
                        max: new Date().toISOString().split('T')[0],
                        required: true,
                    },
                    {
                        category: 'input',
                        name: 'remarks',
                        label: 'Remarks',
                        type: 'text',
                        placeholder: 'Enter remarks',
                    },
                ],
                fieldsGridCount: '2',
                bottomActions: [
                    {id: 'clear', text: 'Clear', type: 'submit'},
                ],
            };
            createModal(modalData);
        }

        function generatePartialClearModal(data) {
            console.log(data);

            let modalData = {
                id: 'partialClearModal',
                class: 'h-auto',
                name: 'Clear Payment',
                method: 'POST',
                action: `/customer-payments/${data.id}/partial-clear`,
                fields: [
                    {
                        category: 'input',
                        name: 'clear_date',
                        label: 'Clear Date',
                        type: 'date',
                        min: (data.cheque_date || data.slip_date)?.split('T')[0],
                        max: new Date().toISOString().split('T')[0],
                        required: true,
                    },
                    {
                        category: 'explicitHtml',
                        html: `
                            <x-select class="" label="Bank Account" name="bank_account_id" id="bank_account_id" :options="[]" required showDefault />
                        `,
                    },
                    {
                        category: 'explicitHtml',
                        html: `
                            <x-input label="Amount" name="amount" id="amount" type="number" placeholder="Enter amount" required/>
                        `,
                    },
                    {
                        category: 'explicitHtml',
                        html: `
                            <x-input label="Reff. No." name="reff_no" id="reff_no" placeholder="Enter reff. no." required/>
                        `,
                    },
                    {
                        category: 'input',
                        name: 'remarks',
                        label: 'Remarks',
                        type: 'text',
                        placeholder: 'Enter remarks',
                        full: true
                    },
                ],
                fieldsGridCount: '2',
                bottomActions: [
                    {id: 'clear', text: 'Clear', type: 'submit'},
                ],
            };
            createModal(modalData);

            let bankAccounts = data.cheque?.supplier?.bank_accounts;
            let form = document.querySelector('#partialClearModal');
            let bankAccountInpDom = form.querySelector('input[id="bank_account_id"]');
            let bankAccountDom = form.querySelector('ul[data-for="bank_account_id"]');
            
            bankAccountInpDom.disabled = false;
            bankAccountInpDom.value = '-- Select bank account --';
            options = `
                <li data-for="bank_account_id" data-value=" " onmousedown="selectThisOption(this)" class="py-2 px-3 cursor-pointer rounded-lg transition hover:bg-[var(--h-bg-color)] text-nowrap overflow-scroll my-scrollbar-2 selected "">-- Select bank account --</li>
            `;
            bankAccounts.forEach(bankAccount => {
                console.log(bankAccount);
                
                options += `
                    <li data-for="bank_account_id" data-value="${bankAccount.id}" onmousedown="selectThisOption(this)" class="py-2 px-3 cursor-pointer rounded-lg transition hover:bg-[var(--h-bg-color)] text-nowrap overflow-scroll my-scrollbar-2">${bankAccount.account_title}</li>
                `;
            });
            bankAccountDom.innerHTML = options;
        }
        
        function generateContextMenu(e) {
            e.preventDefault();
            let item = e.target.closest('.item');
            let data = JSON.parse(item.dataset.json);

            let contextMenuData = {
                item: item,
                data: data,
                x: e.pageX,
                y: e.pageY,
                actions: [],
            };

            if (
                (data.data.method === 'cheque' || data.data.method === 'slip') &&
                (
                    (data.data.method === 'cheque' && new Date(data.data.cheque_date) <= new Date()) ||
                    (data.data.method === 'slip' && new Date(data.data.slip_date) <= new Date())
                )
            ) {
                if (data.data.clear_date == null) {
                    contextMenuData.actions.push(
                        {id: 'clear', text: 'Clear', onclick: `generateClearModal(${JSON.stringify(data.data)})`},
                    );
                }

                if (data.data.clear_date == null && data.data.issued == 'Issued') {
                    contextMenuData.actions.push(
                        {id: 'partial-clear', text: 'Partial Clear', onclick: `generatePartialClearModal(${JSON.stringify(data.data)})`},
                    );
                }
            }

            createContextMenu(contextMenuData);
        }

        function generateModal(item) {
            let data = JSON.parse(item.dataset.json);

            let modalData = {
                id: 'modalForm',
                class: 'h-auto',
                name: data.name,
                details: {
                    'Date': data.details['Date'],
                    'Amount': data.details['Amount'],
                    'Type': data.details['Type'],
                    'Method': data.details['Method'],
                    'hr': true,
                    ...(data.data.cheque_no && { 'Cheque No': data.data.cheque_no }),
                    ...(data.data.slip_no && { 'Slip No': data.data.slip_no }),
                    ...(data.data.transition_id && { 'Transition Id': data.data.transition_id }),
                    ...(data.data.bank && { 'Bank': data.data.bank }),
                    ...(data.data.cheque_date && { 'Cheque Date': formatDate(data.data.cheque_date) }),
                    ...(data.data.slip_date && { 'Slip Date': formatDate(data.data.slip_date) }),
                    // ...(data.data.clear_date && { 'Clear Date': formatDate(data.data.clear_date) }),
                    ...(data.data.clear_amount && { 'Clear Amount': formatNumbersWithDigits(data.data.clear_amount, 1, 1) }),
                    ...((data.data.method == 'cheque' || data.data.method == 'slip') && (data.data.clear_date ? { 'Clear Date': formatDate(data.data.clear_date)} : { 'Clear Date': 'Pending'} )),
                    ...((data.data.method == 'cheque' || data.data.method == 'slip') && { 'Issued': data.data.issued }),
                    'Remarks': data.data.remarks || 'No Remarks',
                },
                bottomActions: [],
            }

            if (
                (data.data.method === 'cheque' || data.data.method === 'slip') &&
                (
                    (data.data.method === 'cheque' && new Date(data.data.cheque_date) <= new Date()) ||
                    (data.data.method === 'slip' && new Date(data.data.slip_date) <= new Date())
                )
            ) {
                if (data.data.clear_date == null) {
                    modalData.bottomActions.push(
                        {id: 'clear', text: 'Clear', onclick: `generateClearModal(${JSON.stringify(data.data)})`},
                    );
                }

                if (data.data.clear_date == null && data.data.issued == 'Issued') {
                    modalData.bottomActions.push(
                        {id: 'partial-clear', text: 'Partial Clear', onclick: `generatePartialClearModal(${JSON.stringify(data.data)})`},
                    );
                }
            }

            createModal(modalData);
        }
    </script>
@endsection
