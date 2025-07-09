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
            "dataFilterPath" => "customer.customer_name",
        ],
        "Type" => [
            "id" => "type",
            "type" => "select",
            "options" => [
                        'normal' => ['text' => 'Normal'],
                        'payment_program' => ['text' => 'Payment Program'],
                    ],
            "onchange" => "runDynamicFilter()",
            "dataFilterPath" => "type",
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
            "dataFilterPath" => "method",
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
            class="show-box mx-auto w-[80%] h-[70vh] bg-[var(--secondary-bg-color)] rounded-xl shadow overflow-y-auto pt-8.5 relative">
            <x-form-title-bar title="Show Customer Payments" changeLayoutBtn layout="{{ $authLayout }}" />

            @if (count($payments) > 0)
                <div class="absolute bottom-3 right-3 flex items-center gap-2 w-fll z-50">
                    <x-section-navigation-button link="{{ route('customer-payments.create') }}" title="Add New Payment" icon="fa-plus" />
                </div>

                <div class="details h-full z-40">
                    <div class="container-parent h-full overflow-y-auto my-scrollbar-2">
                        <div class="card_container py-0 p-3 h-full flex flex-col">
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
                    'Amount': item.amount,
                },
                data: item,
                oncontextmenu: "generateContextMenu(event)",
                onclick: "generateModal(this)",
                visible: true,
            };
        });
        
        function generateContextMenu(e) {
            e.preventDefault();
            let item = e.target.closest('.item');
            let data = JSON.parse(item.dataset.json);

            let contextMenuData = {
                item: item,
                data: data,
                x: e.pageX,
                y: e.pageY,
            };

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
                    ...(data.data.slip_date && { 'Slip Date': data.data.slip_date }),
                    ...(data.data.clear_date && { 'Clear Date': data.data.clear_date }),
                    'Remarks': data.data.remarks || 'No Remarks',
                },
            }

            createModal(modalData);
        }
    </script>
@endsection
