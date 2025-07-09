@extends('app')
@section('title', 'Show Bank Accounts | ' . app('company')->name)
@section('content')
    @php
        $searchFields = [
            "Account Title" => [
                "id" => "account_title",
                "type" => "text",
                "placeholder" => "Enter account title",
                "oninput" => "runDynamicFilter()",
                "dataFilterPath" => "account_title",
            ],
            "Category" => [
                "id" => "category",
                "type" => "select",
                "options" => [
                            'self' => ['text' => 'Self'],
                            'customer' => ['text' => 'Customer'],
                            'supplier' => ['text' => 'Supplier'],
                        ],
                "onchange" => "runDynamicFilter()",
                "dataFilterPath" => "category",
            ],
            "Name" => [
                "id" => "name",
                "type" => "text",
                "placeholder" => "Enter name",
                "oninput" => "runDynamicFilter()",
                "dataFilterPath" => "name",
            ],
            "Account No" => [
                "id" => "account_no",
                "type" => "text",
                "placeholder" => "Enter account no",
                "oninput" => "runDynamicFilter()",
                "dataFilterPath" => "account_no",
            ],
            "Bank" => [
                "id" => "bank",
                "type" => "text",
                "placeholder" => "Enter bank",
                "oninput" => "runDynamicFilter()",
                "dataFilterPath" => "bank",
            ],
            'Status' => [
                'id' => 'status',
                'type' => 'select',
                'options' => [
                    'active' => ['text' => 'Active'],
                    'in_active' => ['text' => 'In Active'],
                ],
                'dataFilterPath' => 'status',
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
    <div>
        <div class="w-[80%] mx-auto">
            <x-search-header heading="Bank Accounts" :search_fields=$searchFields/>
        </div>

        <!-- Main Content -->
        <section class="text-center mx-auto">
            <div
                class="show-box mx-auto w-[80%] h-[70vh] bg-[var(--secondary-bg-color)] rounded-xl shadow overflow-y-auto pt-8.5 relative">
                <x-form-title-bar title="Show Bank Accounts" changeLayoutBtn layout="{{ $authLayout }}" />

                @if (count($bankAccounts) > 0)
                    <div class="absolute bottom-0 right-0 flex items-center justify-between gap-2 w-fll z-50 p-3 w-full pointer-events-none">
                        <x-section-navigation-button direction="right" id="info" icon="fa-info" />
                        <x-section-navigation-button link="{{ route('bank-accounts.create') }}" title="Add New Account" icon="fa-plus" />
                    </div>

                    <div class="details h-full z-40">
                        <div class="container-parent h-full overflow-y-auto my-scrollbar-2">
                            <div class="card_container py-0 p-3 h-full flex flex-col">
                                <div id="table-head" class="grid grid-cols-6 bg-[var(--h-bg-color)] rounded-lg font-medium py-2 hidden mt-4 mx-2">
                                    <div class="text-left pl-5">Date</div>
                                    <div class="text-center">Account Title</div>
                                    <div class="text-center">Name</div>
                                    <div class="text-center">Category</div>
                                    <div class="text-right">Balance</div>
                                    <div class="text-right pr-5">Status</div>
                                </div>
                                <p id="noItemsError" style="display: none" class="text-sm text-[var(--border-error)] mt-3">No items found</p>
                                <div>
                                    <div class="search_container grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 overflow-y-auto grow my-scrollbar-2">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="no-article-message w-full h-full flex flex-col items-center justify-center gap-2">
                        <h1 class="text-md text-[var(--secondary-text)] capitalize">No Bank Account yet</h1>
                        <a href="{{ route('bank-accounts.create') }}"
                        class="text-sm bg-[var(--primary-color)] text-[var(--text-color)] px-4 py-2 rounded-md hover:bg-[var(--h-primary-color)] hover:scale-105 hover:mb-2 transition-all duration-300 ease-in-out font-semibold">Add
                            New</a>
                    </div>
                @endif
            </div>
        </section>
    </div>

    <script>
        let currentUserRole = '{{ Auth::user()->role }}';
        let authLayout = '{{ $authLayout }}';

        
        function createRow(data) {
            return `
            <div id="${data.id}" oncontextmenu='${data.oncontextmenu || ""}' onclick='${data.onclick || ""}'
                class="item row relative group grid text- grid-cols-6 border-b border-[var(--h-bg-color)] items-center py-2 cursor-pointer hover:bg-[var(--h-secondary-bg-color)] transition-all fade-in ease-in-out"
                data-json='${JSON.stringify(data)}'>

                <span class="text-left pl-5">${formatDate(data.date)}</span>
                <span class="text-left pl-5">${data.name}</span>
                <span class="text-center capitalize">${data.details["Name"]}</span>
                <span class="text-center capitalize">${data.details["Category"]}</span>
                <span class="text-right">${Number(data.details["Balance"]).toFixed(1)}</span>
                <span class="text-right pr-5 capitalize">${data.status}</span>
            </div>`;
        }

        const fetchedData = @json($bankAccounts);
        let allDataArray = fetchedData.map(item => {
            console.log(item);
            
            return {
                id: item.id,
                uId: item.id,
                status: item.status,
                name: item.account_title,
                details: {
                    'Name': item.sub_category?.customer_name ?? item.sub_category?.supplier_name ?? item.account_title,
                    'Category': item.category,
                    'Balance': item.balance ?? 0,
                },
                accountNo: item.account_no ?? 0,
                bank: item.bank.title,
                date: item.date,
                chqbkSerialStart: item.chqbk_serial_start ?? 0,
                chqbkSerialEnd: item.chqbk_serial_end ?? 0,
                oncontextmenu: "generateContextMenu(event)",
                onclick: "generateModal(this)",
                visible: true,
            };
        });

        const activeAccounts = allDataArray.filter(account => account.status === 'active');

        let infoDom = document.getElementById('info').querySelector('span');
        infoDom.textContent = `Total Bank Account: ${allDataArray.length} | Active: ${activeAccounts.length}`;

        function generateContextMenu(e) {
            let item = e.target.closest('.item');
            let data = JSON.parse(item.dataset.json);

            let contextMenuData = {
                item: item,
                data: data,
                action: "{{ route('update-bank-account-status') }}",
                x: e.pageX,
                y: e.pageY,
            };

            createContextMenu(contextMenuData);
        }

        function generateModal(item) {
            let data = JSON.parse(item.dataset.json);
            
            let modalData = {
                id: 'modalForm',
                uId: data.id,
                status: data.status,
                name: data.name,
                action: "{{ route('update-bank-account-status') }}",
                details: {
                    'Name': data.details['Name'],
                    'Category': data.details['Category'],
                    'Bank': data.bank,
                    'Date': formatDate(data.date),
                    'Balance': formatNumbersWithDigits(data.details['Balance'], 1, 1),
                },
            }

            if (data.details['Category'] === 'self') {
                modalData.details['Account No'] = data.accountNo;
                modalData.details['Cheque Book Serial'] = data.chqbkSerialStart + ' - ' + data.chqbkSerialEnd;
            }

            createModal(modalData);
        }
    </script>
@endsection
