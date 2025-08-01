@extends('app')
@section('title', 'Show Expenses | ' . app('company')->name)
@section('content')
    @php
        $searchFields = [
            "Supplier Name" => [
                "id" => "supplier_name",
                "type" => "text",
                "placeholder" => "Enter supplier name",
                "oninput" => "runDynamicFilter()",
                "dataFilterPath" => "supplier.supplier_name",
            ],
            "Reff. No" => [
                "id" => "reff_no",
                "type" => "text",
                "placeholder" => "Enter reff. no",
                "oninput" => "runDynamicFilter()",
                "dataFilterPath" => "reff_no",
            ],
            "Expense" => [
                "id" => "expense",
                "type" => "select",
                "options" => $expenseOptions,
                "onchange" => "runDynamicFilter()",
                "dataFilterPath" => "expense_setups.title",
            ],
            "Remarks" => [
                "id" => "remarks",
                "type" => "text",
                "placeholder" => "Enter remarks",
                "oninput" => "runDynamicFilter()",
                "dataFilterPath" => "remarks",
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
        <x-search-header heading="Expenses" :search_fields=$searchFields/>
    </div>
    
    <!-- Main Content -->
    <section class="text-center mx-auto ">
        <div
            class="show-box mx-auto w-[80%] h-[70vh] bg-[var(--secondary-bg-color)] border border-[var(--glass-border-color)]/20 rounded-xl shadow overflow-y-auto pt-8.5 relative">
            <x-form-title-bar title="Show Expenses"/>

            @if (count($expenses) > 0)
                <div class="absolute bottom-3 right-3 flex items-center gap-2 w-fll z-50">
                    <x-section-navigation-button link="{{ route('expenses.create') }}" title="Add New Expense" icon="fa-plus" />
                </div>

                <div class="details h-full z-40">
                    <div class="container-parent h-full overflow-y-auto my-scrollbar-2">
                        <div class="card_container px-3 h-full flex flex-col">
                            <div id="table-head" class="grid grid-cols-8 bg-[var(--h-bg-color)] rounded-lg font-medium py-2 hidden mt-4 mx-2">
                                <div>Date</div>
                                <div class="col-span-2">Supplier Name</div>
                                <div>Reff. No.</div>
                                <div>Expense</div>
                                <div>Lot No.</div>
                                <div>Amount</div>
                                <div>Remarks</div>
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
                    <h1 class="text-sm text-[var(--secondary-text)] capitalize">No Expense Found</h1>
                    <a href="{{ route('expenses.create') }}"
                        class="text-sm bg-[var(--primary-color)] text-[var(--text-color)] px-4 py-2 rounded-md hover:bg-[var(--h-primary-color)] hover:scale-105 hover:mb-2 transition-all duration-300 ease-in-out font-semibold">Add
                        New</a>
                </div>
            @endif
        </div>
    </section>

    <script>
        let authLayout = 'table';

        function createRow(data) {
            return `
            <div id="${data.id}" oncontextmenu='${data.oncontextmenu || ""}' onclick='${data.onclick || ""}'
                class="item row relative group grid grid-cols-8 text-center border-b border-[var(--h-bg-color)] items-center py-2 cursor-pointer hover:bg-[var(--h-secondary-bg-color)] transition-all fade-in ease-in-out"
                data-json='${JSON.stringify(data)}'>

                <span>${data.date}</span>
                <span class="col-span-2">${data.supplier_name}</span>
                <span>${data.reff_no}</span>
                <span>${data.expense}</span>
                <span>${data.lot_no}</span>
                <span>${data.amount}</span>
                <span class="capitalize">${data.remarks}</span>
            </div>`;
        }

        const fetchedData = @json($expenses);
        console.log(fetchedData);
        let allDataArray = fetchedData.map(item => {
            return {
                id: item.id,
                date: formatDate(item.date),
                supplier_name: item.supplier.supplier_name,
                reff_no: item.reff_no || '-',
                expense: item.expense_setups.title,
                lot_no: item.lot_no || '-',
                amount: formatNumbersWithDigits(item.amount, 1, 1),
                remarks: item.remarks || 'No Remarks',
                visible: true,
            };
        });
    </script>
@endsection
