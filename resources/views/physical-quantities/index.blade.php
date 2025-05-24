@extends('app')
@section('title', 'Show Physical Quantities | ' . app('company')->name)
@section('content')
    
    {{-- header --}}
    <div class="w-[80%] mx-auto">
        <x-search-header heading="Physical Quantity" :filter_items="[
            'all' => 'All',
            '#' => 'Article No.',
            'proc_by' => 'Proc. By',
        ]"/>
    </div>

    <!-- Main Content -->
    <section class="text-center mx-auto ">
        <div
            class="show-box mx-auto w-[80%] h-[70vh] bg-[var(--secondary-bg-color)] rounded-xl shadow overflow-y-auto pt-7 pr-2 relative">
            <x-form-title-bar title="Show Physical Quantities" />

            @if (count($physicalQuantities) > 0)
                <div
                    class="add-new-article-btn absolute z-[999] bottom-8 right-5 hover:scale-105 hover:bottom-9 transition-all group duration-300 ease-in-out">
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
                    <div class="container-parent h-full overflow-y-auto my-scrollbar-2">
                        <div class="data_container p-5 pr-3">
                            <div class="table_container overflow-hidden text-sm">
                                <div class="flex items-center bg-[var(--h-bg-color)] rounded-lg font-medium py-2 px-4">
                                    <div class="w-[10%]">Article No.</div>
                                    <div class="w-[7%]">Proc. By</div>
                                    <div class="w-[8%]">Unit</div>
                                    <div class="w-[15%]">Total Qty.</div>
                                    <div class="w-[15%]">Received Qty.</div>
                                    <div class="w-[15%]">Current Stock Qty.</div>
                                    <div class="w-[15%]">A</div>
                                    <div class="w-[15%]">B</div>
                                    <div class="w-[15%]">Remaining Qty.</div>
                                    <div class="w-[8%]">Shipment</div>
                                </div>
                                <div class="search_container overflow-y-auto grow my-scrollbar-2">
                                    @foreach ($physicalQuantities as $physicalQuantity)
                                        @php
                                            $article = $physicalQuantity->article;
                                            $pcsPerPacket = $article->pcs_per_packet;
                                            $totalQuantity = $article->quantity;

                                            $totalPackets = $physicalQuantity->total_packets;

                                            $remainingPcs = $totalQuantity - ($totalPackets * $pcsPerPacket);
                                            $remainingPkts = number_format(($totalQuantity / $pcsPerPacket) - $totalPackets, 1);

                                            $aCategoryPkts = $physicalQuantity->a_category;
                                            $bCategoryPkts = $physicalQuantity->b_category;
                                        @endphp

                                        <div id="{{ $physicalQuantity->id }}" data-json="{{ json_encode($physicalQuantity) }}" class="contextMenuToggle modalToggle relative group flex text-center border-b border-[var(--h-bg-color)] items-center py-2 px-4 cursor-pointer hover:bg-[var(--h-secondary-bg-color)] transition-all fade-in ease-in-out">
                                            <span class="w-[10%]">{{ $article->article_no }}</span>
                                            <span class="capitalize w-[7%]">{{ $article->processed_by }}</span>
                                            <span class="w-[8%]">{{ $pcsPerPacket }} - Pcs.</span>
                                            <span class="w-[15%]">{{ $totalQuantity / 12 }} - Dz. | {{ $totalQuantity / $pcsPerPacket }} - Pcs.</span>
                                            <span class="w-[15%]">{{ number_format(($totalPackets * $pcsPerPacket) / 12, 1) }} - Dz. | {{ $totalPackets }} - Pkts.</span>
                                            <span class="w-[15%]">{{ number_format(($physicalQuantity->current_stock * $pcsPerPacket) / 12, 1) }} - Dz. | {{ $physicalQuantity->current_stock }} - Pkts.</span>
                                            <span class="w-[15%]">{{ number_format(($aCategoryPkts * $pcsPerPacket) / 12, 1) }} - Dz. | {{ $aCategoryPkts }} - Pkts.</span>
                                            <span class="w-[15%]">{{ number_format(($bCategoryPkts * $pcsPerPacket) / 12, 1) }} - Dz. | {{ $bCategoryPkts }} - Pkts.</span>
                                            <span class="w-[15%]">{{ number_format(($remainingPcs / 12), 1) }} - Dz. | {{ $remainingPkts }} - Pkts.</span>
                                            <span class="w-[8%]">{{ $physicalQuantity->shipment ?? '-' }}</span>
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
                            item.article.article_no.toString().includes(search) ||
                            item.article.processed_by.toLowerCase().includes(search)
                        );
                        break;
                        
                    case '#':
                        return (
                            item.article.article_no.toString().includes(search)
                        );
                        break;
                        
                    case 'proc_by':
                        return (
                            item.article.processed_by.toLowerCase().includes(search)
                        );
                        break;

                    default:
                        return (
                            item.article.article_no.toString().includes(search) ||
                            item.article.processed_by.toLowerCase().includes(search)
                        );
                        break;
                }
            });

            return filteredData;
        }
    </script>
@endsection
