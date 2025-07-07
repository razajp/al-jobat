@extends('app')
@section('title', 'Show Payment Programs | ' . app('company')->name)
@section('content')
    @php
        $categories_options = [
            'self_account' => ['text' => 'Self Account'],
            'supplier' => ['text' => 'Supplier'],
            'customer' => ['text' => 'Customer'],
            'waiting' => ['text' => 'Waiting'],
        ];

        $searchFields = [
            'Customer Name' => [
                'id' => 'customer_name',
                'type' => 'text',
                'placeholder' => 'Enter customer name',
                'oninput' => 'runDynamicFilter()',
                'dataFilterPath' => 'customer.customer_name',
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
    <div class="w-[80%] mx-auto">
        <x-search-header heading="Payment Programs" :search_fields=$searchFields />
    </div>

    <!-- Main Content -->
    <section class="text-center mx-auto">
        <div
            class="show-box mx-auto w-[80%] h-[70vh] bg-[var(--secondary-bg-color)] rounded-xl shadow overflow-y-auto pt-8.5 relative">
            <x-form-title-bar title="Show Payment Programs" />

            @if (count($finalData) > 0)
                <div class="absolute bottom-3 right-3 flex items-center gap-2 w-fll z-50">
                    <x-section-navigation-button link="{{ route('payment-programs.create') }}" title="Add New Program"
                        icon="fa-plus" />
                </div>

                <div class="details h-full z-40">
                    <div class="container-parent h-full overflow-y-auto my-scrollbar-2">
                        <div class="card_container py-0 p-3 h-full flex flex-col">
                            <div id="table-head" class="flex items-center bg-[var(--h-bg-color)] rounded-lg font-medium py-2 hidden mt-4 mx-2">
                                <div class="w-[10%]">Date</div>
                                <div class="w-[15%]">Customer</div>
                                <div class="w-[10%]">O/P No.</div>
                                <div class="w-[10%]">Category</div>
                                <div class="w-[10%]">Beneficiary</div>
                                <div class="w-[10%]">Amount</div>
                                <div class="w-[10%]">Document</div>
                                <div class="w-[10%]">Payment</div>
                                <div class="w-[10%]">Balance</div>
                                <div class="w-[10%]">Status</div>
                            </div>
                            <p id="noItemsError" style="display: none" class="text-sm text-[var(--border-error)] mt-3">No items found</p>
                            <div class="search_container grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 overflow-y-auto grow my-scrollbar-2">
                                {{-- class="search_container overflow-y-auto grow my-scrollbar-2"> --}}
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="no-article-message w-full h-full flex flex-col items-center justify-center gap-2">
                    <h1 class="text-md text-[var(--secondary-text)] capitalize">No Payment Programs yet</h1>
                    <a href="{{ route('payment-programs.create') }}"
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
                class="item row relative group flex items-center border-b border-[var(--h-bg-color)] items-center py-2 cursor-pointer hover:bg-[var(--h-secondary-bg-color)] transition-all fade-in ease-in-out"
                data-json='${JSON.stringify(data)}'>

                <span class="w-[10%]">${data.name}</span>
                <span class="w-[15%]">${data.customer_name}</span>
                <span class="w-[10%]">${data.o_p_no}</span>
                <span class="w-[10%]">${data.category}</span>
                <span class="w-[10%]">${data.beneficiary}</span>
                <span class="w-[10%]">${formatNumbersWithDigits(data.amount, 1, 1)}</span>
                <span class="w-[10%]">${data.discount}</span>
                <span class="w-[10%]">${formatNumbersWithDigits(data.payment, 1, 1)}</span>
                <span class="w-[10%]">${formatNumbersWithDigits(data.balance, 1, 1)}</span>
                <span class="w-[10%]">${data.status}</span>
            </div>`;
        }


        
        const fetchedData = @json($finalData);
        console.log(fetchedData);
        let allDataArray = fetchedData.map(item => {
            const category = item.category || item.payment_programs?.category;
            let beneficiary = null;

            if (item?.category) {
                if (item.category === 'supplier' && item.sub_category?.supplier_name) {
                    beneficiary = item.sub_category.supplier_name;
                } else if (item.category === 'customer' && item.sub_category?.customer_name) {
                    beneficiary = item.sub_category.customer_name;
                } else if (item.category === 'waiting' && item.remarks) {
                    beneficiary = item.remarks;
                } else if (item.category === 'self_account' && item.sub_category?.account_title) {
                    beneficiary = item.sub_category.account_title;
                }
            } else if (item?.payment_programs?.category) {
                const p = item.payment_programs;
                const sub = p.sub_category;
                if (p.category === 'supplier' && sub?.supplier_name) {
                    beneficiary = sub.supplier_name;
                } else if (p.category === 'customer' && sub?.customer_name) {
                    beneficiary = sub.customer_name;
                } else if (p.category === 'waiting' && p.remarks) {
                    beneficiary = p.remarks;
                } else if (p.category === 'self_account' && sub?.account_title) {
                    beneficiary = sub.account_title;
                }
            }

            return {
                id: item.id,
                name: item.date,
                customer_name: item.customer?.customer_name || '',
                o_p_no: item.order_no || item.program_no,
                category: category,
                beneficiary: beneficiary || '-',
                amount: item.amount || item.netAmount,
                discount: item.document || 0,
                payment: item.payment || 0,
                balance: item.balance || 0,
                status: item.status || '-',
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
                name: 'Details',
            }

            createModal(modalData);
        }
    </script>
@endsection
