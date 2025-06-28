@extends('app')
@section('title', 'Generate Order | ' . app('company')->name)
@section('content')
    <!-- Modal -->
    <div id="articleModal"
        class="hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-[var(--overlay-color)] fade-in">
        <x-modal id="articlesModalForm" classForBody="p-5 max-w-7xl h-[45rem]" closeAction="closeArticlesModal">
            <!-- Modal Content Slot -->
            <div class="flex items-start relative h-full">
                <div class="flex-1 h-full overflow-y-auto my-scrollbar-2 flex flex-col">
                    <x-search-header heading="Articles" :filter_items="[
                        'all' => 'All',
                        '#' => 'Article No.',
                        'category' => 'Category',
                        'season' => 'Season',
                        'size' => 'Size',
                    ]"/>
        
                    @if (count($articles) > 0)
                        <div class='overflow-y-auto my-scrollbar-2 pt-2 grow'>
                            <div class="card_container grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                                @foreach ($articles as $article)
                                    <div data-json='{{ $article }}' id='{{ $article->id }}' onclick='generateQuantityModal(this)'
                                        class="contextMenuToggle modalToggle card relative border border-gray-600 shadow rounded-xl min-w-[100px] h-[8rem] flex gap-4 p-2 cursor-pointer overflow-hidden fade-in">
                                        <x-card :data="[
                                            'image' => $article->image == 'no_image_icon.png' 
                                                ? asset('images/no_image_icon.png') 
                                                : asset('storage/uploads/images/' . $article->image),
                                            'classImg' => $article->image == 'no_image_icon.png' ? 'p-2' : 'rounded-md',
                                            'name' => '#' . $article->article_no,
                                            'details' => [
                                                'Season' => $article->season,
                                                'Size' => $article->size,
                                                'Category' => $article->category,
                                            ],
                                        ]" />
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="text-[var(--border-error)] text-center h-full">Article Not Found</div>
                    @endif
                    
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
                <button onclick="closeArticlesModal()" type="button"
                    class="px-4 py-2 bg-[var(--secondary-bg-color)] border border-gray-600 text-[var(--secondary-text)] rounded-lg hover:bg-[var(--h-bg-color)] transition-all duration-300 ease-in-out cursor-pointer hover:scale-[0.95]">
                    Close
                </button>
            </x-slot>
        </x-modal>
    </div>

    <div id="quantityModal"
        class="hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-[var(--overlay-color)] fade-in">
    </div>
    <!-- Main Content -->
    <!-- Progress Bar -->
    <div class="mb-5 max-w-4xl mx-auto">
        <x-search-header heading="Generate Order" link linkText="Show Orders" linkHref="{{ route('orders.index') }}"/>
        <x-progress-bar :steps="['Generate Order', 'Preview']" :currentStep="1" />
    </div>

    <!-- Form -->
    <form id="form" action="{{ route('orders.store') }}" method="post" enctype="multipart/form-data"
        class="bg-[var(--secondary-bg-color)] text-sm rounded-xl shadow-lg p-8 border border-[var(--h-bg-color)] pt-12 max-w-4xl mx-auto  relative overflow-hidden">
        @csrf
        <x-form-title-bar title="Generate Order" />

        <!-- Step 1: Generate order -->
        <div class="step1 space-y-4 ">
            <div class="flex justify-between gap-4">
                {{-- order date --}}
                <div class="w-1/3">
                    <x-input label="Date" name="date" id="date" type="date" onchange="getDataByDate(this)" validateMax max='{{ now()->toDateString() }}' validateMin min="{{ now()->subDays(4)->toDateString() }}" required />
                </div>
                
                <input type="hidden" name="generateInvoiceAfterSave" id="generateInvoiceAfterSave" value="0">

                {{-- title --}}
                <div class="grow">
                    <x-select label="Customer" name="customer_id" id="customer_id" :options="$customers_options" searchable required showDefault
                        class="grow" withButton btnId="generateOrderBtn" btnText="Select Articles" />
                </div>
            </div>
            {{-- rate showing --}}
            <div id="order-table" class="w-full text-left text-sm">
                <div class="flex justify-between items-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4 mb-4">
                    <div class="w-[10%]">#</div>
                    <div class="w-1/6">Qty.</div>
                    <div class="grow">Decs.</div>
                    <div class="w-1/6">Rate/Pc</div>
                    <div class="w-1/5">Amount</div>
                    <div class="w-[10%] text-center">Action</div>
                </div>
                <div id="order-list" class="h-[20rem] overflow-y-auto my-scrollbar-2">
                    <div class="text-center bg-[var(--h-bg-color)] rounded-lg py-3 px-4">No Rates Added</div>
                </div>
            </div>

            <div class="flex w-full grid grid-cols-1 md:grid-cols-3 gap-3 text-sm mt-5 text-nowrap">
                <div class="total-qty flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                    <div class="grow">Total Quantity - Pcs</div>
                    <div id="finalOrderedQuantity">0</div>
                </div>
                <div class="final flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                    <div class="grow">Total Amount - Rs.</div>
                    <div id="finalOrderAmount">0.0</div>
                </div>
                <div class="final flex justify-between items-center bg-[var(--h-bg-color)] border border-gray-600 rounded-lg py-2 px-4 w-full">
                    <label for="discount" class="grow">Discount - %</label>
                    <input type="text" name="discount" id="discount" value="10" min="0" max="100"
                        class="text-right bg-transparent outline-none w-1/2 border-none" />
                </div>
                <div class="total-qty flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                    <div class="grow">Previous Balance - Rs.</div>
                    <div id="finalPreviousBalance">0</div>
                </div>
                <div class="final flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                    <div class="grow">Net Amount - Rs.</div>
                    <input type="text" name="netAmount" id="finalNetAmount" value="0.0" readonly
                        class="text-right bg-transparent outline-none w-1/2 border-none" />
                </div>
                <div class="final flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                    <div class="grow">Current Balance - Rs.</div>
                    <div id="finalCurrentBalance">0.0</div>
                </div>
            </div>
            <input type="hidden" name="articles" id="articles" value="" />
        </div>

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
        let selectedArticles = [];
        let totalOrderedQuantity = 0;
        let totalOrderAmount = 0;
        let netAmount = 0;

        const lastOrder = @json($last_order);
        let customerData;
        const articleModalDom = document.getElementById("articleModal");
        const quantityModalDom = document.getElementById("quantityModal");
        const customerSelectDom = document.getElementById("customer_id");
        const generateOrderBtn = document.getElementById("generateOrderBtn");
        generateOrderBtn.disabled = true;

        let totalQuantityDOM;
        let totalAmountDOM;

        let isModalOpened = false;
        let isQuantityModalOpened = false;

        customerSelectDom.addEventListener("change", (e) => {
            let customerDataDom = customerSelectDom.options[customerSelectDom.selectedIndex].getAttribute('data-option');
            customerData = JSON.parse(customerDataDom);
            selectedArticles = [];
            totalOrderedQuantity = 0;
            totalOrderAmount = 0;
            netAmount = 0;
            renderList();
            generateOrder();
            renderFinals();
            trackStateOfCategoryBtn(e.target.value);
        })

        function trackStateOfCategoryBtn(value) {
            if (value != "") {
                generateOrderBtn.disabled = false;
            } else {
                generateOrderBtn.disabled = true;
            }
        }

        generateOrderBtn.addEventListener('click', () => {
            generateArticlesModal();
        })

        function generateArticlesModal() {
            openArticlesModal();
            setDropdownListeners();

            document.querySelectorAll('.card .quantity-label').forEach(previousQuantityLabel => {
                previousQuantityLabel.remove();
            });

            if (selectedArticles.length > 0) {
                selectedArticles.forEach(selectedArticle => {
                    let card = document.getElementById(selectedArticle.id);
                    let quantityLabelDom = card.querySelector('.quantity-label');
                    if (!quantityLabelDom) {
                        card.innerHTML += `
                            <div
                                class="quantity-label absolute text-xs text-[var(--border-success)] top-1 right-2 h-[1rem]">
                                ${selectedArticle.orderedQuantity} Pcs
                            </div>
                        `;
                    } else {
                        quantityLabelDom.textContent = `${selectedArticle.orderedQuantity} Pcs`;
                    }
                });
            }

            totalQuantityDOM = document.getElementById('totalOrderedQty');
            totalAmountDOM = document.getElementById('totalOrderAmount');

            renderTotals();
        }

        function openArticlesModal() {
            isModalOpened = true;
            closeAllDropdowns();
            document.getElementById('articleModal').classList.remove('hidden');
        }

        function closeArticlesModal() {
            generateDecription();
            renderList();
            generateOrder();
            renderFinals();

            isModalOpened = false;
            let modal = document.getElementById('articleModal');
            modal.classList.add('fade-out');

            modal.addEventListener('animationend', () => {
                modal.classList.add('hidden');
                modal.classList.remove('fade-out');
            }, {
                once: true
            });
        }

        function generateQuantityModal(elem) {
            let data = JSON.parse(elem.dataset.json);
            let physicalQuantity = 0;

            const physicalQuantityInpDom = document.getElementById('physical_quantity');
            const dateInpDom = document.getElementById('date');

            quantityModalDom.innerHTML = `
                <x-modal id="quantityModalForm" classForBody="p-5" closeAction="closeQuantityModal">
                    <!-- Modal Content Slot -->
                    <div class="flex items-start relative h-full">
                        <div class="flex-1 h-full overflow-y-auto my-scrollbar-2">
                            <h5 id="name" class="text-2xl my-1 text-[var(--text-color)] capitalize font-semibold">Article Details</h5>

                            <x-input 
                                value="${data.article_no} | ${data.season} | ${data.size} | ${data.category} | ${data.fabric_type} | ${formatNumbersDigitLess(data.quantity)} | ${formatNumbersWithDigits(data.sales_rate, 1, 1)} - Rs." 
                                disabled
                            />

                            <hr class="border-gray-600 mt-3">

                            <div class="w-2/3 mx-auto p-5 flex flex-col gap-4">
                                <x-input 
                                    label="Current Stock - Pcs."
                                    value="${formatNumbersDigitLess(data.quantity - data.ordered_quantity)}" 
                                    disabled
                                />
                                
                                <x-input 
                                    label="Physical Stock - Pcs."
                                    id="physical_quantity"
                                    value="${formatNumbersDigitLess(data.physical_quantity)}" 
                                    disabled
                                />
                                
                                <x-input 
                                    label="Quantity - Pcs."
                                    name="quantity" 
                                    id="quantity" 
                                    type="text" 
                                    placeholder="Enter quantity in pcs." 
                                    required
                                    validateMax
                                    max="${data.quantity - data.ordered_quantity}"
                                    oninput="checkMax(this)"
                                />
                            </div>
                        </div>
                    </div>
                
                    <!-- Modal Action Slot -->
                    <x-slot name="actions">
                        <button onclick="closeQuantityModal()" type="button"
                            class="px-4 py-2 bg-[var(--secondary-bg-color)] border border-gray-600 text-[var(--secondary-text)] rounded-lg hover:bg-[var(--h-bg-color)] transition-all duration-300 ease-in-out cursor-pointer hover:scale-[0.95]">
                            Cancel
                        </button>
                        <button type="button" id="setQuantityBtn" onclick="setQuantity(${data.id})"
                            class="px-5 py-2 bg-[var(--bg-success)] border border-[var(--bg-success)] text-[var(--text-success)] font-medium text-nowrap rounded-lg hover:bg-[var(--h-bg-success)] transition-all duration-300 ease-in-out cursor-pointer hover:scale-[0.95]">
                            Set Quantity
                        </button>
                    </x-slot>
                </x-modal>
            `;

            let quantityLabel = elem.querySelector('.quantity-label');

            if (quantityLabel) {
                document.getElementById("quantity").value = parseInt(quantityLabel.textContent.replace(/\D/g, ""));
            }

            openQuantityModal();
            document.getElementById("quantity").focus();
            document.getElementById("quantity").addEventListener('keydown', (e) => {
                if (e.key == 'Enter') {
                    document.getElementById("setQuantityBtn").click();
                }
            })
        }

        function openQuantityModal() {
            isQuantityModalOpened = true;
            closeAllDropdowns();
            document.getElementById('quantityModal').classList.remove('hidden');
        }

        function closeQuantityModal() {
            isQuantityModalOpened = false;
            let modal = document.getElementById('quantityModal');
            modal.classList.add('fade-out');

            modal.addEventListener('animationend', () => {
                modal.classList.add('hidden');
                modal.classList.remove('fade-out');
            }, {
                once: true
            });
        }

        document.addEventListener('mousedown', (e) => {
            const {
                id
            } = e.target;
            if (id === 'articlesModalForm') {
                closeArticlesModal();
            } else if (id === 'quantityModalForm') {
                closeQuantityModal();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && isModalOpened) {
                closeArticlesModal();
                closeQuantityModal();
            }
        })

        function setQuantity(cardId) {
            let targetCard = document.getElementById(cardId);
            let cardData = JSON.parse(targetCard.dataset.json);
            let alreadySelectedArticle = selectedArticles.filter(c => c.id == cardData.id);
            let quantityInputDOM = document.getElementById("quantity");

            let quantity = quantityInputDOM.value;

            let quantityLabel = targetCard.querySelector('.quantity-label');

            if (quantity > 0) {
                if (quantityLabel) {
                    quantityLabel.textContent = `${quantity} Pcs`;
                } else {
                    targetCard.innerHTML += `
                        <div
                            class="quantity-label absolute text-xs text-[var(--border-success)] top-1 right-2 h-[1rem]">
                            ${quantity} Pcs
                        </div>
                    `;
                }
            } else {
                if (quantityLabel) {
                    quantityLabel.remove();
                    const index = selectedArticles.findIndex(c => c.id === cardData.id);
                    deselectArticleAtIndex(index);
                }
            }

            cardData.orderedQuantity = parseInt(quantity);

            if (alreadySelectedArticle.length > 0) {
                alreadySelectedArticle[0].orderedQuantity = parseInt(quantity);
            } else {
                selectedArticles.push(cardData);
            }

            calculateTotalOrderedQuantity();
            calculateTotalOrderAmount();
            calculateNetAmount();
            renderTotals();
            closeQuantityModal();

            console.log(selectedArticles);
        }

        function deselectArticleAtIndex(index) {
            if (index !== -1) {
                selectedArticles.splice(index, 1);
            }
        }

        function deselectThisArticle(index) {
            deselectArticleAtIndex(index);

            console.log(selectedArticles);
            

            renderList();
            generateOrder();

            calculateTotalOrderedQuantity();
            calculateTotalOrderAmount();
            calculateNetAmount();

            renderFinals();
            renderTotals();
        }

        const finalOrderedQuantity = document.getElementById('finalOrderedQuantity');
        const finalOrderAmount = document.getElementById('finalOrderAmount');
        const discountDOM = document.getElementById('discount');
        const finalPreviousBalance = document.getElementById('finalPreviousBalance');
        const finalNetAmount = document.getElementById('finalNetAmount');
        const finalCurrentBalance = document.getElementById('finalCurrentBalance');

        function calculateTotalOrderedQuantity() {
            totalOrderedQuantity = 0;

            selectedArticles.forEach(selectedArticle => {
                totalOrderedQuantity += selectedArticle.orderedQuantity;
            });

            totalOrderedQuantity = new Intl.NumberFormat('en-US').format(totalOrderedQuantity);
        }

        function calculateTotalOrderAmount() {
            totalOrderAmount = 0;

            selectedArticles.forEach(selectedArticle => {
                totalOrderAmount += selectedArticle.orderedQuantity * selectedArticle.sales_rate;
            });

            totalOrderAmount = new Intl.NumberFormat('en-US', {
                minimumFractionDigits: 1,
                maximumFractionDigits: 1
            }).format(totalOrderAmount);
        }

        function generateDecription() {
            selectedArticles.forEach((selectedArticle, index) => {
                selectedArticle.description = `${selectedArticle.size} | ${selectedArticle.category} | ${selectedArticle.season}`;
            });
        }

        function calculateNetAmount() {
            let totalAmount = parseFloat(totalOrderAmount.replace(/,/g, ''));
            let discount = document.getElementById('discount').value;
            let discountAmount = totalAmount - (totalAmount * (discount / 100));
            netAmount = discountAmount;
            netAmount = new Intl.NumberFormat('en-US', {
                minimumFractionDigits: 1,
                maximumFractionDigits: 1
            }).format(netAmount);
            renderFinals();
        }

        discountDOM.addEventListener('input', calculateNetAmount);

        discountDOM.addEventListener('focus', (e) => {
            e.target.select();
        });

        function renderTotals() {
            totalQuantityDOM.textContent = totalOrderedQuantity;
            totalAmountDOM.textContent = totalOrderAmount;
        }

        const orderListDOM = document.getElementById('order-list');

        function renderList() {
            if (selectedArticles.length > 0) {
                let clutter = "";
                selectedArticles.forEach((selectedArticle, index) => {
                    clutter += `
                        <div class="flex justify-between items-center border-t border-gray-600 py-3 px-4">
                            <div class="w-[10%]">${selectedArticle.article_no}</div>
                            <div class="w-1/6">${selectedArticle.orderedQuantity} pcs</div>
                            <div class="grow">${selectedArticle.description}</div>
                            <div class="w-1/6">${selectedArticle.sales_rate}</div>
                            <div class="w-1/5">${formatNumbersWithDigits(selectedArticle.sales_rate * selectedArticle.orderedQuantity, 1, 1)}</div>
                            <div class="w-[10%] text-center">
                                <button onclick="deselectThisArticle(${index})" type="button" class="text-[var(--danger-color)] text-xs px-2 py-1 rounded-lg hover:text-[var(--h-danger-color)] transition-all duration-300 ease-in-out cursor-pointer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    `;
                });

                orderListDOM.innerHTML = clutter;
            } else {
                orderListDOM.innerHTML =
                    `<div class="text-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4">No Articles Yet</div>`;
            }
            updateInputOrderedArticles();
        }
        renderList();

        function renderFinals() {
            finalOrderedQuantity.textContent = totalOrderedQuantity;
            finalOrderAmount.textContent = totalOrderAmount;
            finalPreviousBalance.textContent = formatNumbersWithDigits(customerData.balance, 1, 1); 
            finalNetAmount.value = netAmount;
            finalCurrentBalance.textContent = formatNumbersWithDigits(customerData.balance + parseFloat(finalNetAmount.value.replace(/,/g, '')), 1, 1);
        }

        function updateInputOrderedArticles() {
            let inputOrderedArticles = document.getElementById('articles');
            let finalArticlesArray = selectedArticles.map(article => {
                return {
                    id: article.id,
                    description: article.description,
                    ordered_quantity: article.orderedQuantity
                }
            });
            inputOrderedArticles.value = JSON.stringify(finalArticlesArray);
        }

        let companyData = @json(app('company'));
        let orderNo;
        let orderDate;
        const previewDom = document.getElementById('preview');

        function generateOrderNo() {
            const yearShort = String(new Date().getFullYear()).slice(-2); // e.g., "25"

            let lastOrderNo = lastOrder?.order_no || `${yearShort}-0000`;

            // Extract numeric part after the dash
            let lastNumber = lastOrderNo.split('-')[1];
            const nextOrderNo = String(parseInt(lastNumber, 10) + 1).padStart(4, '0');

            return `${yearShort}-${nextOrderNo}`;
        }

        function getOrderDate() {
            const dateDom = document.getElementById('date').value;
            const date = new Date(dateDom);

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

        function generateOrder() {
            orderNo = generateOrderNo();
            orderDate = getOrderDate();
            
            if (selectedArticles.length > 0) {
                previewDom.innerHTML = `
                    <div id="order" class="order flex flex-col h-full">
                        <div id="order-banner" class="order-banner w-full flex justify-between items-center mt-8 px-5">
                            <div class="left">
                                <div class="order-logo">
                                    <img src="{{ asset('images/${companyData.logo}') }}" alt="Track Point"
                                        class="w-[12rem]" />
                                    <div class='mt-1'>${ companyData.phone_number }</div>
                                </div>
                            </div>
                            <h1 class="text-2xl font-medium text-[var(--primary-color)]">Sales Order</h1>
                        </div>
                        <hr class="w-full my-3 border-gray-600">
                        <div id="order-header" class="order-header w-full flex justify-between px-5">
                            <div class="left w-50 space-y-1">
                                <div class="order-customer text-lg leading-none">M/s: ${customerData.customer_name}</div>
                                <div class="order-person text-md text-lg leading-none">${customerData.urdu_title}</div>
                                <div class="order-address text-md leading-none">${customerData.address}, ${customerData.city}</div>
                                <div class="order-phone text-md leading-none">${customerData.phone_number}</div>
                            </div>
                            <div class="right w-50 my-auto text-right text-sm text-gray-600 space-y-1.5">
                                <div class="order-date leading-none">Date: ${orderDate}</div>
                                <div class="order-number leading-none">Order No.: ${orderNo}</div>
                                <input type="hidden" name="order_no" value="${orderNo}" />
                                <div class="order-copy leading-none">Order Copy: Customer</div>
                                <div class="order-copy leading-none">Document: Sales Order</div>
                            </div>
                        </div>
                        <hr class="w-full my-3 border-gray-600">
                        <div id="order-body" class="order-body w-[95%] grow mx-auto">
                            <div class="order-table w-full">
                                <div class="table w-full border border-gray-600 rounded-lg pb-2.5 overflow-hidden">
                                    <div class="thead w-full">
                                        <div class="tr flex justify-between w-full px-4 py-1.5 bg-[var(--primary-color)] text-white">
                                            <div class="th text-sm font-medium w-[7%]">S.No</div>
                                            <div class="th text-sm font-medium w-[13%]">Article</div>
                                            <div class="th text-sm font-medium grow">Description</div>
                                            <div class="th text-sm font-medium w-[10%]">Pcs.</div>
                                            <div class="th text-sm font-medium w-[10%]">Packets</div>
                                            <div class="th text-sm font-medium w-[10%]">Rate</div>
                                            <div class="th text-sm font-medium w-[10%]">Amount</div>
                                            <div class="th text-sm font-medium w-[8%]">Dispatch</div>
                                        </div>
                                    </div>
                                    <div id="tbody" class="tbody w-full">
                                        ${selectedArticles.map((article, index) => {
                                            const hrClass = index === 0 ? "mb-2.5" : "my-2.5";
                                                return `
                                                    <div>
                                                        <hr class="w-full ${hrClass} border-gray-600">
                                                        <div class="tr flex justify-between w-full px-4">
                                                            <div class="td text-sm font-semibold w-[7%]">${index + 1}.</div>
                                                            <div class="td text-sm font-semibold w-[13%]">${article.article_no}</div>
                                                            <div class="td text-sm font-semibold grow">${article.description}</div>
                                                            <div class="td text-sm font-semibold w-[10%]">${article.orderedQuantity}</div>
                                                            <div class="td text-sm font-semibold w-[10%]">${article?.pcs_per_packet ? Math.floor(article.orderedQuantity / article.pcs_per_packet) : 0}</div>
                                                            <div class="td text-sm font-semibold w-[10%]">
                                                                ${new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(article.sales_rate)}
                                                            </div>
                                                            <div class="td text-sm font-semibold w-[10%]">
                                                                ${new Intl.NumberFormat('en-US', { minimumFractionDigits: 1, maximumFractionDigits: 1 }).format(parseInt(article.sales_rate) * article.orderedQuantity)}
                                                            </div>
                                                            <div class="td text-sm font-semibold w-[8%]"></div>
                                                        </div>
                                                    </div>
                                                    `;
                                        }).join('')}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr class="w-full my-3 border-gray-600">
                        <div class="flex flex-col space-y-2">
                            <div id="order-total" class="tr flex justify-between w-full px-2 gap-2 text-sm">
                                <div class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                                    <div class="text-nowrap">Total Quantity - Pcs</div>
                                    <div class="w-1/4 text-right grow">${totalQuantityDOM.textContent}</div>
                                </div>
                                <div class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                                    <div class="text-nowrap">Total Amount</div>
                                    <div class="w-1/4 text-right grow">${totalAmountDOM.textContent}</div>
                                </div>
                                <div class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                                    <div class="text-nowrap">Discount - %</div>
                                    <div class="w-1/4 text-right grow">${discountDOM.value}</div>
                                </div>
                            </div>
                            <div id="order-total" class="tr flex justify-between w-full px-2 gap-2 text-sm">
                                <div class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                                    <div class="text-nowrap">Previous Balance</div>
                                    <div class="w-1/4 text-right grow">${finalPreviousBalance.textContent}</div>
                                </div>
                                <div
                                    class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                                    <div class="text-nowrap">Net Amount</div>
                                    <div class="w-1/4 text-right grow">${finalNetAmount.value}</div>
                                </div>
                                <div
                                    class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                                    <div class="text-nowrap">Current Balance</div>
                                    <div class="w-1/4 text-right grow">${finalCurrentBalance.textContent}</div>
                                </div>
                            </div>
                        </div>
                        <hr class="w-full my-3 border-gray-600">
                        <div class="tfooter flex w-full text-sm px-4 justify-between mb-4 text-gray-600">
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
        
        let cardsDom;
        let cardsDataArray = [];

        function getDataByDate(inputElem) {
            $.ajax({
                url: '{{ route('orders.create') }}',
                method: 'GET',
                data: {
                    date: inputElem.value,
                },
                success: function(response) {
                    const customerSelectDom = document.getElementById('customer_id');
                    if (customerSelectDom.value == "") {
                        populateOptions(response);
                    }

                    populateCards(response);
                },
                error: function() {
                    alert('Error submitting form');
                }
            });
        }

        // getDataByDate(document.getElementById('date'));

        function populateOptions(response) {
            const selectElem = $(response).find('#customer_id')[0]; // Get the raw DOM element

            const customerSelectDom = document.getElementById('customer_id');

            // CASE 1: If it's a <select> tag
            if (selectElem && selectElem.tagName.toLowerCase() === 'select') {
                const customersOptions = selectElem.innerHTML;

                if (customersOptions && customersOptions.trim() !== "") {
                    customerSelectDom.innerHTML = customersOptions;
                    customerSelectDom.disabled = false;
                } else {
                    customerSelectDom.disabled = true;
                }

            // CASE 2: If it's an <input> tag with a .optionsDropdown
            } else if (selectElem && selectElem.tagName.toLowerCase() === 'input') {
                const optionsDropdown = $(response).find('.optionsDropdown').html();

                const dropdownUl = customerSelectDom
                    .closest('.form-group') // adjust if needed based on your structure
                    .parentElement
                    .querySelector('.optionsDropdown');

                if (optionsDropdown && optionsDropdown.trim() !== "") {
                    dropdownUl.innerHTML = optionsDropdown;
                    customerSelectDom.disabled = false;
                } else {
                    customerSelectDom.disabled = true;
                }

                selectFirstOption('customer_id');
            }
        }

        function populateCards(response) {
            const articleModal = $(response).find('#articleModal').html();
            const articleModalDom = document.getElementById('articleModal');

            if (articleModal !== undefined && articleModal.trim() !== "") {
                articleModalDom.innerHTML = articleModal;

                cardsDom = $(articleModal).find('.card_container').children().toArray();

                cardsDom.forEach((card) => {
                    cardsDataArray.push(JSON.parse(card.dataset.json));
                })

                // setFilter('all');
            }

            if (selectedArticles.length > 0) {
                reRenderSelectedState();
                reRenderSelectedStateTotal();
            }
        }

        // let filterType;

        // function setFilter(filterTypeArg) {
        //     filterType = filterTypeArg;

        //     searchData(document.getElementById('search_box').value);
        // }

        // function searchData(search) {
        //     search = search.toLowerCase();

        //     const filteredData = cardsDataArray.filter(item => {
        //         switch (filterType) {
        //             case 'all':
        //                 return (
        //                     item.article_no.toString().includes(search) ||
        //                     item.category.toLowerCase().includes(search) ||
        //                     item.season.toLowerCase().includes(search) ||
        //                     item.size.toLowerCase().includes(search)
        //                 );
        //                 break;
                        
        //             case '#':
        //                 return (
        //                     item.article_no.toString().includes(search)
        //                 );
        //                 break;
                        
        //             case 'category':
        //                 return (
        //                     item.category.toLowerCase().includes(search)
        //                 );
        //                 break;
                        
        //             case 'season':
        //                 return (
        //                     item.season.toLowerCase().includes(search)
        //                 );
        //                 break;
                        
        //             case 'size':
        //                 return (
        //                     item.size.toLowerCase().includes(search)
        //                 );
        //                 break;
                
        //             default:
        //                 return (
        //                     item.article_no.toString().includes(search) ||
        //                     item.category.toLowerCase().includes(search) ||
        //                     item.season.toLowerCase().includes(search) ||
        //                     item.size.toLowerCase().includes(search)
        //                 );
        //                 break;
        //         }
        //     });

        //     const cardContainerDom = document.querySelector('.card_container');
        //     cardContainerDom.innerHTML = "";

        //     if (filteredData.length === 0) {
        //         // Show "No articles found" message if no results
        //         const noResultMessage = "<p class='text-center col-span-full text-[var(--border-error)]'>No articles found</p>"
        //         cardContainerDom.innerHTML = noResultMessage;
        //     } else {
        //         filteredData.forEach(item => {
        //             const cardElement = cardsDom.find(card => card.id == item.id);
        //             if (cardElement) {
        //                 cardContainerDom.appendChild(cardElement);
        //             }
        //         });
        //     }
        // }

        function validateForNextStep() {
            generateOrder()
            return true;
        }

        function reRenderSelectedState() {
            const selectedIds = selectedArticles.map(card => card.id);

            // Loop through all rendered cards
            document.querySelectorAll('.card_container .card').forEach(card => {
                const cardData = JSON.parse(card.getAttribute('data-json'));

                if (selectedIds.includes(cardData.id)) {
                    // Find the corresponding selected article
                    const selectedCard = selectedArticles.find(item => item.id === cardData.id);

                    // Add new label with ordered quantity
                    card.innerHTML += `
                        <div class="quantity-label absolute text-xs text-[var(--border-success)] top-1 right-2 h-[1rem]">
                            ${selectedCard.orderedQuantity} Pcs
                        </div>
                    `;
                }
            });
        }

        function reRenderSelectedStateTotal() {
            console.log(totalOrderedQuantity);
            
            totalQuantityDOM = document.getElementById('totalOrderedQty');
            totalAmountDOM = document.getElementById('totalOrderAmount');
            renderTotals();
        }

        document.addEventListener('DOMContentLoaded', function () {
            function addListenerToQuickInvoiceBtn() {
                const quickInvoiceBtn = document.getElementById('quickInvoiceBtn');
                quickInvoiceBtn.addEventListener('click', function () {
                    document.getElementById('generateInvoiceAfterSave').value = 1;
                    document.getElementById('form').submit();
                });
            }
            addListenerToQuickInvoiceBtn();
        });

    </script>
@endsection
