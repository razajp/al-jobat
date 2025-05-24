@extends('app')
@section('title', 'Generate Invoice | ' . app('company')->name)
@section('content')

@php
    $invoiceType = Auth::user()->invoice_type;
@endphp

    @if ($invoiceType == 'shipment')
        <style>
            .checkbox-container:has(.row-checkbox:checked) input[type="number"] {
                pointer-events: all;
                opacity: 1;
            }
        </style>
    
        <!-- Modals -->
        <div id="modal"
            class="mainModal hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-[var(--overlay-color)] fade-in">
            <x-modal id="modalForm" classForBody="p-5 max-w-6xl h-[45rem]" closeAction="closeModal">
                <!-- Modal Content Slot -->
                <div class="flex items-start relative h-full">
                    <div class="flex-1 h-full overflow-y-auto my-scrollbar-2 flex flex-col">
                        <div class="pr-5 pt-1">
                            <x-search-header heading="Customers" :filter_items="[
                                'all' => 'All',
                                '#' => 'Article No.',
                                'category' => 'Category',
                                'season' => 'Season',
                                'size' => 'Size',
                            ]"/>
                        </div>
            
                        <div class="flex items-center justify-between bg-[var(--h-bg-color)] rounded-lg px-4 py-2 mb-3 shadow-sm border border-[var(--h-border-color)]">
                            <div class="text-sm font-medium">
                                Selected: <span id="selected-count" class="text-[var(--primary-color)] font-semibold">0</span> / 
                                <span id="total-count">0</span> customers
                            </div>
                            <div class="text-sm font-medium">
                                Max Cottons: <span id="max-cottons-count" class="text-[var(--primary-color)] font-semibold">0</span>
                            </div>
                        </div>
                        <div class='overflow-y-auto my-scrollbar-2 pt-2 grow'><!-- HEADER BAR -->
                            <div class="text-center flex bg-[var(--h-bg-color)] rounded-lg font-medium py-2 items-center select-none">
                                <div class="text-left pl-5 flex items-center w-[12%]">Select</div>
                                <div class="grow">Customer</div>
                                <div class="w-[15%]">Urdu Title</div>
                                <div class="w-[15%]">Category</div>
                                <div class="w-[15%]">Balance</div>
                                <div class="w-[15%]">Status</div>
                            </div>
                        
                            <div id="customer-container" class="search_container overflow-y-auto grow my-scrollbar-2">
                                
                            </div>
                        </div>
                        
                        <div class="flex w-full gap-4 text-sm mt-5">
                            <div
                                class="total-qty flex justify-between items-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4 w-full">
                                <div class="grow">Total Quantity - Pcs</div>
                                <div id="totalOrderedQty">0</div>
                            </div>
                            <div
                                class="final flex justify-between items-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4 w-full">
                                <div class="grow">Total Amount - Rs.</div>
                                <div id="totalOrderAmount">0.0</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal Action Slot -->
                <x-slot name="actions">
                    <button onclick="closeModal()" type="button"
                        class="px-4 py-2 bg-[var(--secondary-bg-color)] border border-gray-600 text-[var(--secondary-text)] rounded-lg hover:bg-[var(--h-bg-color)] transition-all duration-300 ease-in-out cursor-pointer">
                        Close
                    </button>
                </x-slot>
            </x-modal>
        </div>
    @endif

    <div class="switch-btn-container flex absolute top-3 md:top-17 left-3 md:left-5 z-[100]">
        <div class="switch-btn relative flex border-3 border-[var(--secondary-bg-color)] bg-[var(--secondary-bg-color)] rounded-2xl overflow-hidden">
            <!-- Highlight rectangle -->
            <div id="highlight" class="absolute h-full rounded-xl bg-[var(--bg-color)] transition-all duration-300 ease-in-out z-0"></div>
            
            <!-- Buttons -->
            <button
                id="orderBtn"
                type="button"
                class="relative z-10 px-3.5 md:px-5 py-1.5 md:py-2 cursor-pointer rounded-xl transition-colors duration-300"
                onclick="setInvoiceType(this, 'order')"
            >
                <div class="hidden md:block">Order</div>
                <div class="block md:hidden"><i class="fas fa-cart-shopping text-xs"></i></div>
            </button>
            <button
                id="shipmentBtn"
                type="button"
                class="relative z-10 px-3.5 md:px-5 py-1.5 md:py-2 cursor-pointer rounded-xl transition-colors duration-300"
                onclick="setInvoiceType(this, 'shipment')"
            >
                <div class="hidden md:block">Shipment</div>
                <div class="block md:hidden"><i class="fas fa-box-open text-xs"></i></div>
            </button>
        </div>
    </div>

    <script>
        let btnTypeGlobal = "order";

        function setInvoiceType(btn, btnType) {
            // check if its already selected
            if (btnTypeGlobal == btnType) {
                return;
            }

            $.ajax({
                url: "/set-invoice-type",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    invoice_type: btnType
                },
                success: function () {
                    location.reload();
                },
                error: function () {
                    alert("Failed to update invoice type.");
                    $(btn).prop("disabled", false);
                }
            });


            moveHighlight(btn, btnType);
        }

        function moveHighlight(btn, btnType) {
            const highlight = document.getElementById("highlight");
            const rect = btn.getBoundingClientRect();
            
            const parentRect = btn.parentElement.getBoundingClientRect();
        
            // Move and resize the highlight
            highlight.style.width = `${rect.width}px`;
            highlight.style.left = `${rect.left - parentRect.left - 3}px`;

            btnTypeGlobal = btnType; 
        }
    
        // Initialize highlight on load
        window.onload = () => {
            @if($invoiceType == 'order')
                const activeBtn = document.querySelector("#orderBtn");
                moveHighlight(activeBtn, "order");
            @else
                const activeBtn = document.querySelector("#shipmentBtn");
                moveHighlight(activeBtn, "shipment");
            @endif
        };
    </script>

    <!-- Main Content -->
    <!-- Progress Bar -->
    <div class="mb-5 max-w-4xl mx-auto">
        <x-search-header heading="Generate Invoice" link linkText="Show Invoices" linkHref="{{ route('invoices.index') }}"/>
        <x-progress-bar :steps="['Generate Invoice', 'Preview']" :currentStep="1" />
    </div>

    <!-- Form -->
    <form id="form" action="{{ route('invoices.store') }}" method="post" enctype="multipart/form-data"
        class="bg-[var(--secondary-bg-color)] text-sm rounded-xl shadow-lg p-8 border border-[var(--h-bg-color)] pt-12 max-w-4xl mx-auto  relative overflow-hidden">
        @csrf
        <x-form-title-bar title="Generate Invoice" />

        <!-- Step 1: Generate Invoice -->
        @if($invoiceType == 'order')
            <div class="step1 space-y-4 ">
                <div class="flex justify-between gap-4">
                    <input type="hidden" name="date" value='{{ now()->toDateString() }}'>
                    {{-- order_no --}}
                    <div class="grow">
                        <x-input label="Order Number" name="order_no" id="order_no" autocomplete="off" placeholder="Enter order number" required withButton btnId="generateInvoiceBtn" btnText="Generate Invoice" value="25-"/>
                    </div>
                </div>
                {{-- rate showing --}}
                <div id="article-table" class="w-full text-left text-sm">
                    <div class="flex justify-between items-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4 mb-4">
                        <div class="w-[5%]">#</div>
                        <div class="w-[11%]">Article</div>
                        <div class="w-[11%]">Packets</div>
                        <div class="w-[10%]">Pcs</div>
                        <div class="grow">Decs.</div>
                        <div class="w-[8%]">Pcs/Pkt.</div>
                        <div class="w-[12%] text-right">Rate/Pc</div>
                        <div class="w-[15%] text-right">Amount</div>
                        <div class="w-[15%] text-right">Action</div>
                    </div>
                    <div id="article-list" class="h-[20rem] overflow-y-auto my-scrollbar-2">
                        <div class="text-center bg-[var(--h-bg-color)] rounded-lg py-3 px-4">No Rates Added</div>
                    </div>
                </div>

                <input type="hidden" name="articles_in_invoice" id="articles_in_invoice" value="">

                <div class="flex w-full grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-nowrap">
                    <div class="total-qty flex justify-between items-center border border-gray-600 cursor-not-allowed rounded-lg py-2 px-4 w-full">
                        <div class="grow">Total Quantity - Pcs</div>
                        <div id="totalQuantityInForm">0</div>
                    </div>
                    <div class="final flex justify-between items-center border border-gray-600 cursor-not-allowed rounded-lg py-2 px-4 w-full">
                        <div class="grow">Gross Amount - Rs.</div>
                        <div id="totalAmountInForm">0.0</div>
                    </div>
                    <div class="final flex justify-between items-center border border-gray-600 cursor-not-allowed rounded-lg py-2 px-4 w-full">
                        <div class="grow">Discount - %</div>
                        <div id="dicountInForm">0</div>
                    </div>
                    <div class="final flex justify-between items-center border border-gray-600 cursor-not-allowed rounded-lg py-2 px-4 w-full">
                        <div class="grow">Net Amount - Rs.</div>
                        <input type="text" name="netAmount" id="netAmountInForm" value="0.0" readonly
                            class="text-right bg-transparent outline-none w-1/2 border-none" />
                    </div>
                </div>
            </div>
        @else
            <div class="step1 space-y-4 ">
                <div class="flex justify-between gap-4">
                    <input type="hidden" name="date" value='{{ now()->toDateString() }}'>
                    {{-- shipment_no --}}
                    <div class="grow">
                        <x-input label="Shipment Number" type="number" name="shipment_no" id="shipment_no" placeholder="Enter shipment number" required withButton btnId="selectCustomersBtn" btnText="Select Customers" value=""/>
                    </div>
                </div>
                {{-- rate showing --}}
                <div id="article-table" class="w-full text-left text-sm">
                    <div class="flex justify-between items-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4 mb-4">
                        <div class="w-[5%]">#</div>
                        <div class="w-[11%]">Article</div>
                        <div class="w-[11%]">Packets</div>
                        <div class="w-[10%]">Pcs</div>
                        <div class="grow">Decs.</div>
                        <div class="w-[8%]">Pcs/Pkt.</div>
                        <div class="w-[12%] text-right">Rate/Pc</div>
                        <div class="w-[15%] text-right">Amount</div>
                    </div>
                    <div id="article-list" class="h-[20rem] overflow-y-auto my-scrollbar-2">
                        <div class="text-center bg-[var(--h-bg-color)] rounded-lg py-3 px-4">No Rates Added</div>
                    </div>
                </div>

                <input type="hidden" name="customers_array" id="customers_array" value="">

                <input type="hidden" name="printAfterSave" id="printAfterSave" value="0">

                <div class="flex w-full grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-nowrap">
                    <div class="total-qty flex justify-between items-center border border-gray-600 cursor-not-allowed rounded-lg py-2 px-4 w-full">
                        <div class="grow">Total Quantity - Pcs</div>
                        <div id="totalQuantityInForm">0</div>
                    </div>
                    <div class="final flex justify-between items-center border border-gray-600 cursor-not-allowed rounded-lg py-2 px-4 w-full">
                        <div class="grow">Gross Amount - Rs.</div>
                        <div id="totalAmountInForm">0.0</div>
                    </div>
                    <div class="final flex justify-between items-center border border-gray-600 cursor-not-allowed rounded-lg py-2 px-4 w-full">
                        <div class="grow">Discount - %</div>
                        <div id="dicountInForm">0</div>
                    </div>
                    <div class="final flex justify-between items-center border border-gray-600 cursor-not-allowed rounded-lg py-2 px-4 w-full">
                        <div class="grow">Net Amount - Rs.</div>
                        <input type="text" id="netAmountInForm" value="0.0" readonly
                            class="text-right bg-transparent outline-none w-1/2 border-none" />
                    </div>
                </div>
            </div>
        @endif

        <!-- Step 2: view order -->
        <div class="step2 hidden space-y-4 text-black h-[35rem] overflow-y-auto my-scrollbar-2 bg-white rounded-md">
            <div id="preview-container" class="w-[210mm] h-[297mm] mx-auto overflow-hidden relative">
                <div id="preview" class="preview flex flex-col h-full">
                    <h1 class="text-[var(--border-error)] font-medium text-center mt-5">No Preview avalaible.</h1>
                </div>
            </div>
        </div>
    </form>

    <script>
        let articlesInInvoice = [];
        let totalQuantityPcs = 0;
        let totalAmount = 0;
        let netAmount = 0;
        let discount = 0;
        let isModalOpened = false;
        const lastInvoice = @json($last_Invoice);
        let companyData = @json(app('company'));
    </script>

    @if ($invoiceType == 'shipment')
        <script>
            let shipmentArticles = [];
            const shipmentNoDom = document.getElementById("shipment_no");
            const selectCustomersBtn = document.getElementById("selectCustomersBtn");
            selectCustomersBtn.disabled = true;
            let allCottonCountInputs = document.querySelectorAll('.cottonCount');

            let selectedCustomersArray = [];

            let ogMaxCottonCount = 0;
            let allCustomers = [];
            let maxCottonCount = 0;

            shipmentNoDom.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    getShipmentDetails();
                }
            });

            selectCustomersBtn.addEventListener("click", (e)=>{
                getShipmentDetails();
            });

            function getShipmentDetails() {
                $.ajax({
                    url: "/get-shipment-details",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        shipment_no: shipmentNoDom.value
                    },
                    success: function (response) {
                        console.log(response);
                        
                        if (!response.error) {
                            openModal();

                            shipmentArticles = response.shipment.articles;
                            discount = response.shipment.discount ?? 0;
                            allCustomers = response.customers;

                            renderCustomers(allCustomers)
                            renderList();
                            renderCalcBottom();
                            calculateNoOfSelectableCustomers(response.shipment.articles);
                            document.getElementById('total-count').textContent = allCustomers.length ?? 0;
                            addListners();
                        }
                    }
                });
            }

            function calculateNoOfSelectableCustomers(articlesArray) {
                let countOfCottonsOfArticles = [];

                articlesArray.forEach((article) => {
                    countOfCottonsOfArticles.push(Math.floor(article.available_stock / article.shipment_quantity));
                });

                maxCottonCount = Math.min(...countOfCottonsOfArticles);
                ogMaxCottonCount = maxCottonCount;

                document.getElementById('max-cottons-count').textContent = maxCottonCount;
            }

            function openModal() {
                document.getElementById('modal').classList.remove('hidden');
                document.getElementById('modal').classList.add('flex');
                isModalOpened = true;
                closeAllDropdowns();
            }
            
            function closeModal() {
                isModalOpened = false;
                let modal = document.getElementById('modal');
                modal.classList.add('fade-out');

                modal.addEventListener('animationend', () => {
                    modal.classList.add('hidden');
                    modal.classList.remove('fade-out');
                }, {
                    once: true
                });

                setArrayToCustomersArrayInput();
            }

            function setArrayToCustomersArrayInput() {
                const customersArrayInput = document.getElementById("customers_array");
                let finalCustomersArray = selectedCustomersArray.map(customer => {
                    return {
                        id: customer.id,
                        cotton_count: customer.cotton_count,
                    }
                })
                customersArrayInput.value = JSON.stringify(finalCustomersArray);
            }
            
            document.addEventListener('mousedown', (e) => {
                const { id } = e.target;
                if (id === 'modalForm') {
                    closeModal();
                }
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    if (isModalOpened == true) {
                        closeModal();
                    }
                }
            });
            
            shipmentNoDom.addEventListener('input', (e) => {
                let value = e.target.value;

                value = value.replace(/\D/g, '');

                e.target.value = value;

                trackStateOfShipmentNo(e.target.value);
            });
            
            function trackStateOfShipmentNo(value) {
                if (value != "") {
                    selectCustomersBtn.disabled = false;
                } else {
                    selectCustomersBtn.disabled = true;
                }
            }

            const articleListDOM = document.getElementById('article-list');
            function renderList() {
                if (shipmentArticles && shipmentArticles.length > 0) {
                    totalAmount = 0;
                    totalQuantityPcs = 0;

                    let clutter = "";
                    shipmentArticles.forEach((selectedArticle, index) => {
                        if (selectedArticle.available_stock > selectedArticle.shipment_quantity) {
                            totalQuantityPcs += selectedArticle.shipment_quantity;

                            let articleAmount = selectedArticle.article.sales_rate * selectedArticle.shipment_quantity;

                            clutter += `
                                <div class="flex justify-between items-center border-t border-gray-600 py-3 px-4">
                                    <div class="w-[5%]">${index + 1}.</div>
                                    <div class="w-[11%]">#${selectedArticle.article.article_no}</div>
                                    <div class="w-[11%] pr-3">${Math.floor(formatNumbersDigitLess(selectedArticle.shipment_quantity / selectedArticle.article.pcs_per_packet))}</div>
                                    <div class="w-[10%]">${formatNumbersDigitLess(selectedArticle.shipment_quantity)}</div>
                                    <div class="grow">${selectedArticle.description}</div>
                                    <div class="w-[8%]">${selectedArticle.article.pcs_per_packet}</div>
                                    <div class="w-[12%] text-right">${formatNumbersWithDigits(selectedArticle.article.sales_rate, 1, 1)}</div>
                                    <div class="w-[15%] text-right">${formatNumbersWithDigits(articleAmount, 1, 1)}</div>
                                    <div class="w-[15%] text-right">${formatNumbersWithDigits(articleAmount, 1, 1)}</div>
                                </div>
                            `;

                            totalAmount += articleAmount;

                            selectedArticle.packets = selectedArticle.available_stock / selectedArticle.article.pcs_per_packet
                        }
                    });

                    articleListDOM.innerHTML = clutter;
                } else {
                    articleListDOM.innerHTML =
                        `<div class="text-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4">No Orders Yet</div>`;
                }
            }
            renderList();

            // Calc Bottom
            let totalQuantityInFormDom = document.getElementById('totalQuantityInForm');
            let totalAmountInFormDom = document.getElementById('totalAmountInForm');
            let dicountInFormDom = document.getElementById('dicountInForm');
            let netAmountInFormDom = document.getElementById('netAmountInForm');
            
            function renderCalcBottom() {
                netAmount = totalAmount - (totalAmount * (discount / 100));
                totalQuantityInFormDom.textContent = formatNumbersDigitLess(totalQuantityPcs);
                totalAmountInFormDom.textContent = formatNumbersWithDigits(totalAmount, 1, 1);
                dicountInFormDom.textContent = discount;
                netAmountInFormDom.value = formatNumbersWithDigits(netAmount, 1, 1);
            }

            // 

            function updateSelectedCount() {
                const checkboxes = document.querySelectorAll('.row-checkbox');
                const selected = document.querySelectorAll('.row-checkbox:checked').length;

                document.getElementById('selected-count').textContent = selected;
            }

            function addListners() {
                // Individual checkbox
                document.querySelectorAll('.row-checkbox').forEach(cb => {
                    cb.addEventListener('change', updateSelectedCount);
                });

                // Row click toggles checkbox
                document.querySelectorAll('.row-toggle').forEach(row => {
                    row.addEventListener('click', function (e) {
                        if (e.target.tagName.toLowerCase() === 'input') return;

                        const checkbox = this.querySelector('.row-checkbox');
                        checkbox.checked = !checkbox.checked;
                        checkbox.dispatchEvent(new Event('change')); // trigger count + update
                    });
                });

                // apply event listeners to all checkboxes
                document.querySelectorAll('.row-checkbox').forEach(cb => {
                    cb.addEventListener('change', function () {
                        const customerRowDOM = this.closest('.row-toggle');
                        selectCustomer(customerRowDOM);
                    });
                });
            }

            function selectCustomer(customerRowDOM) {
                const checkbox = customerRowDOM.querySelector('.row-checkbox');
                const customerData = JSON.parse(customerRowDOM.dataset.json);
                const customerId = customerData.id;

                let cottonCountInput = customerRowDOM.querySelector('input.cottonCount');
                let cottonCount = cottonCountInput.value;
                cottonCountInput.value = 1;

                const availableCottonCount = getAvailableCottonCount(cottonCountInput);

                if (checkbox.checked) {
                    if (availableCottonCount > 0) {   
                        customerData['cotton_count'] = cottonCount;
                        selectedCustomersArray.push(customerData);
                    }
                } else {
                    const index = selectedCustomersArray.findIndex(customer => customer.id === customerId);
                    if (index > -1) {
                        selectedCustomersArray.splice(index, 1);
                    }

                    cottonCountInput.dataset.previousValue = 1;
                }
                updateCustomerRowsState();
            }

            function setOnInput(input) {
                const cottonCount = parseInt(input.value);

                const customerRowDOM = input.closest('.row-toggle');
                const customerData = JSON.parse(customerRowDOM.dataset.json);
                const customerId = customerData.id;
                const index = selectedCustomersArray.findIndex(customer => customer.id === customerId);

                if (index >= 0) {
                    selectedCustomersArray[index]['cotton_count'] = cottonCount;
                }

                updateCustomerRowsState();
            }

            function validateCottonCount(currentInput) {
                currentInput.value = currentInput.value.replace(/[^\d]/g, '');

                const min = 1;
                const availableCottonCount = getAvailableCottonCount(currentInput);

                if (currentInput.value === '') {
                    currentInput.value = min;
                }

                const value = parseInt(currentInput.value, 10);

                if (value > availableCottonCount) {
                    currentInput.value = availableCottonCount;
                } else if (value < min) {
                    currentInput.value = min;
                }

                setOnInput(currentInput);
            }

            function getAvailableCottonCount(currentInput) {
                let sum = 0;
                document.querySelectorAll('.cottonCount').forEach(input => {
                    if (input !== currentInput) {
                        // Skip disabled inputs (opacity 0 or pointer-events none)
                        const style = window.getComputedStyle(input);
                        if (
                            style.opacity === '0' ||
                            style.pointerEvents === 'none'
                        ) return;

                        const val = parseInt(input.value, 10);
                        if (!isNaN(val)) sum += val;
                    }
                });
                
                let availableCottonCount = ogMaxCottonCount - sum;
                return availableCottonCount;
            }

            function updateCustomerRowsState() {
                const customerRows = document.querySelectorAll('.customer-row');

                const availableCottonCount = getAvailableCottonCount();

                customerRows.forEach((customerRow, index) => {
                    if (availableCottonCount > 0) {
                        customerRow.style.pointerEvents = 'all';
                        customerRow.style.opacity = '1';
                        customerRow.style.cursor = 'pointer';
                    } else {
                        const checkbox = customerRow.querySelector('.row-checkbox');
                        if (!checkbox.checked) {
                            customerRow.style.pointerEvents = 'none';
                            customerRow.style.opacity = '0.5';
                            customerRow.style.cursor = 'not-allowed';
                        }
                    }
                });
            }

            function renderCustomers(customers) {
                const container = document.getElementById('customer-container');
                container.innerHTML = ''; // Clear previous content

                customers.forEach(customer => {
                    const html = `
                        <div id="customer-${customer.id}" data-json='${JSON.stringify(customer)}' class="customer-row contextMenuToggle modalToggle relative text-center group flex border-b border-[var(--h-bg-color)] items-center py-2 cursor-pointer hover:bg-[var(--h-secondary-bg-color)] transition-all fade-in ease-in-out row-toggle select-none" >
                            <span class="text-left pl-5 flex items-center gap-4 checkbox-container w-[12%]">
                                <input type="checkbox" name="selected_customers[]"
                                    class="row-checkbox shrink-0 w-3.5 h-3.5 appearance-none border border-gray-400 rounded-sm checked:bg-[var(--primary-color)] checked:border-transparent focus:outline-none transition duration-150 cursor-pointer" />
                                
                                <input class="cottonCount w-[70%] border border-gray-600 bg-[var(--h-bg-color)] py-0.5 px-2 rounded-md text-xs focus:outline-none opacity-0 pointer-events-none" type="number" name="cotton_count" value="1" min="1" oninput="validateCottonCount(this)" onclick="this.select()" />
                            </span>
                            <span class="capitalize grow">${customer.customer_name} | ${customer.city.title}</span>
                            <span class="w-[15%]">${customer.urdu_title}</span>
                            <span class="w-[15%]">${customer.category}</span>
                            <span class="w-[15%]">${Number(customer.balance).toFixed(1)}</span>
                            <span class="w-[15%] capitalize">${customer.user?.status ?? ''}</span>
                        </div>
                    `;

                    container.insertAdjacentHTML('beforeend', html);
                });
            }

            // its for loop

            let invoiceNo;
            let invoiceDate;
            let cottonCount = 0;
            const previewDom = document.getElementById('preview');

            function generateInvoiceNo() {
                const yearShort = String(new Date().getFullYear()).slice(-2); // e.g., "25"

                let lastInvoiceNo = lastInvoice?.invoice_no || `${yearShort}-0000`;

                // Extract numeric part after the dash
                let lastNumber = lastInvoiceNo.split('-')[1];
                const nextInvoiceNo = String(parseInt(lastNumber, 10) + 1).padStart(4, '0');

                return `${yearShort}-${nextInvoiceNo}`;
            }

            function getInvoiceDate() {
                const date = new Date();

                // Extract day, month, and year
                const day = String(date.getDate()).padStart(2, '0');
                const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are 0-based
                const year = date.getFullYear();
                const dayOfWeek = date.getDay(); // 0 = Sunday, 1 = Monday, ..., 6 = Saturday

                // Array of weekday names
                const weekDays = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];

                // Return the formatted date
                return `${day}-${month}-${year}, ${weekDays[dayOfWeek]}`;
            }

            function getCottonCount() {
                return ++cottonCount;
            }

            function generateInvoice() {
                customerData = selectedCustomersArray[0];
                invoiceNo = generateInvoiceNo();
                invoiceDate = getInvoiceDate();
                cottonCount = getCottonCount();
                let totalQuantity = 0;
                if (shipmentArticles.length > 0) {  
                    previewDom.innerHTML = `
                        <div id="invoice" class="invoice flex flex-col h-full">
                            <div id="invoice-banner" class="invoice-banner w-full flex justify-between items-center mt-8 pl-5 pr-8">
                                <div class="left">
                                    <div class="invoice-logo">
                                        <img src="{{ asset('images/${companyData.logo}') }}" alt="Track Point"
                                            class="w-[12rem]" />
                                        <div class='mt-1'>${ companyData.phone_number }</div>
                                    </div>
                                </div>
                                <div class="left">
                                    <div class="invoice-logo">
                                        <h1 class="text-2xl font-medium text-[var(--h-primary-color)]">Sales Invoice</h1>
                                        <div class="space-y-1 mt-1">
                                            <div class="text-right leading-none ${cottonCount == 0 ? '' : ''} ">Cotton: ${cottonCount}</div>
                                            <div class="text-right leading-none">Shipment No.: ${shipmentNoDom.value}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr class="w-full my-3 border-black">
                            <div id="invoice-header" class="invoice-header w-full flex justify-between px-5">
                                <div class="left w-50 space-y-1">
                                    <div class="invoice-customer text-lg leading-none">M/s: ${customerData.customer_name}</div>
                                    <div class="invoice-person text-md text-lg leading-none">${customerData.urdu_title}</div>
                                    <div class="invoice-address text-md leading-none">${customerData.address}, ${customerData.city}</div>
                                    <div class="invoice-phone text-md leading-none">${customerData.phone_number}</div>
                                </div>
                                <div class="right my-auto pr-3 text-sm text-black space-y-1.5">
                                    <div class="invoice-date leading-none">Date: ${invoiceDate}</div>
                                    <div class="invoice-number leading-none">Invoice No.: ${invoiceNo}</div>
                                    <input type="hidden" name="invoice_no" value="${invoiceNo}">
                                    <div class="invoice-copy leading-none">Invoice Copy: Customer</div>
                                    <div class="invoice-copy leading-none">Document: Sales Invoice</div>
                                </div>
                            </div>
                            <hr class="w-full my-3 border-black">
                            <div id="invoice-body" class="invoice-body w-[95%] grow mx-auto">
                                <div class="invoice-table w-full">
                                    <div class="table w-full border border-black rounded-lg pb-2.5 overflow-hidden">
                                        <div class="thead w-full">
                                            <div class="tr flex justify-between w-full px-4 py-1.5 bg-[var(--primary-color)] text-white">
                                                <div class="th text-sm font-medium w-[7%]">S.No</div>
                                                <div class="th text-sm font-medium w-[10%]">Article</div>
                                                <div class="th text-sm font-medium w-[10%]">Packets</div>
                                                <div class="th text-sm font-medium w-[10%]">Pcs.</div>
                                                <div class="th text-sm font-medium grow">Description</div>
                                                <div class="th text-sm font-medium w-[10%]">Pcs/Pkt.</div>
                                                <div class="th text-sm font-medium w-[11%]">Rate/Pc.</div>
                                                <div class="th text-sm font-medium w-[11%]">Amount</div>
                                            </div>
                                        </div>
                                        <div id="tbody" class="tbody w-full">
                                            ${shipmentArticles.map((articles, index) => {
                                                const hrClass = index === 0 ? "mb-2.5" : "my-2.5";

                                                return `
                                                    <div>
                                                        <hr class="w-full ${hrClass} border-black">
                                                        <div class="tr flex justify-between w-full px-4">
                                                            <div class="td text-sm font-semibold w-[7%]">${index + 1}.</div>
                                                            <div class="td text-sm font-semibold w-[10%]">#${articles.article.article_no}</div>
                                                            <div class="td text-sm font-semibold w-[10%]">${articles.shipment_quantity / articles.article.pcs_per_packet}</div>
                                                            <div class="td text-sm font-semibold w-[10%]">${articles.shipment_quantity}</div>
                                                            <div class="td text-sm font-semibold grow">${articles.description}</div>
                                                            <div class="td text-sm font-semibold w-[10%]">${formatNumbersDigitLess(articles.article.pcs_per_packet)}</div>
                                                            <div class="td text-sm font-semibold w-[11%]">${formatNumbersWithDigits(articles.article.sales_rate, 2, 2)}</div>
                                                            <div class="td text-sm font-semibold w-[11%]">${formatNumbersWithDigits(parseInt(articles.article.sales_rate) * articles.shipment_quantity, 1, 1)}</div>
                                                        </div>
                                                    </div>
                                                `;
                                            }).join('')}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr class="w-full my-3 border-black">
                            <div class="flex flex-col space-y-2">
                                <div id="invoice-total" class="tr flex justify-between w-full px-2 gap-2 text-sm">
                                    <div class="total flex justify-between items-center border border-black rounded-lg py-1.5 px-4 w-full">
                                        <div class="text-nowrap">Total Quantity - Pcs</div>
                                        <div class="w-1/2 text-right grow">${formatNumbersDigitLess(totalQuantityPcs)}</div>
                                    </div>
                                    <div class="total flex justify-between items-center border border-black rounded-lg py-1.5 px-4 w-full">
                                        <div class="text-nowrap">Gross Amount</div>
                                        <div class="w-1/2 text-right grow">${formatNumbersWithDigits(totalAmount, 1, 1)}</div>
                                    </div>
                                </div>
                                <div id="invoice-total" class="tr flex justify-between w-full px-2 gap-2 text-sm">
                                    <div class="total flex justify-between items-center border border-black rounded-lg py-1.5 px-4 w-full">
                                        <div class="text-nowrap">Discount</div>
                                        <div class="w-1/2 text-right grow">${formatNumbersDigitLess(discount)}</div>
                                    </div>
                                    <div
                                        class="total flex justify-between items-center border border-black rounded-lg py-1.5 px-4 w-full">
                                        <div class="text-nowrap">Net Amount</div>
                                        <div class="w-1/2 text-right grow">${formatNumbersWithDigits(netAmount, 1, 1)}</div>
                                    </div>
                                </div>
                            </div>
                            <hr class="w-full my-3 border-black">
                            <div class="tfooter flex w-full text-sm px-4 justify-between mb-4 text-black">
                                <P class="leading-none">${ companyData.name } | ${ companyData.address }</P>
                                <p class="leading-none text-sm">&copy; 2025 Spark Pair | +92 316 5825495</p>
                            </div>
                        </div>
                    `;
                } else {
                    previewDom.innerHTML = `
                        <h1 class="text-[var(--border-error)] font-medium text-center mt-5">No Preview avalaible.</h1>
                    `;
                }
            }

            function validateForNextStep(){
                generateInvoice()
                return true;
            }

            document.addEventListener('DOMContentLoaded', function () {
                function addListenerToPrintAndSaveBtn() {
                    const printAndSaveBtn = document.getElementById('printAndSaveBtn');
                    printAndSaveBtn.addEventListener('click', function () {
                        document.getElementById('printAfterSave').value = 1;
                        document.getElementById('form').submit();
                    });
                }
                addListenerToPrintAndSaveBtn();
            });
        </script>
    @else
        <script>
            let orderedArticles = [];
            let customerData;
            const articleModalDom = document.getElementById("articleModal");
            const quantityModalDom = document.getElementById("quantityModal");
            const orderNoDom = document.getElementById("order_no");
            const generateInvoiceBtn = document.getElementById("generateInvoiceBtn");
            generateInvoiceBtn.disabled = true;
            
            // Calc Bottom
            let totalQuantityInFormDom = document.getElementById('totalQuantityInForm');
            let totalAmountInFormDom = document.getElementById('totalAmountInForm');
            let dicountInFormDom = document.getElementById('dicountInForm');
            let netAmountInFormDom = document.getElementById('netAmountInForm');

            let totalQuantityDOM;
            let totalAmountDOM;

            orderNoDom.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\D/g, ''); // Remove non-digits

                // Limit total input to 6 digits (2 for year, 4 for number)
                value = value.slice(0, 6);

                // Format as "25-0001"
                if (value.length > 2) {
                    value = value.slice(0, 2) + '-' + value.slice(2);
                }

                e.target.value = value;

                trackStateOfOrderNo(e.target.value);
            });

            orderNoDom.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    generateInvoiceBtn.click();
                }
            });

            generateInvoiceBtn.addEventListener('click', function () {
                getOrderDetails();
            });

            let orderNumber = @json($orderNumber);

            if (orderNumber) {
                orderNoDom.value = orderNumber;
                trackStateOfOrderNo(orderNoDom.value);
                getOrderDetails();
            }

            function getOrderDetails() {
                $.ajax({
                    url: "/get-order-details",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        order_no: orderNoDom.value
                    },
                    success: function (response) {
                        console.log(response);
                        
                        orderedArticles = response.articles;
                        discount = response.discount ?? 0;
                        customerData = response.customer;
                        
                        renderList();
                        renderCalcBottom();
                    }
                });
            }

            function trackStateOfOrderNo(value) {
                if (value != "") {
                    generateInvoiceBtn.disabled = false;
                } else {
                    generateInvoiceBtn.disabled = true;
                }
            }

            const articleListDOM = document.getElementById('article-list');

            function renderList() {
                if (orderedArticles && orderedArticles.length > 0) {
                    totalAmount = 0;
                    totalQuantityPcs = 0;

                    let clutter = "";
                    orderedArticles.forEach((selectedArticle, index) => {
                        if (selectedArticle.total_quantity_in_packets > 0) {
                            // let orderedQuantity = selectedArticle.ordered_quantity;
                            let totalQuantityInPackets = selectedArticle.total_quantity_in_packets;
                            // let totalPhysicalStockPcs = selectedArticle.total_quantity_in_packets * selectedArticle.article.pcs_per_packet;
                            
                            totalQuantityPcs += totalQuantityInPackets * selectedArticle.article.pcs_per_packet;

                            let articleAmount = (selectedArticle.article.sales_rate * selectedArticle.article.pcs_per_packet) * totalQuantityInPackets;

                            clutter += `
                                <div class="flex justify-between items-center border-t border-gray-600 py-3 px-4">
                                    <div class="w-[5%]">${index + 1}.</div>
                                    <div class="w-[11%]">#${selectedArticle.article.article_no}</div>
                                    <div class="w-[11%] pr-3">
                                        <input type="number" class="w-full border border-gray-600 bg-[var(--h-bg-color)] py-1 px-2 rounded-md focus:outline-none" value="${totalQuantityInPackets}" max="${totalQuantityInPackets}" onclick='this.select()' oninput="packetEdited(this)" />
                                    </div>
                                    <div class="w-[10%]">${formatNumbersDigitLess(totalQuantityInPackets * selectedArticle.article.pcs_per_packet)}</div>
                                    <div class="grow">${selectedArticle.description}</div>
                                    <div class="w-[8%]">${selectedArticle.article.pcs_per_packet}</div>
                                    <div class="w-[12%] text-right">${formatNumbersWithDigits(selectedArticle.article.sales_rate, 1, 1)}</div>
                                    <div class="w-[15%] text-right">${formatNumbersWithDigits(articleAmount, 1, 1)}</div>
                                    <div class="w-[15%] text-right">
                                        <button onclick="removeArticle(${index})" type="button" class="text-[var(--danger-color)] text-xs px-2 py-1 rounded-lg hover:text-[var(--h-danger-color)] transition-all duration-300 ease-in-out ${orderedArticles.length > 1 ? 'cursor-pointer' : 'cursor-not-allowed opacity-40'}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            `;

                            totalAmount += articleAmount;

                            selectedArticle.packets = totalQuantityInPackets
                            selectedArticle.ordered_quantity = totalQuantityInPackets * selectedArticle.article.pcs_per_packet
                        }
                    });

                    articleListDOM.innerHTML = clutter;
                } else {
                    articleListDOM.innerHTML =
                        `<div class="text-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4">No Orders Yet</div>`;
                }
            }
            renderList();

            function removeArticle(index) {
                if (orderedArticles.length > index && orderedArticles.length > 1) {
                    orderedArticles.splice(index, 1);
                    renderList();
                    renderCalcBottom();
                }
            }

            function updateInputArticlesInInvoice() {
                const articlesInInvoiceInpDom = document.getElementById("articles_in_invoice");
                let finalArticlesArray = orderedArticles.map(article => {
                    return {
                        id: article.article.id,
                        description: article.description,
                        invoice_quantity: article.ordered_quantity,
                    }
                })
                articlesInInvoiceInpDom.value = JSON.stringify(finalArticlesArray);
                console.log(finalArticlesArray);
            }

            function renderCalcBottom() {
                netAmount = totalAmount - (totalAmount * (discount / 100));
                totalQuantityInFormDom.textContent = formatNumbersDigitLess(totalQuantityPcs);
                totalAmountInFormDom.textContent = formatNumbersWithDigits(totalAmount, 1, 1);
                dicountInFormDom.textContent = discount;
                netAmountInFormDom.value = formatNumbersWithDigits(netAmount, 1, 1);
            }

            function packetEdited(elem) {
                let max = parseInt(elem.max);
                
                if (elem.value > max) {
                    elem.value = max;
                } else if (elem.value < 1) {
                    elem.value = 1;
                }

                elem.value = elem.value.replace(/\./g, '');

                calculateAndApplyChangesOnOrderArticle(elem);
            }

            function calculateAndApplyChangesOnOrderArticle(elem) {
                let childrenDom = elem.parentElement.parentElement.children;

                let packetsValue = parseInt(elem.value);

                let articleNoInRowDom = childrenDom[1];
                let pcsInRowDom = childrenDom[3];
                totalQuantityPcs -= parseInt(pcsInRowDom.textContent.replace(/[,]/g, ''));
                let pcsPerPktInRowDom = childrenDom[5];
                let ratePerPcInRowDom = childrenDom[6];

                let amountInRowDom = childrenDom[childrenDom.length - 2];
                totalAmount -= parseInt(amountInRowDom.textContent.replace(/[,]/g, ''));

                let pcsCalculated = packetsValue * parseInt(pcsPerPktInRowDom.textContent);
                totalQuantityPcs += pcsCalculated;

                pcsInRowDom.textContent = formatNumbersDigitLess(pcsCalculated) || 0;

                let amountCalculated = parseInt(pcsInRowDom.textContent.replace(/[,]/g, '')) * parseInt(ratePerPcInRowDom.textContent.replace(/[,]/g, ''));
                totalAmount += amountCalculated;
                
                amountInRowDom.textContent = formatNumbersWithDigits(amountCalculated, 1, 1) || 0.0;

                let currentArticle = orderedArticles.find(article => article.article.article_no == parseInt(articleNoInRowDom.textContent.replace(/#/g, '')))
                
                if (currentArticle) {
                    currentArticle.packets = packetsValue
                    currentArticle.ordered_quantity = pcsCalculated
                }
                
                renderCalcBottom();
            }

            let invoiceNo;
            let invoiceDate;
            const previewDom = document.getElementById('preview');

            function generateInvoiceNo() {
                const yearShort = String(new Date().getFullYear()).slice(-2); // e.g., "25"

                let lastInvoiceNo = lastInvoice?.invoice_no || `${yearShort}-0000`;

                // Extract numeric part after the dash
                let lastNumber = lastInvoiceNo.split('-')[1];
                const nextInvoiceNo = String(parseInt(lastNumber, 10) + 1).padStart(4, '0');

                return `${yearShort}-${nextInvoiceNo}`;
            }

            function getInvoiceDate() {
                const date = new Date();

                // Extract day, month, and year
                const day = String(date.getDate()).padStart(2, '0');
                const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are 0-based
                const year = date.getFullYear();
                const dayOfWeek = date.getDay(); // 0 = Sunday, 1 = Monday, ..., 6 = Saturday

                // Array of weekday names
                const weekDays = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];

                // Return the formatted date
                return `${day}-${month}-${year}, ${weekDays[dayOfWeek]}`;
            }

            function generateInvoice() {
                invoiceNo = generateInvoiceNo();
                invoiceDate = getInvoiceDate();
                
                if (orderedArticles.length > 0) {
                    previewDom.innerHTML = `
                        <div id="invoice" class="invoice flex flex-col h-full">
                            <div id="invoice-banner" class="invoice-banner w-full flex justify-between items-center mt-8 pl-5 pr-8">
                                <div class="left">
                                    <div class="invoice-logo">
                                        <img src="{{ asset('images/${companyData.logo}') }}" alt="Track Point"
                                            class="w-[12rem]" />
                                        <div class='mt-1'>${ companyData.phone_number }</div>
                                    </div>
                                </div>
                                <div class="left">
                                    <div class="invoice-logo">
                                        <h1 class="text-2xl font-medium text-[var(--h-primary-color)]">Sales Invoice</h1>
                                        <div class="space-y-1 mt-1">
                                            <div class="text-right leading-none">Order No.: ${orderNoDom.value}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr class="w-full my-3 border-black">
                            <div id="invoice-header" class="invoice-header w-full flex justify-between px-5">
                                <div class="left w-50 space-y-1">
                                    <div class="invoice-customer text-lg leading-none">M/s: ${customerData.customer_name}</div>
                                    <div class="invoice-person text-md text-lg leading-none">${customerData.urdu_title}</div>
                                    <div class="invoice-address text-md leading-none">${customerData.address}, ${customerData.city}</div>
                                    <div class="invoice-phone text-md leading-none">${customerData.phone_number}</div>
                                </div>
                                <div class="right my-auto pr-3 text-sm text-black space-y-1.5">
                                    <div class="invoice-date leading-none">Date: ${invoiceDate}</div>
                                    <div class="invoice-number leading-none">Invoice No.: ${invoiceNo}</div>
                                    <input type="hidden" name="invoice_no" value="${invoiceNo}">
                                    <div class="invoice-copy leading-none">Invoice Copy: Customer</div>
                                    <div class="invoice-copy leading-none">Document: Sales Invoice</div>
                                </div>
                            </div>
                            <hr class="w-full my-3 border-black">
                            <div id="invoice-body" class="invoice-body w-[95%] grow mx-auto">
                                <div class="invoice-table w-full">
                                    <div class="table w-full border border-black rounded-lg pb-2.5 overflow-hidden">
                                        <div class="thead w-full">
                                            <div class="tr flex justify-between w-full px-4 py-1.5 bg-[var(--primary-color)] text-white">
                                                <div class="th text-sm font-medium w-[7%]">S.No</div>
                                                <div class="th text-sm font-medium w-[10%]">Article</div>
                                                <div class="th text-sm font-medium w-[10%]">Packets</div>
                                                <div class="th text-sm font-medium w-[10%]">Pcs.</div>
                                                <div class="th text-sm font-medium grow">Description</div>
                                                <div class="th text-sm font-medium w-[10%]">Pcs/Pkt.</div>
                                                <div class="th text-sm font-medium w-[11%]">Rate/Pc.</div>
                                                <div class="th text-sm font-medium w-[11%]">Amount</div>
                                            </div>
                                        </div>
                                        <div id="tbody" class="tbody w-full">
                                            ${orderedArticles.map((articles, index) => {
                                                const hrClass = index === 0 ? "mb-2.5" : "my-2.5";

                                                return `
                                                    <div>
                                                        <hr class="w-full ${hrClass} border-black">
                                                        <div class="tr flex justify-between w-full px-4">
                                                            <div class="td text-sm font-semibold w-[7%]">${index + 1}.</div>
                                                            <div class="td text-sm font-semibold w-[10%]">#${articles.article.article_no}</div>
                                                            <div class="td text-sm font-semibold w-[10%]">${articles.shipment_quantity / articles.article.pcs_per_packet}</div>
                                                            <div class="td text-sm font-semibold w-[10%]">${articles.shipment_quantity}</div>
                                                            <div class="td text-sm font-semibold grow">${articles.description}</div>
                                                            <div class="td text-sm font-semibold w-[10%]">${formatNumbersDigitLess(articles.article.pcs_per_packet)}</div>
                                                            <div class="td text-sm font-semibold w-[11%]">${formatNumbersWithDigits(articles.article.sales_rate, 2, 2)}</div>
                                                            <div class="td text-sm font-semibold w-[11%]">${formatNumbersWithDigits(parseInt(articles.article.sales_rate) * articles.shipment_quantity, 1, 1)}</div>
                                                        </div>
                                                    </div>
                                                `;
                                            }).join('')}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr class="w-full my-3 border-black">
                            <div class="flex flex-col space-y-2">
                                <div id="invoice-total" class="tr flex justify-between w-full px-2 gap-2 text-sm">
                                    <div class="total flex justify-between items-center border border-black rounded-lg py-1.5 px-4 w-full">
                                        <div class="text-nowrap">Total Quantity - Pcs</div>
                                        <div class="w-1/2 text-right grow">${formatNumbersDigitLess(totalQuantityPcs)}</div>
                                    </div>
                                    <div class="total flex justify-between items-center border border-black rounded-lg py-1.5 px-4 w-full">
                                        <div class="text-nowrap">Gross Amount</div>
                                        <div class="w-1/2 text-right grow">${formatNumbersWithDigits(totalAmount, 1, 1)}</div>
                                    </div>
                                </div>
                                <div id="invoice-total" class="tr flex justify-between w-full px-2 gap-2 text-sm">
                                    <div class="total flex justify-between items-center border border-black rounded-lg py-1.5 px-4 w-full">
                                        <div class="text-nowrap">Discount</div>
                                        <div class="w-1/2 text-right grow">${formatNumbersDigitLess(discount)}</div>
                                    </div>
                                    <div
                                        class="total flex justify-between items-center border border-black rounded-lg py-1.5 px-4 w-full">
                                        <div class="text-nowrap">Net Amount</div>
                                        <div class="w-1/2 text-right grow">${formatNumbersWithDigits(netAmount, 1, 1)}</div>
                                    </div>
                                </div>
                            </div>
                            <hr class="w-full my-3 border-black">
                            <div class="tfooter flex w-full text-sm px-4 justify-between mb-4 text-black">
                                <P class="leading-none">${ companyData.name } | ${ companyData.address }</P>
                                <p class="leading-none text-sm">&copy; 2025 Spark Pair | +92 316 5825495</p>
                            </div>
                        </div>
                    `;
                } else {
                    previewDom.innerHTML = `
                        <h1 class="text-[var(--border-error)] font-medium text-center mt-5">No Preview avalaible.</h1>
                    `;
                }
            }

            function validateForNextStep() {
                generateInvoice()
                updateInputArticlesInInvoice();
                return true;
            }
            
            function addListenerToPrintAndSaveBtn() {
                document.getElementById('printAndSaveBtn').addEventListener('click', (e) => {
                    e.preventDefault();
                    closeAllDropdowns();
                    const preview = document.getElementById('preview-container'); // preview content

                    // Pehle se agar koi iframe hai to usko remove karein
                    let oldIframe = document.getElementById('printIframe');
                    if (oldIframe) {
                        oldIframe.remove();
                    }

                    // Naya iframe banayein
                    let printIframe = document.createElement('iframe');
                    printIframe.id = "printIframe";
                    printIframe.style.position = "absolute";
                    printIframe.style.width = "0px";
                    printIframe.style.height = "0px";
                    printIframe.style.border = "none";
                    printIframe.style.display = "none"; // ✅ Hide iframe

                    // Iframe ko body me add karein
                    document.body.appendChild(printIframe);

                    let printDocument = printIframe.contentDocument || printIframe.contentWindow.document;
                    printDocument.open();

                    // ✅ Current page ke CSS styles bhi iframe me inject karenge
                    const headContent = document.head.innerHTML;

                    printDocument.write(`
                        <html>
                            <head>
                                <title>Print Invoice</title>
                                ${headContent} <!-- Copy current styles -->
                                <style>
                                    @media print {

                                        body {
                                            margin: 0;
                                            padding: 0;
                                            width: 210mm; /* A4 width */
                                            height: 297mm; /* A4 height */
                                            
                                        }

                                        .preview-container, .preview-container * {
                                            page-break-inside: avoid;
                                        }
                                    }
                                </style> 
                            </head>
                            <body>
                                <div class="preview-container pt-3">${preview.innerHTML}</div> <!-- Add the preview content, only innerHTML -->
                                <div id="preview-container" class="preview-container pt-3">${preview.innerHTML}</div> <!-- Add the preview content, only innerHTML -->
                            </body>
                        </html>
                    `);

                    printDocument.close();

                    // Wait for iframe to load and print
                    printIframe.onload = () => {
                        let orderCopy = printDocument.querySelector('#preview-container .invoice-copy');
                        if (orderCopy) {
                            orderCopy.textContent = "Invoice Copy: Office";
                        }

                        // Listen for after print in the iframe's window
                        printIframe.contentWindow.onafterprint = () => {
                            document.getElementById('form').submit();
                        };

                        setTimeout(() => {
                            printIframe.contentWindow.focus();
                            printIframe.contentWindow.print();
                        }, 1000);
                    };
                });
            }
            
            document.addEventListener("DOMContentLoaded", ()=>{
                addListenerToPrintAndSaveBtn();
            });
        </script>
    @endif
@endsection
