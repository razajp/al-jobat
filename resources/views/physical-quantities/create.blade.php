@extends('app')
@section('title', 'Generate Order | ' . app('company')->name)
@section('content')
    <!-- Modal -->
    <div id="articleModal"
        class="hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-black bg-opacity-50 fade-in">
    </div>
    <div id="quantityModal"
        class="hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-black bg-opacity-50 fade-in">
    </div>
    <!-- Main Content -->
    <h1 class="text-3xl font-bold mb-6 text-center text-[--primary-color] fade-in"> Add Article </h1>

    <!-- Progress Bar -->
    <div class="mb-5 max-w-3xl mx-auto">
        <x-progress-bar :steps="['Generate Order', 'View Order']" :currentStep="1" />
    </div>

    <!-- Form -->
    <form id="form" action="{{ route('orders.store') }}" method="post" enctype="multipart/form-data"
        class="bg-[--secondary-bg-color] text-sm rounded-xl shadow-lg p-8 border border-[--h-bg-color] pt-12 max-w-3xl mx-auto  relative overflow-hidden">
        @csrf
        <div
            class="form-title text-center absolute top-0 left-0 w-full bg-[--primary-color] py-1 capitalize tracking-wide font-medium text-sm">
            <h4>Add New Article</h4>
        </div>

        <!-- Step 1: Generate order -->
        <div class="step1 space-y-4 ">
            <div class="flex justify-between gap-4">
                {{-- order date --}}
                <div class="w-1/3">
                    <x-input label="Date" name="date" id="date" type="date" required />
                </div>
            </div>
            {{-- rate showing --}}
            <div id="order-table" class="w-full text-left text-sm">
                <div class="flex justify-between items-center bg-[--h-bg-color] rounded-lg py-2 px-4 mb-4">
                    <div class="w-[10%]">#</div>
                    <div class="w-1/6">Qty.</div>
                    <div class="grow">Decs.</div>
                    <div class="w-1/6">Rate</div>
                    <div class="w-1/5">Amount</div>
                    <div class="w-[10%] text-center">Action</div>
                </div>
                <div id="order-list" class="h-[250px] overflow-y-auto my-scroller-2">
                    <div class="text-center bg-[--h-bg-color] rounded-lg py-3 px-4">No Rates Added</div>
                </div>
            </div>

            <div class="flex w-full gap-4 text-sm mt-5">
                <div class="total-qty flex justify-between items-center bg-[--h-bg-color] rounded-lg py-2 px-4 w-full">
                    <div class="grow">Total Quantity - Pcs</div>
                    <div id="finalOrderedQuantity">0</div>
                </div>
                <div class="final flex justify-between items-center bg-[--h-bg-color] rounded-lg py-2 px-4 w-full">
                    <div class="grow">Total Amount - Rs.</div>
                    <div id="finalOrderAmount">0.0</div>
                </div>
            </div>
            <input type="hidden" name="ordered_articles" id="ordered_articles" value="">
        </div>

        <!-- Step 2: view order -->
        <div class="step2 hidden space-y-4">
            <x-image-upload id="image_upload" name="image_upload" placeholder="{{ asset('images/image_icon.png') }}"
                uploadText="Upload article image" />
        </div>
    </form>

    <script>
        let selectedArticles = [];
        let totalOrderedQuantity = 0;
        let totalOrderAmount = 0;

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
            selectedArticles = [];
            totalOrderedQuantity = 0;
            totalOrderAmount = 0;
            renderList();
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
            articleModalDom.innerHTML = `
                <x-modal id="articlesModalForm" classForBody="p-5 max-w-6xl h-[45rem]" closeAction="closeArticlesModal">
                    <!-- Modal Content Slot -->
                    <div class="flex items-start relative h-full">
                        <div class="flex-1 h-full overflow-y-auto my-scroller-2 flex flex-col">
                            <h5 id="name" class="text-2xl my-1 text-[--text-color] capitalize font-semibold">Articles</h5>
                            
                            <hr class="border-gray-600 my-3">
                
                            @if (count($articles) > 0)
                                <div class='overflow-y-auto my-scroller-2 pt-2 grow'>
                                    <div class="card_container grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                                        @foreach ($articles as $article)
                                            <div data-json='{{ $article }}' id='{{ $article->id }}' onclick='generateQuantityModal(this)'
                                                class="contextMenuToggle modalToggle card relative border border-gray-600 shadow rounded-xl min-w-[100px] h-[8rem] flex gap-4 p-2 cursor-pointer overflow-hidden fade-in">
                                                <x-card :data="[
                                                    'image' => $article->image == 'no_image_icon.png' 
                                                        ? asset('images/no_image_icon.png') 
                                                        : asset('storage/uploads/images/' . $article->image),
                                                    'status' => $article->image == 'no_image_icon.png' ? 'no_Image' : 'transparent',
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
                            @endif
                            
                            <div class="flex w-full gap-4 text-sm mt-5">
                                <div
                                    class="total-qty flex justify-between items-center bg-[--h-bg-color] rounded-lg py-2 px-4 w-full">
                                    <div class="grow">Total Quantity - Pcs</div>
                                    <div id="totalOrderedQty">0</div>
                                </div>
                                <div
                                    class="final flex justify-between items-center bg-[--h-bg-color] rounded-lg py-2 px-4 w-full">
                                    <div class="grow">Total Amount - Rs.</div>
                                    <div id="totalOrderAmount">0.0</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-modal>
            `;

            openArticlesModal();

            if (selectedArticles.length > 0) {
                selectedArticles.forEach(selectedArticle => {
                    let card = document.getElementById(selectedArticle.id);
                    card.innerHTML += `
                        <div
                            class="quantity-label absolute text-xs text-[--border-success] top-1 right-2 h-[1rem]">
                            ${selectedArticle.orderedQuantity} Pcs
                        </div>
                    `;
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
        };

        function closeArticlesModal() {
            renderList();
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

            quantityModalDom.innerHTML = `
                <x-modal id="quantityModalForm" classForBody="p-5" closeAction="closeQuantityModal">
                    <!-- Modal Content Slot -->
                    <div class="flex items-start relative h-full">
                        <div class="flex-1 h-full overflow-y-auto my-scroller-2">
                            <h5 id="name" class="text-2xl my-1 text-[--text-color] capitalize font-semibold">Article Details</h5>

                            <x-input 
                                value="#${data.article_no} | ${data.season} | ${data.size} | ${data.category} | ${data.fabric_type} | ${data.sales_rate} - Rs." 
                                disabled
                            />

                            <hr class="border-gray-600 mt-3">

                            <div class="w-2/3 mx-auto p-5 flex flex-col gap-4">
                                <x-input 
                                    label="Current Stock"
                                    value="${data.quantity - data.sold_quantity}" 
                                    disabled
                                />
                                
                                <x-input 
                                    label="Quantity"
                                    name="quantity" 
                                    id="quantity" 
                                    type="number" 
                                    placeholder="Enter quantity" 
                                    required
                                    
                                    validateMax
                                    max="${data.quantity - data.sold_quantity}"
                                />
                            </div>
                        </div>
                    </div>
                
                    <!-- Modal Action Slot -->
                    <x-slot name="actions">
                        <button onclick="closeQuantityModal()" type="button"
                            class="px-4 py-2 bg-[--secondary-bg-color] border border-gray-600 text-[--secondary-text] rounded-lg hover:bg-[--h-bg-color] transition-all duration-300 ease-in-out">
                            Cancel
                        </button>
                        <button type="button" id="setQuantityBtn" onclick="setQuantity(${data.id})"
                            class="px-5 py-2 bg-[--bg-success] border border-[--bg-success] text-nowrap rounded-lg hover:bg-[--h-bg-success] transition-all 0.3s ease-in-out">
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
        };

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
            if (quantityLabel && quantity <= 0) {
                quantityLabel.remove();
                const index = selectedArticles.findIndex(c => c.id === cardData.id);
                deselectArticleAtIndex(index);
            } else if (quantityLabel) {
                quantityLabel.textContent = `${quantity} Pcs`;
            } else {
                targetCard.innerHTML += `
                    <div
                        class="quantity-label absolute text-xs text-[--border-success] top-1 right-2 h-[1rem]">
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

            calculateTotalOrderedQuantity();
            calculateTotalOrderAmount();
            renderTotals();
            closeQuantityModal();
        }

        function deselectArticleAtIndex(index) {
            if (index !== -1) {
                selectedArticles.splice(index, 1);
            }
        }

        function deselectThisArticle(index) {
            deselectArticleAtIndex(index);

            renderList();

            calculateTotalOrderedQuantity();
            calculateTotalOrderAmount();

            renderFinals();
            renderTotals();
        }

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
                            <div class="grow">${selectedArticle.size} | ${selectedArticle.category} | ${selectedArticle.season}</div>
                            <div class="w-1/6">${selectedArticle.sales_rate}</div>
                            <div class="w-1/5">${selectedArticle.sales_rate * selectedArticle.orderedQuantity}</div>
                            <div class="w-[10%] text-center">
                                <button onclick="deselectThisArticle(${index})" type="button" class="text-[--danger-color] text-xs px-2 py-1 rounded-lg hover:text-[--h-danger-color] transition-all duration-300 ease-in-out">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    `;
                });

                orderListDOM.innerHTML = clutter;
            } else {
                orderListDOM.innerHTML =
                    `<div class="text-center bg-[--h-bg-color] rounded-lg py-2 px-4">No Orders Yet</div>`;
            }
            updateInputOrderedArticles();
        }
        renderList();

        const finalOrderedQuantity = document.getElementById('finalOrderedQuantity');
        const finalOrderAmount = document.getElementById('finalOrderAmount');

        function renderFinals() {
            finalOrderedQuantity.textContent = totalOrderedQuantity;
            finalOrderAmount.textContent = totalOrderAmount;
        }

        function updateInputOrderedArticles() {
            let inputOrderedArticles = document.getElementById('ordered_articles');
            let finalArticlesArray = selectedArticles.map(article => {
                return {
                    id: article.id,
                    ordered_quantity: article.orderedQuantity
                }
            });
            inputOrderedArticles.value = JSON.stringify(finalArticlesArray);
        }

        function validateForNextStep() {
            return true;
        }
    </script>
@endsection
