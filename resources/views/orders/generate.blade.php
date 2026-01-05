@extends('app')
@section('title', 'Generate Order | ' . $client_company->name)
@section('content')
    <!-- Main Content -->
    <!-- Progress Bar -->
    <div class="mb-5 max-w-4xl mx-auto">
        <x-search-header heading="Generate Order" link linkText="Show Orders" linkHref="{{ route('orders.index') }}"/>
        <x-progress-bar :steps="['Generate Order', 'Preview']" :currentStep="1" />
    </div>

    <!-- Form -->
    <form id="form" action="{{ route('orders.store') }}" method="post" enctype="multipart/form-data"
        class="bg-[var(--secondary-bg-color)] text-sm rounded-xl shadow-lg p-8 border border-[var(--glass-border-color)]/20 pt-14 max-w-4xl mx-auto  relative overflow-hidden">
        @csrf
        <x-form-title-bar title="Generate Order" />

        <!-- Step 1: Generate order -->
        <div class="step1 space-y-4 ">
            <div class="flex justify-between gap-4">
                {{-- order date --}}
                <div class="w-1/3">
                    <x-input label="Date" name="date" id="date" type="date" onchange="getDataByDate(this)" validateMax max='{{ now()->toDateString() }}' validateMin min="2024-01-01" required />
                </div>

                <input type="hidden" name="generateInvoiceAfterSave" id="generateInvoiceAfterSave" value="0">

                {{-- title --}}
                <div class="grow">
                    <x-select label="Customer" name="customer_id" id="customer_id" :options="[]" required showDefault onchange="trackCustomerState(this)"
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

            <div class="flex w-full grid grid-cols-1 md:grid-cols-2 gap-3 text-sm mt-5 text-nowrap">
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
                <div class="final flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                    <div class="grow">Net Amount - Rs.</div>
                    <input type="text" name="netAmount" id="finalNetAmount" value="0.0" readonly
                        class="text-right bg-transparent outline-none w-1/2 border-none" />
                </div>
            </div>
            <input type="hidden" name="articles" id="articles" value="" />
        </div>

        <!-- Step 2: view order -->
        <div class="step2 hidden space-y-4 text-black h-[35rem] overflow-y-auto my-scrollbar-2 bg-white rounded-md py-10">
            <div id="preview-container" class="w-[208mm] h-[300mm] mx-auto overflow-hidden relative ">
                <div id="preview" class="preview w-[208mm] h-[300mm] overflow-hidden flex flex-col">
                    <h1 class="text-[var(--border-error)] font-medium text-center mt-5">No Preview avalaible.</h1>
                </div>
            </div>
        </div>
    </form>

    <script>
        let maxLimitOfArticles = 500;
        let limitOfArticles = 500;
        let selectedArticles = [];
        let totalOrderedQuantity = 0;
        let totalOrderAmount = 0;
        let netAmount = 0;
        let articles;

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

        function isArticleAlreadySelected(articleId) {
            return selectedArticles.some(a => a.id == articleId);
        }

        function trackCustomerState(elem) {
            if (elem.value != "") {
                let customerDataDom = elem.parentElement.querySelector('.optionsDropdown li.selected').getAttribute('data-option');

                customerData = JSON.parse(customerDataDom);
                selectedArticles = [];
                totalOrderedQuantity = 0;
                totalOrderAmount = 0;
                netAmount = 0;
                renderList();
                generateOrder();
                renderFinals();

                generateOrderBtn.disabled = false;
            } else {
                generateOrderBtn.disabled = true;
            }
        }
        let cardData = [];

        function basicSearch(searchValue) {
            let modalData = {
                id: 'modalForm',
                cards: {data: cardData.filter((item) => item.name.toLowerCase().includes(searchValue.toLowerCase()))},
            }
            renderCardsInModal(modalData);
        }

        generateOrderBtn.addEventListener('click', () => {
            generateArticlesModal();
        })

        function generateArticlesModal() {
            let data = articles;

            if (data.length > 0) {
                cardData.push(...data.map(item => {
                    return {
                        id: item.id,
                        name: item.article_no,
                        image: item.image == 'no_image_icon.png' ? '/images/no_image_icon.png' : `/storage/uploads/images/${item.image}`,
                        details: {
                            "Category": item.category,
                            "Season": item.season,
                            "Size": item.size,
                        },
                        data: item,
                        onclick: 'generateQuantityModal(this)',
                    };
                }));
            }

            let modalData = {
                id: 'modalForm',
                class: 'h-[80%] w-full',
                cards: {name: 'Articles', count: 3, data: cardData},
                basicSearch: true,
                onBasicSearch: 'basicSearch(this.value)',
                info: `Selected: ${selectedArticles.length}/${maxLimitOfArticles}`,
                flex_col: true,
                calcBottom: [
                    {label: 'Total Quantity - Pcs', name: 'totalShipmentedQty', value: '0', disabled: true},
                    {label: 'Total Amount - Rs.', name: 'totalShipmentAmount', value: '0.0', disabled: true},
                ],
            }

            createModal(modalData);

            totalQuantityDOM = document.querySelector('#modalForm #totalShipmentedQty');
            totalAmountDOM = document.querySelector('#modalForm #totalShipmentAmount');

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

            calculateTotalOrderedQuantity();
            calculateTotalOrderAmount();
            calculateNetAmount();
            renderTotals();
            generateDescription();
            renderList();
            generateOrder();
            renderFinals();
        }

        function generateQuantityModal(elem) {
            let data = JSON.parse(elem.dataset.json).data;
            let alreadySelected = isArticleAlreadySelected(data.id);

            if (limitOfArticles > 0 || alreadySelected) {

                let modalData = {
                    id: 'QuantityModalForm',
                    name: 'Enter Quantity',
                    class: 'h-auto',
                    fields: [
                        {
                            category: 'input',
                            value: `${data.article_no} | ${data.season} | ${data.size} | ${data.category} | ${data.fabric_type} | ${data.quantity} | ${data.sales_rate} - Rs.`,
                            disabled: true,
                        },
                        {
                            category: 'input',
                            label: 'Available - Pcs.',
                            value: formatNumbersDigitLess(data.quantity - data.ordered_quantity),
                            disabled: true,
                        },
                        {
                            category: 'input',
                            label: 'Current Stock - Pcs.',
                            value: formatNumbersDigitLess(data.physical_quantity),
                            disabled: true,
                        },
                        {
                            category: 'input',
                            name: 'quantity',
                            id: 'quantity',
                            type: 'number',
                            label: 'Quantity - Pcs.',
                            placeholder: 'Enter quantity in pcs.',
                            required: true,
                            oninput: "checkMax(this)",
                        },
                    ],
                    fieldsGridCount: '1',
                    bottomActions: [
                        { id: 'setQuantityBtn', text: 'Set Quantity', onclick: `setQuantity(${data.id})` },
                    ],
                };

                createModal(modalData);

                const quantityLabel = document.getElementById(data.id)
                    ?.querySelector('.quantity-label');

                if (quantityLabel) {
                    document.getElementById("quantity").value =
                        parseInt(quantityLabel.textContent.replace(/\D/g, ""));
                }

                document.getElementById("quantity").focus();
                document.getElementById("quantity").addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        document.getElementById("setQuantityBtn-in-modal").click();
                    }
                });

            } else {
                messageBox.innerHTML = `
                    <x-alert type="error" :messages="'You have reached the maximum allowed number of 18 articles.'" />
                `;
                messageBoxAnimation();
            }
        }

        function setQuantity(cardId) {
            let targetCard = document.getElementById(cardId);
            let cardData = JSON.parse(targetCard.dataset.json).data;
            let alreadySelected = isArticleAlreadySelected(cardData.id);

            if (limitOfArticles > 0 || alreadySelected) {

                closeModal('QuantityModalForm');

                let alreadySelectedArticle = selectedArticles.filter(c => c.id == cardData.id);
                let quantityInputDOM = document.getElementById("quantity");
                let quantity = quantityInputDOM.value;
                let quantityLabel = targetCard.querySelector('.quantity-label');

                if (quantity > 0) {
                    if (quantityLabel) {
                        quantityLabel.textContent = `${quantity} Pcs`;
                    } else {
                        targetCard.innerHTML += `
                            <div class="quantity-label absolute text-xs text-[var(--border-success)] top-1 right-2 h-[1rem]">
                                ${quantity} Pcs
                            </div>
                        `;
                    }

                    cardData.orderedQuantity = parseInt(quantity);

                    if (alreadySelectedArticle.length > 0) {
                        alreadySelectedArticle[0].orderedQuantity = parseInt(quantity);
                    } else {
                        selectedArticles.push(cardData);
                    }

                } else {
                    if (quantityLabel) {
                        quantityLabel.remove();
                        const index = selectedArticles.findIndex(c => c.id === cardData.id);
                        deselectArticleAtIndex(index);
                    }
                }

                generateDescription();
                calculateTotalOrderedQuantity();
                calculateTotalOrderAmount();
                calculateNetAmount();
                renderTotals();
                renderList();
                renderFinals();

            } else {
                messageBox.innerHTML = `
                    <x-alert type="error" :messages="'You have reached the maximum allowed number of 18 articles.'" />
                `;
                messageBoxAnimation();
            }
        }

        function updateInfo() {
            const infoDom = document.querySelector('.modalFormInfo span');
            if (!infoDom) return;

            infoDom.textContent = `Selected ${selectedArticles.length}/${maxLimitOfArticles}`;
        }

        function deselectArticleAtIndex(index) {
            if (index !== -1) {
                selectedArticles.splice(index, 1);
            }
        }

        function deselectThisArticle(index) {
            deselectArticleAtIndex(index);

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
        const finalNetAmount = document.getElementById('finalNetAmount');

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
        }

        function generateDescription() {
            selectedArticles.forEach((selectedArticle, index) => {
                selectedArticle.description = `${selectedArticle.size} | ${selectedArticle.category} | ${selectedArticle.season}`;
            });
        }

        function calculateNetAmount() {
            let totalAmount = totalOrderAmount;
            let discount = document.getElementById('discount').value;
            let discountAmount = totalAmount - (totalAmount * (discount / 100));
            netAmount = discountAmount;
        }

        discountDOM.addEventListener('input', calculateNetAmount);

        discountDOM.addEventListener('focus', (e) => {
            e.target.select();
        });

        function renderTotals() {
            totalQuantityDOM.value = formatNumbersWithDigits(totalOrderedQuantity, 1, 1);
            totalAmountDOM.value = formatNumbersWithDigits(totalOrderAmount, 1, 1);
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
            limitOfArticles = maxLimitOfArticles - selectedArticles.length;
            updateInfo();
        }
        renderList();

        function renderFinals() {
            finalOrderedQuantity.textContent = formatNumbersWithDigits(totalOrderedQuantity, 1, 1);
            finalOrderAmount.textContent = formatNumbersWithDigits(totalOrderAmount, 1, 1);
            finalNetAmount.value = formatNumbersWithDigits(netAmount, 1, 1);
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

        let companyData = @json($client_company);
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
                        <div id="order-banner" class="order-banner w-full flex justify-between items-center px-5">
                            <div class="left">
                                <div class="order-logo">
                                    <img src="{{ asset('images/${companyData.logo}') }}" alt="garmentsos-pro"
                                        class="w-[12rem]" />
                                    <div class='mt-1'>${ companyData.phone_number }</div>
                                </div>
                            </div>
                            <h1 class="text-2xl font-medium text-[var(--primary-color)]">Sales Order</h1>
                        </div>
                        <hr class="w-full my-3 border-gray-600">
                        <div id="order-header" class="order-header w-full flex justify-between px-5">
                            <div class="left w-50 space-y-1">
                                <div class="order-customer text-lg leading-none capitalize font-medium text-nowrap">M/s: ${customerData.customer_name}</div>
                                <div class="order-person text-md text-lg leading-none">${customerData.urdu_title}</div>
                                <div class="order-address text-md leading-none">${customerData.address}, ${customerData.city}</div>
                                <div class="order-phone text-md leading-none">${customerData.phone_number}</div>
                            </div>
                            <div class="right w-50 my-auto text-right text-sm text-gray-600 space-y-1.5">
                                <div class="order-date leading-none">Date: ${orderDate}</div>
                                <div class="order-number leading-none capitalize font-medium">Order No.: ${orderNo}</div>
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
                                    <div class="w-1/4 text-right grow">${totalQuantityDOM.value}</div>
                                </div>
                                <div class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                                    <div class="text-nowrap">Total Amount</div>
                                    <div class="w-1/4 text-right grow">${totalAmountDOM.value}</div>
                                </div>
                            </div>
                            <div id="order-total" class="tr flex justify-between w-full px-2 gap-2 text-sm">
                                <div class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                                    <div class="text-nowrap">Discount - %</div>
                                    <div class="w-1/4 text-right grow">${discountDOM.value}</div>
                                </div>
                                <div
                                    class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                                    <div class="text-nowrap">Net Amount</div>
                                    <div class="w-1/4 text-right grow">${finalNetAmount.value}</div>
                                </div>
                            </div>
                        </div>
                        <hr class="w-full my-3 border-gray-600">
                        <div class="tfooter flex w-full text-sm px-4 justify-between text-gray-600">
                            <P class="leading-none">Powered by SparkPair</P>
                            <p class="leading-none text-sm">&copy; 2025 SparkPair | +92 316 5825495</p>
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
                url: '{{ route("orders.create") }}',
                method: 'GET',
                data: {
                    date: inputElem.value,
                },
                success: function(response) {
                    populateOptions(response.customers_options);
                    articles = response.articles || [];
                },
                error: function() {
                    alert('Error submitting form');
                }
            });
        }

        function populateOptions(customers_options) {
            const customerSelectDom = document.getElementById('customer_id');
            customerSelectDom.disabled = false;
            customerSelectDom.value = "-- Select Customer --";
            const dropdownUl = customerSelectDom.parentElement.parentElement.parentElement.querySelector('ul');
            dropdownUl.innerHTML = ""; // Clear existing options
            optionsHTML = `
                <li data-for="customer_id" data-value="" onmousedown="selectThisOption(this)" class="py-2 px-3 cursor-pointer rounded-lg transition hover:bg-[var(--h-bg-color)] text-nowrap overflow-x-auto scrollbar-hidden selected">-- Select Customer --</li>
            `;
            customers_options.forEach(customer => {
                optionsHTML += `
                    <li data-for="customer_id" data-value="${customer.data_option.id}" onmousedown="selectThisOption(this)" data-option='${JSON.stringify(customer.data_option)}' class="py-2 px-3 cursor-pointer rounded-lg transition hover:bg-[var(--h-bg-color)] text-nowrap overflow-x-auto scrollbar-hidden">${customer.text}</li>
                `;
            });
            dropdownUl.innerHTML = optionsHTML;
        }

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
