@extends('app')
@section('title', 'Show Employee Payments | ' . app('company')->name)
@section('content')
@php
    $searchFields = [
        "Beneficiary" => [
            "id" => "beneficiary",
            "type" => "text",
            "placeholder" => "Enter beneficiary",
            "oninput" => "runDynamicFilter()",
            "dataFilterPath" => "beneficiary",
        ],
        "Voucher No." => [
            "id" => "voucher_no",
            "type" => "text",
            "placeholder" => "Enter voucher no.",
            "oninput" => "runDynamicFilter()",
            "dataFilterPath" => "voucher_no",
        ],
        "Employee Name" => [
            "id" => "employee_name",
            "type" => "text",
            "placeholder" => "Enter employee name",
            "oninput" => "runDynamicFilter()",
            "dataFilterPath" => "name",
        ],
        "City" => [
            "id" => "city",
            "type" => "text",
            "placeholder" => "Enter city",
            "oninput" => "runDynamicFilter()",
            "dataFilterPath" => "data.employee.city.title",
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
        "Date" => [
            "id" => "date",
            "type" => "text",
            "placeholder" => "Enter date",
            "oninput" => "runDynamicFilter()",
            "dataFilterPath" => "details.Date",
        ],
        "Reff. No." => [
            "id" => "reff_no",
            "type" => "text",
            "placeholder" => "Enter reff. no.",
            "oninput" => "runDynamicFilter()",
            "dataFilterPath" => "reff_no",
        ],
        "Issued" => [
            "id" => "issued",
            "type" => "select",
            "options" => [
                        'Issued' => ['text' => 'Issued'],
                        'Return' => ['text' => 'Return'],
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
    ];
@endphp
    <div class="w-[80%] mx-auto">
        <x-search-header heading="Employee Payments" :search_fields=$searchFields/>
    </div>

    <!-- Main Content -->
    <section class="text-center mx-auto ">
        <div
            class="show-box mx-auto w-[80%] h-[70vh] bg-[var(--secondary-bg-color)] border border-[var(--glass-border-color)]/20 rounded-xl shadow pt-8.5 relative">
            <x-form-title-bar title="Show Employee Payments" changeLayoutBtn layout="{{ $authLayout }}" />

            @if (count($payments) > 0)
                <div class="absolute bottom-0 right-0 flex items-center justify-between gap-2 w-fll z-50 p-3 w-full pointer-events-none">
                    <x-section-navigation-button direction="right" id="info" icon="fa-info" />
                    <x-section-navigation-button link="{{ route('employee-payments.create') }}" title="Add New Payment" icon="fa-plus" />
                </div>

                <div class="details h-full z-40">
                    <div class="container-parent h-full">
                        <div class="card_container px-3 pb-3 h-full flex flex-col">
                            <div id="table-head" class="grid grid-cols-5 bg-[var(--h-bg-color)] rounded-lg font-medium py-2 hidden mt-4 mx-2">
                                <div class="text-center">Date</div>
                                <div class="text-center">Category</div>
                                <div class="text-center">Employee</div>
                                <div class="text-center">Method</div>
                                <div class="text-center">Amount</div>
                            </div>
                            <p id="noItemsError" style="display: none" class="text-sm text-[var(--border-error)] mt-3">No items found</p>
                            <div class="overflow-y-auto grow my-scrollbar-2">
                                <div class="search_container grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 grow ">
                                    {{-- class="search_container overflow-y-auto grow my-scrollbar-2"> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="no-records-message w-full h-full flex flex-col items-center justify-center gap-2">
                    <h1 class="text-sm text-[var(--secondary-text)] capitalize">No Payment Found</h1>
                    <a href="{{ route('employee-payments.create') }}"
                        class="text-sm bg-[var(--primary-color)] text-[var(--text-color)] px-4 py-2 rounded-md hover:bg-[var(--h-primary-color)] hover:scale-105 hover:mb-2 transition-all duration-300 ease-in-out font-semibold">Add
                        New</a>
                </div>
            @endif
        </div>
    </section>

    <script>
        let authLayout = '{{ $authLayout }}';

        function createRow(data) {
            return `
                <div id="${data.id}" oncontextmenu='${data.oncontextmenu || ""}' onclick='${data.onclick || ""}'
                    class="item row relative group grid grid-cols-5 border-b border-[var(--h-bg-color)] items-center py-2 cursor-pointer hover:bg-[var(--h-secondary-bg-color)] transition-all fade-in ease-in-out"
                    data-json='${JSON.stringify(data)}'>

                    <span class="text-center">${data.details['Date']}</span>
                    <span class="text-center">${data.details['Category']}</span>
                    <span class="text-center">${data.name}</span>
                    <span class="text-center capitalize">${data.details["Method"]}</span>
                    <span class="text-center">${data.details['Amount']}</span>
                </div>
            `;
        }

        const fetchedData = @json($payments);
        let allDataArray = fetchedData.map(item => {
            return {
                id: item.id,
                name: item.employee.employee_name + ' | ' + item.employee.type.title,
                details: {
                    'Category': item.employee.category,
                    'Method': item.method,
                    'Date': formatDate(item.date),
                    'Amount': formatNumbersWithDigits(item.amount, 1, 1),
                },
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
                actions: [
                    // {id: 'edit-payment', text: 'Edit Payment', dataId: data.id}
                ],
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
                    'Category': data.details['Category'],
                    'Method': data.details['Method'],
                    'Amount': data.details['Amount'],
                },
                bottomActions: [
                    // {id: 'edit-payment', text: 'Edit Payment', dataId: data.id}
                ],
            }

            createModal(modalData);
        }

        let infoDom = document.getElementById('info').querySelector('span');

        function onFilter() {
            infoDom.textContent = `Showing ${newlyFilteredData.filter(d => d.visible).length} of ${allDataArray.length} payments.`;
        }
    </script>
@endsection
