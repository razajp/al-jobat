@extends('app')
@section('title', 'Show Daily Ledger | ' . app('company')->name)
@section('content')
@php
    $searchFields = [
        "Description" => [
            "id" => "description",
            "type" => "text",
            "placeholder" => "Enter description",
            "oninput" => "runDynamicFilter()",
            "dataFilterPath" => "description",
        ],
        "Type" => [
            "id" => "type",
            "type" => "select",
            "options" => [
                        'deposit' => ['text' => 'Deposit'],
                        'use' => ['text' => 'Use'],
                    ],
            "onchange" => "runDynamicFilter()",
            "dataFilterPath" => "type",
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
        <x-search-header heading="Daily Ledger" :search_fields=$searchFields/>
    </div>

    <!-- Main Content -->
    <section class="text-center mx-auto ">
        <div
            class="show-box mx-auto w-[80%] h-[70vh] bg-[var(--secondary-bg-color)] border border-[var(--glass-border-color)]/20 rounded-xl shadow pt-8.5 relative">
            <x-form-title-bar printBtn layout="table" title="Show Daily Ledger" resetSortBtn />

            @if (count($dailyLedgers) > 0)
                <div class="absolute bottom-14 right-0 flex items-center justify-between gap-2 w-fll z-50 p-3 w-full pointer-events-none">
                    <x-section-navigation-button direction="right" id="info" icon="fa-info" />
                    <x-section-navigation-button link="{{ route('daily-ledger.create') }}" title="New Deposit | use" icon="fa-plus" />
                </div>

                <div class="details h-full z-40">
                    <div class="container-parent h-full">
                        <div class="card_container px-3 pb-3 h-full flex flex-col">
                            <div id="table-head" class="grid grid-cols-5 text-center bg-[var(--h-bg-color)] rounded-lg font-medium py-2 hidden mt-4 mx-2">
                                <div class="cursor-pointer" onclick="sortByThis(this)">Date</div>
                                <div class="cursor-pointer" onclick="sortByThis(this)">Description</div>
                                <div class="cursor-pointer" onclick="sortByThis(this)">Deposit</div>
                                <div class="cursor-pointer" onclick="sortByThis(this)">Use</div>
                                <div class="cursor-pointer" onclick="sortByThis(this)">Balance</div>
                            </div>
                            <p id="noItemsError" style="display: none" class="text-sm text-[var(--border-error)] mt-3">No items found</p>
                            <div class="overflow-y-auto grow my-scrollbar-2">
                                <div class="search_container grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 grow">
                                </div>
                            </div>
                            <div id="calc-bottom" class="flex w-full gap-4 text-sm bg-[var(--secondary-bg-color)] py-2 rounded-lg">
                                <div
                                    class="total-Deposit flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full cursor-not-allowed">
                                    <div>Total Deposit - Rs.</div>
                                    <div class="text-right">0.00</div>
                                </div>
                                <div
                                    class="total-Payment flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full cursor-not-allowed">
                                    <div>Total Use - Rs.</div>
                                    <div class="text-right">0.00</div>
                                </div>
                                <div
                                    class="balance flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full cursor-not-allowed">
                                    <div>Total Balance - Rs.</div>
                                    <div class="text-right">0.00</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="no-records-message w-full h-full flex flex-col items-center justify-center gap-2">
                    <h1 class="text-sm text-[var(--secondary-text)] capitalize">No Records Found</h1>
                    <a href="{{ route('daily-ledger.create') }}"
                        class="text-sm bg-[var(--primary-color)] text-[var(--text-color)] px-4 py-2 rounded-md hover:bg-[var(--h-primary-color)] hover:scale-105 hover:mb-2 transition-all duration-300 ease-in-out font-semibold">Add
                        New</a>
                </div>
            @endif
        </div>
    </section>

    <script>
        let totalDepositAmount = 0;
        let totalUseAmount = 0;
        let authLayout = 'table';
        let balance = 0;

        function createRow(data) {
            return `
                <div id="${data.id}" oncontextmenu='${data.oncontextmenu || ""}' onclick='${data.onclick || ""}'
                    class="item row relative group grid grid-cols-5 text-center border-b border-[var(--h-bg-color)] items-center py-2 cursor-pointer hover:bg-[var(--h-secondary-bg-color)] transition-all fade-in ease-in-out"
                    data-json='${JSON.stringify(data)}'>

                    <span>${formatDate(data.date)}</span>
                    <span>${data.description}</span>
                    <span>${formatNumbersWithDigits(data.deposit, 1, 1)}</span>
                    <span>${formatNumbersWithDigits(data.use, 1, 1)}</span>
                    <span>${formatNumbersWithDigits(data.balance, 1, 1)}</span>
                </div>
            `;
        }

        const fetchedData = @json($dailyLedgers);
        let allDataArray = fetchedData.map(item => {
            balance += item.deposit;
            balance -= item.use;
            totalDepositAmount += parseFloat(item.deposit ?? 0);
            totalUseAmount += parseFloat(item.use ?? 0);
            return {
                id: item.id,
                date: item.date,
                description: item.description ?? '-',
                deposit: item.deposit,
                use: item.use,
                type: item.deposit > 0 ? 'deposit' : 'use',
                balance: balance,
                visible: true,
            };
        });

        let totalDepositDom = document.querySelector('#calc-bottom >.total-Deposit .text-right');
        let totalUseDom = document.querySelector('#calc-bottom >.total-Payment .text-right');
        let balanceDom = document.querySelector('#calc-bottom >.balance .text-right');
        let infoDom = document.getElementById('info').querySelector('span');

        function onFilter() {
            totalDepositAmount = visibleData.reduce((sum, d) => sum + d.deposit, 0);
            totalUseAmount = visibleData.reduce((sum, d) => sum + d.use, 0);
            infoDom.textContent = `Showing ${visibleData.length} of ${allDataArray.length} payments.`;

            totalDepositDom.innerText = formatNumbersWithDigits(totalDepositAmount, 1, 1);
            totalUseDom.innerText = formatNumbersWithDigits(totalUseAmount, 1, 1);
            balanceDom.innerText = formatNumbersWithDigits(totalDepositAmount - totalUseAmount, 1, 1);
        }
    </script>
@endsection
