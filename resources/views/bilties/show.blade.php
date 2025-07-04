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
        <div class="show-box mx-auto w-[80%] h-[70vh] bg-[var(--secondary-bg-color)] rounded-xl shadow overflow-y-auto pt-7 pr-2 relative">
            <x-form-title-bar title="Show Bilties" />

            @if (count($bilties) > 0)
                <div class="absolute bottom-3 right-3 flex items-center gap-2 w-fll z-50">
                    <x-section-navigation-button link="{{ route('bilties.create') }}" title="Add New Bilty" icon="fa-plus" />
                </div>
                
                <div class="details h-full">
                    <div class="container-parent h-full overflow-y-auto my-scrollbar-2">
                        <div class="data_container p-5 pr-3">
                            <div class="table_container overflow-hidden text-sm">
                                <div class="grid grid-cols-5 bg-[var(--h-bg-color)] rounded-lg font-medium py-2">
                                    <div>Date</div>
                                    <div>Customer</div>
                                    <div>Invoice No.</div>
                                    <div>Cargo Name</div>
                                    <div>Bilty No.</div>
                                </div>
                                <div class="search_container overflow-y-auto grow my-scrollbar-2">
                                    @forEach ($bilties as $bilty)
                                        <div id="{{ $bilty->id }}" data-json="{{ $bilty }}"
                                            class="contextMenuToggle modalToggle relative group grid grid-cols-5 text-center border-b border-[var(--h-bg-color)] items-center py-2 cursor-pointer hover:bg-[var(--h-secondary-bg-color)] transition-all fade-in ease-in-out">
                                            <span>{{ $bilty->date->format('d-M-Y. D') }}</span>
                                            <span class="capitalize">{{ $bilty->invoice->customer->customer_name }} | {{ $bilty->invoice->customer->city->title }}</span>
                                            <span>{{ $bilty->invoice->invoice_no }}</span>
                                            <span>{{ $bilty->invoice->cargo_name }}</span>
                                            <span>{{ $bilty->bilty_no }} | {{ $bilty->invoice->cotton_count ?? "-" }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <p id="noItemsError" style="display: none" class="text-sm text-[var(--border-error)] mt-3">No items found</p>
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
        // Function for Search
        function filterData(search) {
            const filteredData = cardsDataArray.filter(item => {
                switch (filterType) {
                    case 'all':
                        return (
                            item.bilty_no.toString().includes(search) ||
                            item.invoice.invoice_no.toString().includes(search) ||
                            item.date.toLowerCase().includes(search)
                        );
                        break;
                        
                    case 'bilty_no':
                        return (
                            item.bilty_no.toString().includes(search)
                        );
                        break;
                        
                    case 'date':
                        return (
                            item.date.toLowerCase().includes(search)
                        );
                        break;
                        
                    case 'invoice_no':
                        return (
                            item.invoice.invoice_no.toString().includes(search)
                        );
                        break;

                    default:
                        return (
                            item.bilty_no.toString().includes(search) ||
                            item.invoice.invoice_no.toString().includes(search) ||
                            item.date.toLowerCase().includes(search)
                        );
                        break;
                }
            });

            return filteredData;
        }
    </script>
@endsection
