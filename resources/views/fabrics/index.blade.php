@extends('app')
@section('title', 'Show Fabrics | ' . app('company')->name)
@section('content')
    @php
        $categories_options = [
            'self_account' => ['text' => 'Self Account'],
            'supplier' => ['text' => 'Supplier'],
            'customer' => ['text' => 'Customer'],
            'waiting' => ['text' => 'Waiting'],
        ];

        $searchFields = [
            'Supplier Name' => [
                'id' => 'supplier_name',
                'type' => 'text',
                'placeholder' => 'Enter supplier name',
                'oninput' => 'runDynamicFilter()',
                'dataFilterPath' => 'supplier.supplier_name',
            ],
            'Worker Name' => [
                'id' => 'worker_name',
                'type' => 'text',
                'placeholder' => 'Enter worker name',
                'oninput' => 'runDynamicFilter()',
                'dataFilterPath' => 'worker.worker_name',
            ],
            'Category' => [
                'id' => 'category',
                'type' => 'select',
                'options' => [
                    'supplier' => ['text' => 'Supplier'],
                    'self_account' => ['text' => 'Self Account'],
                    'customer' => ['text' => 'Customer'],
                    'waiting' => ['text' => 'Waiting'],
                ],
                'onchange' => 'runDynamicFilter()',
                'dataFilterPath' => 'Category',
            ],
            'Status' => [
                'id' => 'status',
                'type' => 'select',
                'options' => [
                    'paid' => ['text' => 'Paid'],
                    'unpaid' => ['text' => 'Unpaid'],
                    'overpaid' => ['text' => 'Overpaid'],
                ],
                'onchange' => 'runDynamicFilter()',
                'dataFilterPath' => 'status',
            ],
            'Date Range' => [
                'id' => 'date_range_start',
                'type' => 'date',
                'id2' => 'date_range_end',
                'type2' => 'date',
                'oninput' => 'runDynamicFilter()',
                'dataFilterPath' => 'date',
            ],
        ];
    @endphp
    <div>
        <div class="w-[80%] mx-auto">
            <x-search-header heading="Fabrics" :search_fields=$searchFields />
        </div>

        <!-- Main Content -->
        <section class="text-center mx-auto">
            <div
                class="show-box mx-auto w-[80%] h-[70vh] bg-[var(--secondary-bg-color)] rounded-xl shadow overflow-y-auto pt-8.5 pr-2 relative">
                <x-form-title-bar title="Show Fabrics" />

                @if (count($finalData) > 0)
                    <div class="absolute bottom-3 right-3 flex items-center gap-2 w-fll z-50">
                        <x-section-navigation-button link="{{ route('fabrics.create') }}" title="Add New Fabric"
                            icon="fa-plus" />
                    </div>
                
                    <div class="details h-full z-40">
                        <div class="container-parent h-full overflow-y-auto my-scrollbar-2">
                            <div class="data_container pt-4 p-5 pr-3 h-full flex flex-col">
                                <div class="flex items-center bg-[var(--h-bg-color)] rounded-lg font-medium py-2">
                                    <div class="text-center w-[10%]">Date</div>
                                    <div class="text-center w-[15%]">Supplier / Worker</div>
                                    <div class="text-center w-[10%]">Fabric</div>
                                    <div class="text-center w-[10%]">Remarks</div>
                                    <div class="text-center w-[10%]">Color</div>
                                    <div class="text-center w-[10%]">Unit</div>
                                    <div class="text-center w-[10%]">Quantity</div>
                                    <div class="text-center w-[20%]">Tag</div>
                                    <div class="text-center w-[10%]">Type</div>
                                </div>

                                <div class="search_container overflow-y-auto grow my-scrollbar-2">
                                    @foreach ($finalData as $data)
                                        <div id="{{ $data['id'] }}" data-json="{{ json_encode($data) }}"
                                            class="relative group flex border-b border-[var(--h-bg-color)] items-center py-2 cursor-pointer hover:bg-[var(--h-secondary-bg-color)] transition-all fade-in ease-in-out">
                                            <span class="text-center w-[10%]">{{ date('d-M-Y, D', strtotime($data['date'])) }}</span>
                                            <span class="text-center w-[15%] capitalize">{{ $data['supplier_name'] ?? $data['employee_name'] }}</span>
                                            <span class="text-center w-[10%] capitalize">{{ $data['fabric'] ?? '-' }}</span>
                                            <span class="text-center w-[10%] capitalize">{{ $data['remarks'] ??'-' }}</span>
                                            <span class="text-center w-[10%] capitalize">{{ $data['color'] ?? '-' }}</span>
                                            <span class="text-center w-[10%] capitalize">{{ $data['unit'] ?? '-' }}</span>
                                            <span class="text-center w-[10%]">{{ number_format($data['quantity'] ?? '0', 1) }}</span>
                                            <span class="text-center w-[20%]">{{ $data['tag'] ?? '-' }}</span>
                                            <span class="text-center w-[10%]">{{ $data['type'] ?? '-' }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            <p id="noItemsError" style="display: none" class="text-sm text-[var(--border-error)] mt-3">No items found</p>
                        </div>
                    </div>
                </div>
                @else
                    <div class="no-article-message w-full h-full flex flex-col items-center justify-center gap-2">
                        <h1 class="text-md text-[var(--secondary-text)] capitalize">No Fabrics yet</h1>
                        <a href="{{ route('fabrics.create') }}"
                            class="text-sm bg-[var(--primary-color)] text-[var(--text-color)] px-4 py-2 rounded-md hover:bg-[var(--h-primary-color)] hover:scale-105 hover:mb-2 transition-all duration-300 ease-in-out font-semibold">Add
                            New</a>
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection
