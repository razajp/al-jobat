@extends('app')
@section('title', 'Show Articles | ' . app('company')->name)
@section('content')
    
    {{-- header --}}
    <div class="w-[80%] mx-auto">
        <x-search-header heading="Physical Quantity" :filter_items="[
            'all' => 'All',
            '#' => 'Article No.',
            'date' => 'Date',
        ]"/>
    </div>

    <!-- Main Content -->
    <section class="text-center mx-auto ">
        <div
            class="show-box mx-auto w-[80%] h-[70vh] bg-[var(--secondary-bg-color)] rounded-xl shadow overflow-y-auto pt-7 pr-2 relative">
            <div
                class="form-title text-center absolute top-0 left-0 w-full bg-[var(--primary-color)] py-1 shadow-lg uppercase font-semibold text-sm">
                <h4>Show physical Quantities</h4>
            </div>

            @if (count($physicalQuantities) > 0)
                <div
                    class="add-new-article-btn absolute bottom-8 right-5 hover:scale-105 hover:bottom-9 transition-all group duration-300 ease-in-out">
                    <a href="{{ route('physical-quantities.create') }}"
                        class="bg-[var(--primary-color)] text-[var(--text-color)] px-3 py-2 rounded-full hover:bg-[var(--h-primary-color)] transition-all duration-300 ease-in-out"><i
                            class="fas fa-plus"></i></a>
                    <span
                        class="absolute shadow-xl right-7 top-0 border border-gray-600 transform -translate-x-1/2 bg-[var(--secondary-bg-color)] text-[var(--text-color)] text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
                        Add
                    </span>
                </div>
            @endif

            @if (count($physicalQuantities) > 0)
                <div class="details h-full">
                    <div class="container-parent h-full overflow-y-auto my-scrollbar">
                        <div class="data_container p-5 pr-3">
                            <div class="table_container overflow-hidden text-sm">
                                <div class="grid grid-cols-4 bg-[var(--h-bg-color)] rounded-lg font-medium py-2">
                                    <div>Article No.</div>
                                    <div>Date</div>
                                    <div>Pc/Pkt</div>
                                    <div>Packets</div>
                                </div>
                                <div class="search_container overflow-y-auto grow my-scrollbar-2">
                                    @forEach ($physicalQuantities as $physicalQuantity)
                                        <div id="{{ $physicalQuantity->id }}" data-json="{{ $physicalQuantity }}"
                                            class="contextMenuToggle modalToggle relative group grid grid-cols-4 text-center border-b border-[var(--h-bg-color)] items-center py-2 cursor-pointer hover:bg-[var(--h-secondary-bg-color)] transition-all fade-in ease-in-out"
                                            onclick="toggleDetails(this)">
                                            <span>#{{ $physicalQuantity->article->article_no }}</span>
                                            <span>{{ $physicalQuantity->date }}</span>
                                            <span>{{ $physicalQuantity->article->pcs_per_packet }}</span>
                                            <span>{{ $physicalQuantity->packets }}</span>
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
                    <a href="{{ route('physical-quantities.create') }}"
                        class="text-sm bg-[var(--primary-color)] text-[var(--text-color)] px-4 py-2 rounded-md hover:bg-[var(--h-primary-color)] hover:scale-105 hover:mb-2 transition-all 0.3s ease-in-out font-semibold">Add
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
                            item.article.article_no.toString().includes(search) ||
                            item.date.toLowerCase().includes(search)
                        );
                        break;
                        
                    case '#':
                        return (
                            item.article.article_no.toString().includes(search)
                        );
                        break;
                        
                    case 'date':
                        return (
                            item.date.toLowerCase().includes(search)
                        );
                        break;

                    default:
                        return (
                            item.article.article_no.toString().includes(search) ||
                            item.date.toLowerCase().includes(search)
                        );
                        break;
                }
            });

            return filteredData;
        }
    </script>
@endsection
