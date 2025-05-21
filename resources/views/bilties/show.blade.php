@extends('app')
@section('title', 'Show Bilties | ' . app('company')->name)
@section('content')
    
    {{-- header --}}
    <div class="w-[80%] mx-auto">
        <x-search-header heading="Bilties" :filter_items="[
            'all' => 'All',
            'bilty_no' => 'Bilty No.',
            'date' => 'Date',
            'invoice_no' => 'Invoice No.',
        ]"/>
    </div>

    <!-- Main Content -->
    <section class="text-center mx-auto ">
        <div class="show-box mx-auto w-[80%] h-[70vh] bg-[var(--secondary-bg-color)] rounded-xl shadow overflow-y-auto pt-7 pr-2 relative">
            <x-form-title-bar title="Show Bilties" />

            @if (count($bilties) > 0)
                <div
                    class="add-new-article-btn absolute z-[999] bottom-8 right-5 hover:scale-105 hover:bottom-9 transition-all group duration-300 ease-in-out">
                    <a href="{{ route('bilties.create') }}"
                        class="bg-[var(--primary-color)] text-[var(--text-color)] px-3 py-2 rounded-full hover:bg-[var(--h-primary-color)] transition-all duration-300 ease-in-out"><i
                            class="fas fa-plus"></i></a>
                    <span
                        class="absolute shadow-xl right-7 top-0 border border-gray-600 transform -translate-x-1/2 bg-[var(--secondary-bg-color)] text-[var(--text-color)] text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
                        Add
                    </span>
                </div>
            @endif

            @if (count($bilties) > 0)
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
                                            <span class="capitalize">{{ $bilty->invoice->customer->customer_name }} | {{ $bilty->invoice->customer->city }}</span>
                                            <span>{{ $bilty->invoice->invoice_no }}</span>
                                            <span>{{ $bilty->invoice->cargo_name }}</span>
                                            <span>{{ $bilty->bilty_no }} | {{ $bilty->invoice->cotton_count ?? "-" }}</span>
                                        </div>
                                    @endforeach
                                </div>
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
