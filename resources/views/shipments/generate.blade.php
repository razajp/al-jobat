@extends('app')
@section('title', 'Generate Shipment | ' . app('company')->name)
@section('content')
    <!-- Modal -->
    <div id="articleModal"
        class="hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-[var(--overlay-color))] fade-in">
        <x-modal id="articlesModalForm" classForBody="p-5 max-w-6xl h-[45rem]" closeAction="closeArticlesModal">
            <!-- Modal Content Slot -->
            <div class="flex items-start relative h-full">
                <div class="flex-1 h-full overflow-y-auto my-scrollbar-2 flex flex-col">
                    <div class="pr-5 pt-1">
                        <x-search-header heading="Articles" :filter_items="[
                            'all' => 'All',
                            '#' => 'Article No.',
                            'category' => 'Category',
                            'season' => 'Season',
                            'size' => 'Size',
                        ]" />
                    </div>

                    @if (count($articles) > 0)
                        <div class='overflow-y-auto my-scrollbar-2 pt-2 grow'>
                            <div class="card_container grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                                @foreach ($articles as $article)
                                    <div data-json='{{ $article }}' id='{{ $article->id }}'
                                        onclick='generateQuantityModal(this)'
                                        class="contextMenuToggle modalToggle card relative border border-gray-600 shadow rounded-xl min-w-[100px] h-[8rem] flex gap-4 p-2 cursor-pointer overflow-hidden fade-in">
                                        <x-card :data="[
                                            'image' =>
                                                $article->image == 'no_image_icon.png'
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
                            <div id="totalShipmentedQty">0</div>
                        </div>
                        <div
                            class="final flex justify-between items-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4 w-full">
                            <div class="grow">Total Amount - Rs.</div>
                            <div id="totalShipmentAmount">0.0</div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal Action Slot -->
            <x-slot name="actions">
                <button onclick="closeArticlesModal()" type="button"
                    class="px-4 py-2 bg-[var(--secondary-bg-color)] border border-gray-600 text-[var(--secondary-text)] rounded-lg hover:bg-[var(--h-bg-color)] transition-all duration-300 ease-in-out cursor-pointer">
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
        <x-search-header heading="Generate Shipment" link linkText="Show Shipments" linkHref="{{ route('shipments.index') }}"/>
        <x-progress-bar :steps="['Generate Shipment', 'Preview']" :currentStep="1" />
    </div>

    <!-- Form -->
    <form id="form" action="{{ route('shipments.store') }}" method="post" enctype="multipart/form-data"
        class="bg-[var(--secondary-bg-color)] text-sm rounded-xl shadow-lg p-8 border border-[var(--h-bg-color)] pt-12 max-w-4xl mx-auto  relative overflow-hidden">
        @csrf
        <x-form-title-bar title="Generate Shipment" />

        <!-- Step 1: Generate shipment -->
        <div class="step1 space-y-4 ">
            <div class="flex justify-between items-end gap-4">
                {{-- shipment date --}}
                <div class="grow">
                    <x-input label="Date" name="date" id="date" type="date" onchange="getDataByDate(this)"
                        validateMax max='{{ now()->toDateString() }}' validateMin
                        min="{{ now()->subDays(4)->toDateString() }}" required />
                </div>

                <button id="generateShipmentBtn" type="button"
                    class="bg-[var(--primary-color)] px-4 py-2 rounded-lg hover:bg-[var(--h-primary-color)] transition-all duration-300 ease-in-out text-nowrap cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">Select Articles</button>
            </div>
            {{-- rate showing --}}
            <div id="shipment-table" class="w-full text-left text-sm">
                <div class="flex justify-between items-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4 mb-4">
                    <div class="w-[10%]">#</div>
                    <div class="w-1/6">Qty.</div>
                    <div class="grow">Decs.</div>
                    <div class="w-1/6">Rate/Pc</div>
                    <div class="w-1/5">Amount</div>
                    <div class="w-[10%] text-center">Action</div>
                </div>
                <div id="shipment-list" class="h-[20rem] overflow-y-auto my-scrollbar-2">
                    <div class="text-center bg-[var(--h-bg-color)] rounded-lg py-3 px-4">No Rates Added</div>
                </div>
            </div>

            <div class="flex w-full grid grid-cols-1 md:grid-cols-2 gap-3 text-sm mt-5 text-nowrap">
                <div class="total-qty flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                    <div class="grow">Total Quantity - Pcs</div>
                    <div id="finalShipmentQuantity">0</div>
                </div>
                <div class="final flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                    <div class="grow">Total Amount - Rs.</div>
                    <div id="finalShipmentAmount">0.0</div>
                </div>
                <div
                    class="final flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                    <label for="discount" class="grow">Discount - %</label>
                    <input type="text" name="discount" id="discount" value="10"
                        class="text-right bg-transparent outline-none w-1/2 border-none" readonly />
                </div>
                <div class="final flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                    <div class="grow">Net Amount - Rs.</div>
                    <input type="text" name="netAmount" id="finalNetAmount" value="0.0" readonly
                        class="text-right bg-transparent outline-none w-1/2 border-none" />
                </div>
            </div>
            <input type="hidden" name="articles" id="articles" value="">
        </div>

        <!-- Step 2: view shipment -->
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
        let totalShipmentQuantity = 0;
        let totalShipmentAmount = 0;
        let netAmount = 0;

        const lastShipment = @json($last_shipment);
        const articleModalDom = document.getElementById("articleModal");
        const quantityModalDom = document.getElementById("quantityModal");
        const generateShipmentBtn = document.getElementById("generateShipmentBtn");
        generateShipmentBtn.disabled = true;

        function trackStateOfgenerateBtn(value) {
            if (value != "") {
                generateShipmentBtn.disabled = false;
            } else {
                generateShipmentBtn.disabled = true;
            }
        }

        let totalQuantityDOM;
        let totalAmountDOM;

        let isModalOpened = false;
        let isQuantityModalOpened = false;

        generateShipmentBtn.addEventListener('click', () => {
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
                                ${selectedArticle.shipmentQuantity} Pcs
                            </div>
                        `;
                    } else {
                        quantityLabelDom.textContent = `${selectedArticle.shipmentQuantity} Pcs`;
                    }
                });
            }

            totalQuantityDOM = document.getElementById('totalShipmentedQty');
            totalAmountDOM = document.getElementById('totalShipmentAmount');

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
            generateShipment();
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
                                value="#${data.article_no} | ${data.season} | ${data.size} | ${data.category} | ${data.fabric_type} | ${data.quantity} | ${data.sales_rate} - Rs." 
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
                            class="px-4 py-2 bg-[var(--secondary-bg-color)] border border-gray-600 text-[var(--secondary-text)] rounded-lg hover:bg-[var(--h-bg-color)] transition-all duration-300 ease-in-out cursor-pointer">
                            Cancel
                        </button>
                        <button type="button" id="setQuantityBtn" onclick="setQuantity(${data.id})"
                            class="px-5 py-2 bg-[var(--bg-success)] border border-[var(--bg-success)] text-[var(--text-success)] font-medium text-nowrap rounded-lg hover:bg-[var(--h-bg-success)] transition-all duration-300 ease-in-out cursor-pointer">
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
            document.getElementById("quantity").focus()
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

            cardData.shipmentQuantity = parseInt(quantity);

            if (alreadySelectedArticle.length > 0) {
                alreadySelectedArticle[0].shipmentQuantity = parseInt(quantity);
            } else {
                selectedArticles.push(cardData);
            }

            calculateTotalShipmentQuantity();
            calculateTotalShipmentAmount();
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
            generateShipment();

            calculateTotalShipmentQuantity();
            calculateTotalShipmentAmount();
            calculateNetAmount();

            renderFinals();
            renderTotals();
        }

        const finalShipmentQuantity = document.getElementById('finalShipmentQuantity');
        const finalShipmentAmount = document.getElementById('finalShipmentAmount');
        const discountDOM = document.getElementById('discount');
        const finalNetAmount = document.getElementById('finalNetAmount');

        function calculateTotalShipmentQuantity() {
            totalShipmentQuantity = 0;

            selectedArticles.forEach(selectedArticle => {
                totalShipmentQuantity += selectedArticle.shipmentQuantity;
            });

            totalShipmentQuantity = new Intl.NumberFormat('en-US').format(totalShipmentQuantity);
        }

        function calculateTotalShipmentAmount() {
            totalShipmentAmount = 0;

            selectedArticles.forEach(selectedArticle => {
                totalShipmentAmount += selectedArticle.shipmentQuantity * selectedArticle.sales_rate;
            });

            totalShipmentAmount = new Intl.NumberFormat('en-US', {
                minimumFractionDigits: 1,
                maximumFractionDigits: 1
            }).format(totalShipmentAmount);
        }

        function generateDecription() {
            selectedArticles.forEach((selectedArticle, index) => {
                selectedArticle.description =
                    `${selectedArticle.size} | ${selectedArticle.category} | ${selectedArticle.season}`;
            });
        }

        function calculateNetAmount() {
            let totalAmount = parseFloat(totalShipmentAmount.replace(/,/g, ''));
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
            totalQuantityDOM.textContent = totalShipmentQuantity;
            totalAmountDOM.textContent = totalShipmentAmount;
        }

        const orderListDOM = document.getElementById('shipment-list');

        function renderList() {
            if (selectedArticles.length > 0) {
                let clutter = "";
                selectedArticles.forEach((selectedArticle, index) => {
                    clutter += `
                        <div class="flex justify-between items-center border-t border-gray-600 py-3 px-4">
                            <div class="w-[10%]">${selectedArticle.article_no}</div>
                            <div class="w-1/6">${selectedArticle.shipmentQuantity} pcs</div>
                            <div class="grow">${selectedArticle.description}</div>
                            <div class="w-1/6">${selectedArticle.sales_rate}</div>
                            <div class="w-1/5">${selectedArticle.sales_rate * selectedArticle.shipmentQuantity}</div>
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
            updateInputShipmentedArticles();
        }
        renderList();

        function renderFinals() {
            finalShipmentQuantity.textContent = totalShipmentQuantity;
            finalShipmentAmount.textContent = totalShipmentAmount;
            finalNetAmount.value = netAmount;
        }

        function updateInputShipmentedArticles() {
            let inputShipmentedArticles = document.getElementById('articles');
            let finalArticlesArray = selectedArticles.map(article => {
                return {
                    id: article.id,
                    description: article.description,
                    shipment_quantity: article.shipmentQuantity
                }
            });
            inputShipmentedArticles.value = JSON.stringify(finalArticlesArray);
        }

        let companyData = @json(app('company'));
        let shipmentNo;
        let shipmentDate;
        const previewDom = document.getElementById('preview');

        function generateShipmentNo() {
            let lastShipmentNo = lastShipment.shipment_no
            const nextShipmentNo = String(parseInt(lastShipmentNo) + 1).padStart(4, '0');
            return nextShipmentNo;
        }

        function getShipmentDate() {
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

        function generateShipment() {
            shipmentNo = generateShipmentNo();
            shipmentDate = getShipmentDate();

            if (selectedArticles.length > 0) {
                previewDom.innerHTML = `
                    <div id="shipment" class="shipment flex flex-col h-full">
                        <div id="shipment-banner" class="shipment-banner w-full flex justify-between items-center mt-8 px-5">
                            <div class="left">
                                <div class="shipment-logo">
                                    <img src="{{ asset('images/${companyData.logo}') }}" alt="Track Point"
                                        class="w-[12rem]" />
                                </div>
                            </div>
                            <div class="right">
                                <div class="text-right">
                                    <h1 class="text-2xl font-medium text-[var(--primary-color)]">Shipment</h1>
                                    <div class='mt-1'>${ companyData.phone_number }</div>
                                </div>
                            </div>
                        </div>
                        <hr class="w-full my-3 border-gray-600">
                        <div id="shipment-header" class="shipment-header w-full flex justify-between px-5">
                            <div class="left w-50 my-auto text-sm text-gray-600 space-y-1.5">
                                <div class="shipment-date leading-none">Date: ${shipmentDate}</div>
                                <div class="shipment-number leading-none">Shipment No.: ${shipmentNo}</div>
                                <input type="hidden" name="shipment_no" value="${shipmentNo}" />
                            </div>
                            <div class="right w-50 my-auto text-right text-sm text-gray-600 space-y-1.5">
                                <div class="shipment-copy leading-none">Shipment Copy: Office</div>
                                <div class="shipment-copy leading-none">Document: Shipment</div>
                            </div>
                        </div>
                        <hr class="w-full my-3 border-gray-600">
                        <div id="shipment-body" class="shipment-body w-[95%] grow mx-auto">
                            <div class="shipment-table w-full">
                                <div class="table w-full border border-gray-600 rounded-lg pb-2.5 overflow-hidden">
                                    <div class="thead w-full">
                                        <div class="tr flex justify-between w-full px-4 py-1.5 bg-[var(--primary-color)] text-white">
                                            <div class="th text-sm font-medium w-[7%]">S.No</div>
                                            <div class="th text-sm font-medium w-[10%]">Article</div>
                                            <div class="th text-sm font-medium grow">Description</div>
                                            <div class="th text-sm font-medium w-[10%]">Pcs.</div>
                                            <div class="th text-sm font-medium w-[10%]">Packets</div>
                                            <div class="th text-sm font-medium w-[10%]">Rate</div>
                                            <div class="th text-sm font-medium w-[10%]">Amount</div>
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
                                                        <div class="td text-sm font-semibold w-[10%]">#${article.article_no}</div>
                                                        <div class="td text-sm font-semibold grow">${article.description}</div>
                                                        <div class="td text-sm font-semibold w-[10%]">${article.shipmentQuantity}</div>
                                                        <div class="td text-sm font-semibold w-[10%]">${article.pcs_per_packet ? Math.floor(article.shipmentQuantity / article.pcs_per_packet) : 0}</div>
                                                        <div class="td text-sm font-semibold w-[10%]">
                                                            ${new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(article.sales_rate)}
                                                        </div>
                                                        <div class="td text-sm font-semibold w-[10%]">
                                                            ${new Intl.NumberFormat('en-US', { minimumFractionDigits: 1, maximumFractionDigits: 1 }).format(parseInt(article.sales_rate) * article.shipmentQuantity)}
                                                        </div>
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
                            <div id="shipment-total" class="tr flex justify-between w-full px-2 gap-2 text-sm">
                                <div class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                                    <div class="text-nowrap">Total Quantity - Pcs</div>
                                    <div class="w-1/4 text-right grow">${totalQuantityDOM.textContent}</div>
                                </div>
                                <div class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                                    <div class="text-nowrap">Total Amount</div>
                                    <div class="w-1/4 text-right grow">${totalAmountDOM.textContent}</div>
                                </div>
                            </div>
                            <div id="shipment-total" class="tr flex justify-between w-full px-2 gap-2 text-sm">
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
            trackStateOfgenerateBtn(inputElem.value);
            $.ajax({
                url: '{{ route('shipments.create') }}',
                method: 'GET',
                data: {
                    date: inputElem.value,
                },
                success: function(response) {
                    const articleModal = $(response).find('#articleModal').html();
                    const articleModalDom = document.getElementById('articleModal');

                    if (articleModal !== undefined && articleModal.trim() !== "") {
                        articleModalDom.innerHTML = articleModal;
                        // addListenerToCards();
                        // addContextMenuListenerToCards();

                        cardsDom = $(articleModal).find('.card_container').children().toArray();

                        cardsDom.forEach((card) => {
                            cardsDataArray.push(JSON.parse(card.dataset.json));
                        })

                        setFilter('all');
                    }
                },
                error: function() {
                    alert('Error submitting form');
                }
            });
        }

        let filterType;

        function setFilter(filterTypeArg) {
            filterType = filterTypeArg;

            searchData(document.getElementById('search_box').value);
        }

        function searchData(search) {
            search = search.toLowerCase();

            const filteredData = cardsDataArray.filter(item => {
                switch (filterType) {
                    case 'all':
                        return (
                            item.article_no.toString().includes(search) ||
                            item.category.toLowerCase().includes(search) ||
                            item.season.toLowerCase().includes(search) ||
                            item.size.toLowerCase().includes(search)
                        );
                        break;

                    case '#':
                        return (
                            item.article_no.toString().includes(search)
                        );
                        break;

                    case 'category':
                        return (
                            item.category.toLowerCase().includes(search)
                        );
                        break;

                    case 'season':
                        return (
                            item.season.toLowerCase().includes(search)
                        );
                        break;

                    case 'size':
                        return (
                            item.size.toLowerCase().includes(search)
                        );
                        break;

                    default:
                        return (
                            item.article_no.toString().includes(search) ||
                            item.category.toLowerCase().includes(search) ||
                            item.season.toLowerCase().includes(search) ||
                            item.size.toLowerCase().includes(search)
                        );
                        break;
                }
            });

            const cardContainerDom = document.querySelector('.card_container');
            cardContainerDom.innerHTML = "";

            if (filteredData.length === 0) {
                // Show "No articles found" message if no results
                const noResultMessage =
                    "<p class='text-center col-span-full text-[var(--border-error)]'>No articles found</p>"
                cardContainerDom.innerHTML = noResultMessage;
            } else {
                filteredData.forEach(item => {
                    const cardElement = cardsDom.find(card => card.id == item.id);
                    if (cardElement) {
                        cardContainerDom.appendChild(cardElement);
                    }
                });
            }
        }

        function validateForNextStep() {
            generateShipment()
            return true;
        }
    </script>
@endsection
