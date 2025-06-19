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
    <!-- Modal -->
    <div id="modal"
        class="hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-[var(--overlay-color)] fade-in">
    </div>
    <!-- updateModal -->
    <div id="updateModal"
        class="hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-[var(--overlay-color)] fade-in">
    </div>
    <div>
        <div class="w-[80%] mx-auto">
            <x-search-header heading="Payment Programs" :search_fields=$searchFields />
        </div>

        {{-- <div class="w-[80%] mx-auto">
            <x-search-header heading="Payment Programs" :filter_items="[
                'all' => 'All',
                'title' => 'Title',
                'category' => 'Category',
                'name' => 'Name',
            ]" />
        </div> --}}

        <!-- Main Content -->
        <section class="text-center mx-auto">
            <div
                class="show-box mx-auto w-[80%] h-[70vh] bg-[var(--secondary-bg-color)] rounded-xl shadow overflow-y-auto pt-8.5 pr-2 relative">
                <x-form-title-bar title="Show Payment Programs" />

                @if (count($finalData) > 0)
                    <div class="absolute bottom-3 right-3 flex items-center gap-2 w-fll z-50">
                        <x-section-navigation-button link="{{ route('payment-programs.create') }}" title="Add New Program"
                            icon="fa-plus" />
                    </div>
                
                    <div class="details h-full z-40">
                        <div class="container-parent h-full overflow-y-auto my-scrollbar-2">
                            <div class="data_container pt-4 p-5 pr-3 h-full flex flex-col">
                                <div class="flex items-center bg-[var(--h-bg-color)] rounded-lg font-medium py-2">
                                    <div class="text-center w-[10%]">Date</div>
                                    <div class="text-center w-[15%]">Customer</div>
                                    <div class="text-center w-[10%]">O/P No.</div>
                                    <div class="text-center w-[10%]">Category</div>
                                    <div class="text-center w-[10%]">Beneficiary</div>
                                    <div class="text-center w-[10%]">Amount</div>
                                    <div class="text-center w-[10%]">Document</div>
                                    <div class="text-center w-[10%]">Payment</div>
                                    <div class="text-center w-[10%]">Balance</div>
                                    <div class="text-center w-[10%]">Status</div>
                                </div>

                                <div class="search_container overflow-y-auto grow my-scrollbar-2">
                                    @foreach ($finalData as $data)
                                        <div id="{{ $data['id'] }}" data-json="{{ json_encode($data) }}"
                                            class="contextMenuToggle modalToggle relative group flex border-b border-[var(--h-bg-color)] items-center py-2 cursor-pointer hover:bg-[var(--h-secondary-bg-color)] transition-all fade-in ease-in-out">
                                            <span class="text-center w-[10%]">{{ date('d-M-Y', strtotime($data['date'])) }}</span>
                                            <span
                                                class="text-center w-[15%] capitalize">{{ $data['customer']['customer_name'] }}</span>
                                            <span class="text-center w-[10%]">{{ $data['order_no'] ?? $data['program_no'] }}</span>
                                            <span
                                                class="text-center w-[10%] capitalize">{{ str_replace('_', ' ', $data['category'] ?? ($data['payment_programs']['category'] ?? '-')) }}</span>
                                            <span class="text-center w-[10%]">
                                                @php
                                                    $beneficiary = '-';
                                                    if (isset($data['category'])) {
                                                        if (
                                                            $data['category'] == 'supplier' &&
                                                            isset($data['sub_category']['supplier_name'])
                                                        ) {
                                                            $beneficiary = $data['sub_category']['supplier_name'];
                                                        } elseif (
                                                            $data['category'] == 'customer' &&
                                                            isset($data['sub_category']['customer_name'])
                                                        ) {
                                                            $beneficiary = $data['sub_category']['customer_name'];
                                                        } elseif ($data['category'] == 'waiting' && isset($data['remarks'])) {
                                                            $beneficiary = $data['remarks'];
                                                        } elseif (
                                                            $data['category'] == 'self_account' &&
                                                            isset($data['sub_category']['account_title'])
                                                        ) {
                                                            $beneficiary = $data['sub_category']['account_title'];
                                                        }
                                                    } elseif (isset($data['payment_programs']['category'])) {
                                                        if (
                                                            $data['payment_programs']['category'] == 'supplier' &&
                                                            isset($data['payment_programs']['sub_category']['supplier_name'])
                                                        ) {
                                                            $beneficiary =
                                                                $data['payment_programs']['sub_category']['supplier_name'];
                                                        } elseif (
                                                            $data['payment_programs']['category'] == 'customer' &&
                                                            isset($data['payment_programs']['sub_category']['customer_name'])
                                                        ) {
                                                            $beneficiary =
                                                                $data['payment_programs']['sub_category']['customer_name'];
                                                        } elseif (
                                                            $data['payment_programs']['category'] == 'waiting' &&
                                                            isset($data['payment_programs']['remarks'])
                                                        ) {
                                                            $beneficiary = $data['payment_programs']['remarks'];
                                                        } elseif (
                                                            $data['payment_programs']['category'] == 'self_account' &&
                                                            isset($data['payment_programs']['sub_category']['account_title'])
                                                        ) {
                                                            $beneficiary =
                                                                $data['payment_programs']['sub_category']['account_title'];
                                                        }
                                                    }
                                                @endphp
                                                {{ $beneficiary }}
                                            </span>
                                            <span
                                                class="text-center w-[10%]">{{ number_format($data['amount'] ?? $data['netAmount'], 1) }}</span>
                                            <span class="text-center w-[10%]">{{ $data['document'] ?? '-' }}</span>
                                            <span
                                                class="text-center w-[10%]">{{ number_format($data['payment'] ?? '0', 1) }}</span>
                                            <span
                                                class="text-center w-[10%]">{{ number_format($data['balance'] ?? '0', 1) }}</span>
                                            <span class="text-center w-[10%]">{{ $data['status'] ?? '-' }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            <p id="noItemsError" style="display: none" class="text-sm text-[var(--border-error)]">No items found</p>
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
        <div class="context-menu absolute top-0 text-sm" style="display: none;">
            <div
                class="border border-gray-600 w-48 bg-[var(--secondary-bg-color)] text-[var(--text-color)] shadow-lg rounded-xl transform transition-all duration-300 ease-in-out z-50">
                <ul class="p-2">
                    <li>
                        <button id="show-details" type="button"
                            class="flex items-center w-full px-4 py-2 text-left hover:bg-[var(--h-bg-color)] rounded-md transition-all duration-300 ease-in-out cursor-pointer">Show
                            Details</button>
                    </li>
                    <li>
                        <button id="add-payment" type="button"
                            class="flex items-center w-full px-4 py-2 text-left hover:bg-[var(--h-bg-color)] rounded-md transition-all duration-300 ease-in-out cursor-pointer">Add
                            Payment</button>
                    </li>
                    <li>
                        <button id="update-program" type="button"
                            class="flex items-center w-full px-4 py-2 text-left hover:bg-[var(--h-bg-color)] rounded-md transition-all duration-300 ease-in-out cursor-pointer">Update
                            Program</button>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        let contextMenu = document.querySelector('.context-menu');
        let isContextMenuOpened = false;

        function closeContextMenu() {
            contextMenu.classList.remove('fade-in');
            contextMenu.style.display = 'none';
            isContextMenuOpened = false;
        }

        function openContextMenu() {
            closeAllDropdowns()
            contextMenu.classList.add('fade-in');
            contextMenu.style.display = 'block';
            isContextMenuOpened = true;
        }

        let contextMenuToggle = document.querySelectorAll('.contextMenuToggle');

        contextMenuToggle.forEach(toggle => {
            toggle.addEventListener('contextmenu', (e) => {
                generateContextMenu(e);
            });
        });

        function generateContextMenu(e) {
            contextMenu.classList.remove('fade-in');

            let item = e.target.closest('.modalToggle');
            let data = JSON.parse(item.dataset.json);

            const wrapper = document.querySelector(".wrapper"); // Replace with your wrapper's ID

            if (!contextMenu || !wrapper) return;

            const wrapperRect = wrapper.getBoundingClientRect(); // Get wrapper's position

            let x = e.clientX - wrapperRect.left; // Adjust X relative to wrapper
            let y = e.clientY - wrapperRect.top; // Adjust Y relative to wrapper

            // Prevent right edge overflow
            if (x + contextMenu.offsetWidth > wrapperRect.width) {
                x -= contextMenu.offsetWidth;
            }

            // Prevent bottom edge overflow
            if (y + contextMenu.offsetHeight > wrapperRect.height) {
                y -= contextMenu.offsetHeight;
            }

            contextMenu.style.left = `${x}px`;
            contextMenu.style.top = `${y}px`;

            openContextMenu();

            document.addEventListener('mousedown', (e) => {
                if (e.target.id === "show-details") {
                    generateModal(item)
                }
            });

            document.addEventListener('mousedown', (e) => {
                if (e.target.id === "add-payment") {
                    goToAddPayment(data);
                }
            });

            document.addEventListener('mousedown', (e) => {
                if (e.target.id === "update-program") {
                    generateUpdateProgramModal(data);
                }
            });

            // Function to remove context menu
            const removeContextMenu = (event) => {
                if (!contextMenu.contains(event.target)) {
                    closeContextMenu();
                    document.removeEventListener('click', removeContextMenu);
                    document.removeEventListener('contextmenu', removeContextMenu);
                }
            }

            // Wait for a small delay before attaching event listeners to avoid immediate removal
            setTimeout(() => {
                document.addEventListener('mousedown', removeContextMenu);
            }, 10);
        }

        let isModalOpened = false;
        let isUpdateupdateModalOpened = false;
        let card = document.querySelectorAll('.modalToggle')

        card.forEach(item => {
            item.addEventListener('click', () => {
                generateModal(item);
            });
        });

        function generateModal(item) {
            let modalDom = document.getElementById('modal');
            let data = JSON.parse(item.dataset.json);
            let cardsHTML = '';
            if (data && (data.payments?.length > 0 || data.payment_programs?.payments?.length > 0)) {
                let paymentsArray = data.payments ?? data.payment_programs.payments ?? [];
                paymentsArray.forEach(function(payment) {
                    cardsHTML += `
                        <div class="card relative border flex items-center justify-between border-gray-600 shadow rounded-xl min-w-[100px] py-3 px-4 cursor-pointer overflow-hidden fade-in">
                            <div class="text-start">
                                <h5 class="text-xl mb-2 text-[var(--text-color)] capitalize font-semibold leading-none">
                                    Date: <span>${formatDate(payment.date)}</span>
                                </h5>
                                <p class="text-[var(--secondary-text)] tracking-wide text-sm capitalize"><strong>Method:</strong> <span>${payment.method}</span></p>
                                <p class="text-[var(--secondary-text)] tracking-wide text-sm capitalize"><strong>Amount:</strong> <span>${formatNumbersWithDigits(payment.amount, 1, 1)}</span></p>
                                <p class="text-[var(--secondary-text)] tracking-wide text-sm capitalize"><strong>Account:</strong> <span>${payment.bank_account?.account_title ?? ' - '} | ${payment.bank_account?.bank.short_title ?? ' - '}</span></p>
                            </div>
                        </div>
                    `;
                });
            } else {
                cardsHTML = `
                    <div class="text-[var(--border-error)] text-center font-medium h-full col-span-full">Not Found</div>
                `;
            }
            modalDom.innerHTML = `
                <x-modal id="modalForm" classForBody="p-5 pt-4 max-w-6xl h-[45rem]" closeAction="closeModal">
                    <!-- Modal Content Slot -->
                    <div class="flex items-start relative h-full">
                        <div class="flex-1 h-full overflow-y-auto my-scrollbar-2 flex flex-col pt-2 pr-1">
                            <x-search-header heading="Payment Details"/>
                                
                            <div class='overflow-y-auto my-scrollbar-2 pt-2 grow'>
                                <div class="card_container grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">
                                    ${cardsHTML}
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal Action Slot -->
                    <x-slot name="actions">
                        <button id="update-program-btn-in-modal" type="button"
                            class="px-4 py-2 bg-[var(--secondary-bg-color)] border border-gray-600 text-[var(--secondary-text)] rounded-lg hover:bg-[var(--h-bg-color)] transition-all duration-300 ease-in-out cursor-pointer hover:scale-[0.95]">
                            Update Program
                        </button>
                        <button id="add-payment-btn-in-modal" type="button"
                            class="px-4 py-2 bg-[var(--secondary-bg-color)] border border-gray-600 text-[var(--secondary-text)] rounded-lg hover:bg-[var(--h-bg-color)] transition-all duration-300 ease-in-out cursor-pointer hover:scale-[0.95]">
                            App Payment
                        </button>
                        <button onclick="closeModal()" type="button"
                            class="px-4 py-2 bg-[var(--secondary-bg-color)] border border-gray-600 text-[var(--secondary-text)] rounded-lg hover:bg-[var(--h-bg-color)] transition-all duration-300 ease-in-out cursor-pointer hover:scale-[0.95]">
                            Close
                        </button>
                    </x-slot>
                </x-modal>
            `;

            document.getElementById('add-payment-btn-in-modal').addEventListener('click', () => {
                goToAddPayment(data);
            })

            document.getElementById('update-program-btn-in-modal').addEventListener('click', () => {
                generateUpdateProgramModal(data);
            })

            openModal()
        }

        function generateUpdateProgramModal(data) {
            let modalDom = document.getElementById('updateModal');
            modalDom.innerHTML = `
                <x-modal id="updateModalForm" classForBody="p-4 pt-4" action="{{ route('payment-programs.update-program') }}" method="POST" closeAction="closeUpdateModal">
                    <!-- Modal Content Slot -->
                    <div class="flex items-start relative h-full">
                        <div class="flex-1 h-full overflow-y-auto my-scrollbar-2 flex flex-col pt-2 pr-1">
                            <x-search-header heading="Update Program"/>
                            <div class="grid grid-cols-2 gap-4 p-1">
                                {{-- date --}}
                                <x-input label="Date" id="date" type="date" disabled/>

                                {{-- cusomer --}}
                                <x-select 
                                    label="Customer"
                                    name="customer_id"
                                    id="customer_id"
                                    required
                                    showDefault
                                    disabled
                                />
                                
                                {{-- category --}}
                                <x-select 
                                    label="Category"    
                                    name="category"
                                    id="category"
                                    :options="$categories_options"
                                    required
                                    showDefault
                                />
                                
                                {{-- cusomer --}}
                                <x-select 
                                    label="Disabled"
                                    name="sub_category"
                                    id="subCategory"
                                    disabled
                                    showDefault
                                />
                                
                                <input type="hidden" name="program_id" id="program_id" />
                                {{-- remarks --}}
                                <x-input label="Remarks" name="remarks" id="remarks" placeholder="Enter Remarks" />

                                <div class="col-span-full">
                                    {{-- amount --}}
                                    <x-input label="Amount" type="number" name="amount" id="amount" placeholder='Enter Amount' required />
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal Action Slot -->
                    <x-slot name="actions">
                        <button onclick="closeUpdateModal()" type="button"
                            class="px-4 py-2 bg-[var(--secondary-bg-color)] border border-gray-600 text-[var(--secondary-text)] rounded-lg hover:bg-[var(--h-bg-color)] transition-all duration-300 ease-in-out cursor-pointer hover:scale-[0.95]">
                            Close
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-[var(--bg-success)] border border-[var(--border-success)] text-[var(--success-text)] rounded-lg hover:bg-[var(--h-bg-success)] transition-all duration-300 ease-in-out cursor-pointer hover:scale-[0.95]">
                            Update
                        </button>
                    </x-slot>
                </x-modal>
            `;

            updateProgramFormScript(data)
            openUpdateModal()
        }

        function updateProgramFormScript(data) {
            let allData = data;
            data = data.payment_programs || data;
            data.customer ||= allData.customer;
            let programIdDom = document.getElementById('program_id');
            let dateInpDom = document.getElementById('date');
            let customerSelectDom = document.getElementById('customer_id');
            let categorySelectDom = document.getElementById('category');
            let amountInpDom = document.getElementById('amount');

            let subCategoryLabelDom = document.querySelector('[for=sub_category]');
            let subCategorySelectDom = document.getElementById('subCategory');
            let subCategoryFirstOptDom = subCategorySelectDom.children[0];

            let remarksInputDom = document.getElementById('remarks');
            remarksInputDom.parentElement.parentElement.classList.add("hidden");

            programIdDom.value = data.id;

            dateInpDom.value = data.date;

            customerSelectDom.innerHTML += `
                <option value="${data.customer.id}" selected>${data.customer.customer_name} | ${data.customer.city.title}</option>
            `;

            categorySelectDom.value = data.category;
            getCategoryData(categorySelectDom.value)

            categorySelectDom.addEventListener('change', () => {
                getCategoryData(categorySelectDom.value);
            })

            amountInpDom.value = data.amount;

            function getCategoryData(value) {
                if (value != "waiting") {
                    subCategorySelectDom.parentElement.parentElement.classList.remove("hidden");
                    remarksInputDom.parentElement.parentElement.classList.add("hidden");

                    $.ajax({
                        url: "/get-category-data",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            category: value,
                        },
                        success: function(response) {
                            let clutter = `
                                <option value='' selected>
                                    -- No option avalaible --
                                </option>
                            `;
                            switch (value) {
                                case 'self_account':
                                    if (response.length > 0) {
                                        clutter = '';
                                        clutter += `
                                            <option value='' selected>
                                                -- Select Self Account --
                                            </option>
                                        `;
                                        subCategorySelectDom.disabled = false;
                                    } else {
                                        subCategorySelectDom.disabled = true;
                                        subCategoryFirstOptDom.textContent = '-- No options available --';
                                    }

                                    response.forEach(subCat => {
                                        clutter += `
                                            <option value='${subCat.id}'>
                                                ${subCat.account_title} | ${subCat.bank.short_title}
                                            </option>
                                        `;
                                    });

                                    subCategoryLabelDom.textContent = 'Self Account';
                                    subCategoryFirstOptDom.textContent = '-- Select Self Account --';
                                    break;

                                case 'supplier':
                                    if (response.length > 0) {
                                        clutter = '';
                                        clutter += `
                                            <option value='' selected>
                                                -- Select Supplier --
                                            </option>
                                        `;
                                        subCategorySelectDom.disabled = false;
                                    } else {
                                        subCategorySelectDom.disabled = true;
                                        subCategoryFirstOptDom.textContent = '-- No options available --';
                                    }

                                    response.forEach(subCat => {
                                        clutter += `
                                            <option value='${subCat.id}'>
                                                ${subCat.supplier_name} | Balance: ${formatNumbersWithDigits(subCat.balance, 1, 1)}
                                            </option>
                                        `;
                                    });

                                    subCategoryLabelDom.textContent = 'Supplier';
                                    subCategoryFirstOptDom.textContent = '-- Select Supplier --';
                                    break;

                                case 'customer':
                                    clutter = '';
                                    clutter += `
                                        <option value='' selected>
                                            -- Select Customer --
                                        </option>
                                    `;

                                    response.forEach(subCat => {
                                        if (subCat.id != customerSelectDom.value) {
                                            clutter += `
                                                <option value='${subCat.id}'>
                                                    ${subCat.customer_name} | ${subCat.city} | Baalance: ${formatNumbersWithDigits(subCat.balance, 1, 1)}
                                                </option>
                                            `;
                                            subCategorySelectDom.disabled = false;
                                        }
                                    });

                                    subCategoryLabelDom.textContent = 'Customer';
                                    subCategoryFirstOptDom.textContent = '-- Select Customer --';
                                    break;

                                default:
                                    break;
                            }

                            subCategorySelectDom.innerHTML = clutter;
                            if (data.category == value) {
                                subCategorySelectDom.value = data.sub_category_id;
                            }
                        }
                    });
                } else {
                    subCategorySelectDom.parentElement.parentElement.classList.add("hidden");
                    remarksInputDom.parentElement.parentElement.classList.remove("hidden");
                    remarksInputDom.value = data.remarks;
                }
            }
        }

        document.addEventListener('mousedown', (e) => {
            const {
                id
            } = e.target;
            if (id === 'modalForm') {
                closeModal();
            } else if (id === 'updateModalForm') {
                closeUpdateModal();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && isModalOpened) {
                closeContextMenu();
                closeModal();
                closeUpdateModal();
            }
        })

        function openModal() {
            isModalOpened = true;
            document.getElementById('modal').classList.remove('hidden');
            closeAllDropdowns();
            closeContextMenu();
        }

        function closeModal() {
            let modal = document.getElementById('modal');
            modal.classList.add('fade-out');

            modal.addEventListener('animationend', () => {
                modal.classList.add('hidden');
                modal.classList.remove('fade-out');
            }, {
                once: true
            });
        }

        function openUpdateModal() {
            isModalOpened = true;
            document.getElementById('updateModal').classList.remove('hidden');
            closeAllDropdowns();
            closeContextMenu();
        }

        function closeUpdateModal() {
            let modal = document.getElementById('updateModal');
            modal.classList.add('fade-out');

            modal.addEventListener('animationend', () => {
                modal.classList.add('hidden');
                modal.classList.remove('fade-out');
            }, {
                once: true
            });
        }

        function goToAddPayment(program) {
            const url = new URL("{{ route('customer-payments.create') }}", window.location.origin);
            url.searchParams.set("program_id", program.payment_programs?.id ?? program
                .id); // or send other keys like amount, customer_id, etc.
            window.location.href = url.toString();
        }

        // Function for Search
        function filterData(search) {
            const filteredData = cardsDataArray.filter(item => {
                let name = '';
                switch (filterType) {
                    case 'all':
                        switch (item.category) {

                            case 'supplier':
                                name = item.sub_category.supplier_name;
                                break;
                            case 'customer':
                                name = item.sub_category.customer_name;
                                break;
                            case 'self':
                                name = item.sub_category.name;
                                break;
                            default:
                                break;
                        }

                        return (
                            item.account_title.toLowerCase().includes(search) ||
                            item.category.toLowerCase().includes(search) ||
                            name.toLowerCase().includes(search)
                        );
                        break;

                    case 'title':
                        return (
                            item.account_title.toLowerCase().includes(search)
                        );
                        break;

                    case 'category':
                        return (
                            item.category.toLowerCase().includes(search)
                        );
                        break;

                    case 'name':
                        // let name = '';
                        switch (item.category) {
                            case 'supplier':
                                name = item.sub_category.supplier_name;
                                break;
                            case 'customer':
                                name = item.sub_category.customer_name;
                                break;
                            case 'self':
                                name = item.sub_category.name;
                                break;
                            default:
                                break;
                        }

                        return (
                            name.toLowerCase().includes(search)
                        );
                        break;

                    default:
                        // let name = '';
                        switch (item.category) {
                            case 'supplier':
                                name = item.sub_category.supplier_name;
                                break;
                            case 'customer':
                                name = item.sub_category.customer_name;
                                break;
                            case 'self':
                                name = item.sub_category.name;
                                break;
                            default:
                                break;
                        }

                        return (
                            item.account_title.toLowerCase().includes(search) ||
                            item.category.toLowerCase().includes(search) ||
                            name.toLowerCase().includes(search)
                        );
                        break;
                }
            });

            return filteredData;
        }
    </script>
@endsection
