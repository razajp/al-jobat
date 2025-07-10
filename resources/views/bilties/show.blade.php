@extends('app')
@section('title', 'Show Bilties | ' . app('company')->name)
@section('content')
    @php
        $searchFields = [
            "Customer Name" => [
                "id" => "customer_name",
                "type" => "text",
                "placeholder" => "Enter customer name",
                "oninput" => "runDynamicFilter()",
                "dataFilterPath" => "invoice.customer.customer_name",
            ],
            "Invoice No" => [
                "id" => "invoice_no",
                "type" => "text",
                "placeholder" => "Enter invoice no",
                "oninput" => "runDynamicFilter()",
                "dataFilterPath" => "invoice.invoice_no",
            ],
            "Cargo Name" => [
                "id" => "cargo_name",
                "type" => "text",
                "placeholder" => "Enter cargo name",
                "oninput" => "runDynamicFilter()",
                "dataFilterPath" => "invoice.cargo_name",
            ],
            "Bilty No" => [
                "id" => "bilty_no",
                "type" => "text",
                "placeholder" => "Enter bilty no",
                "oninput" => "runDynamicFilter()",
                "dataFilterPath" => "bilty_no",
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

    {{-- header --}}
    <div class="w-[80%] mx-auto">
        <x-search-header heading="Bilties" :search_fields=$searchFields/>
    </div>

    <!-- Main Content -->
    <section class="text-center mx-auto ">
        <div 
            class="show-box mx-auto w-[80%] h-[70vh] bg-[var(--secondary-bg-color)] border border-[var(--glass-border-color)]/20 rounded-xl shadow overflow-y-auto pt-8.5 relative">
            <x-form-title-bar title="Show Bilties" />

            @if (count($bilties) > 0)
                <div class="absolute bottom-3 right-3 flex items-center gap-2 w-fll z-50">
                    <x-section-navigation-button link="{{ route('bilties.create') }}" title="Add New Bilty" icon="fa-plus" />
                </div>

                <div class="details h-full z-40">
                    <div class="container-parent h-full overflow-y-auto my-scrollbar-2">
                        <div class="card_container py-0 p-3 h-full flex flex-col">
                            <div id="table-head" class="grid grid-cols-6 bg-[var(--h-bg-color)] rounded-lg font-medium py-2 hidden mt-4 mx-2">
                                <div>Date</div>
                                <div class="col-span-2">Customer Name</div>
                                <div>Invoice No.</div>
                                <div>Cargo Name</div>
                                <div>Bilty No.</div>
                            </div>
                            <p id="noItemsError" style="display: none" class="text-sm text-[var(--border-error)] mt-3">No items found</p>
                            <div class="search_container grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 overflow-y-auto grow my-scrollbar-2">
                                {{-- class="search_container overflow-y-auto grow my-scrollbar-2"> --}}
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="no-record-message w-full h-full flex flex-col items-center justify-center gap-2">
                    <h1 class="text-sm text-[var(--secondary-text)] capitalize">No Record Found</h1>
                    <a href="{{ route('bilties.create') }}"
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
                class="item row relative group grid grid-cols-6 text-center border-b border-[var(--h-bg-color)] items-center py-2 cursor-pointer hover:bg-[var(--h-secondary-bg-color)] transition-all fade-in ease-in-out"
                data-json='${JSON.stringify(data)}'>

                <span>${data.date}</span>
                <span class="col-span-2">${data.customer_name}</span>
                <span>${data.invoice_no}</span>
                <span>${data.cargo_name}</span>
                <span>${data.bilty_no}</span>
            </div>`;
        }

        const fetchedData = @json($bilties);
        console.log(fetchedData);
        let allDataArray = fetchedData.map(item => {
            return {
                id: item.id,
                date: formatDate(item.date),
                customer_name: item.invoice.customer.customer_name + ' | ' + item.invoice.customer.city.title,
                invoice_no: item.invoice.invoice_no,
                cargo_name: item.invoice.cargo_name,
                bilty_no: item.bilty_no + ' | ' + item.invoice.cotton_count,
                visible: true,
            };
        });
    </script>
@endsection
