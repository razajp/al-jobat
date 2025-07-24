@extends('app')
@section('title', 'Add Production | ' . app('company')->name)
@section('content')
@php
    $category_options = [
        'a' => ['text'  => 'A'],
        'b' => ['text'  => 'B'],
];
@endphp
    <!-- Main Content -->
    <div class="max-w-5xl mx-auto">
        <x-search-header heading="Add Production" link linkText="Show Productions" linkHref="{{ route('productions.index') }}"/>
        <x-progress-bar :steps="['Master Information', 'Details']" :currentStep="1" />
    </div>

    <!-- Form -->
    <form id="form" action="{{ route('productions.store') }}" method="post"
        class="bg-[var(--secondary-bg-color)] text-sm rounded-xl shadow-lg p-8 border border-[var(--glass-border-color)]/20 pt-14 max-w-5xl mx-auto  relative overflow-hidden">
        @csrf
        <x-form-title-bar title="Add Production" />

        <div class="step1 space-y-4 ">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- article --}}
                <x-input label="Article" id="article" placeholder='Select Article' class="cursor-pointer" withImg imgUrl="" readonly required />
                <input type="hidden" name="article_id" id="article_id" value="" />

                {{-- work --}}
                    <x-select 
                    label="Work"
                    name="work"
                    id="work"
                    :options="$worke_options"
                    showDefault
                    required
                />

                {{-- worker --}}
                <x-select 
                    label="Worker"
                    name="worker"
                    id="worker"
                    :options="$worker_options"
                    showDefault
                    required
                    onchange="trackWorkerState(this)"
                />

                {{-- balance --}}
                <x-input label="Balance" id="balance" placeholder='Balance' disabled />
            </div>
        </div>

        <div class="step2 space-y-4 hidden">
            <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-3 gap-4">
                {{-- tags  --}}
                <x-input label="Tags" name="tags" id="tags" placeholder="Select Tags" class="cursor-pointer" required onclick="generateSelectTagModal()"/>

                {{-- packets --}}
                <x-input label="Packets" name="packets" id="packets" type="number" placeholder="Enter packet count" required />

                {{-- category --}}
                <x-select 
                    label="Category"
                    name="category"
                    id="category"
                    :options="$category_options"
                    required
                />
            </div>

            <hr class="border-gray-600 my-3">

            <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-4 text-sm mt-5 items-start">
                <div class="first w-full">
                    <div class="current-phys-qty flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4">
                        <div class="grow">Total Physical Stock - Pcs.</div>
                        <div id="currentPhysicalQuantity">0</div>
                    </div>
                </div>
                <div class="second w-full">
                    <div class="total-qty flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4">
                        <div class="grow">Total Quantity - Pcs.</div>
                        <div id="finalOrderedQuantity">0</div>
                    </div>
                    <div id="total-qty-error" class="text-[var(--border-error)] text-xs mt-1 hidden transition-all 0.3s ease-in-out"></div>
                </div>
                <div class="thered w-full">
                    <div class="final flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4">
                        <div class="grow">Remaining Quantity - Pcs.</div>
                        <div id="remainingquantity">0</div>
                    </div>
                </div>
                <div class="fourth w-full">
                    <div class="final flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4">
                        <div class="grow">Total Amount - Rs.</div>
                        <div id="finalOrderAmount">0.0</div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        const articleSelectInputDOM = document.getElementById("article");
        const articleIdInputDOM = document.getElementById("article_id");
        const articleImageShowDOM = document.getElementById("img-article");

        const pcsPerPacketDom = document.getElementById('pcs_per_packet');
        const processedByDom = document.getElementById('processed_by');
        const packetsDom = document.getElementById('packets');
        const categoryDom = document.getElementById('category');

        const totalPhysicalQuantityDom = document.getElementById('currentPhysicalQuantity');
        const finalOrderedQuantityDom = document.getElementById('finalOrderedQuantity');
        const remainingqQuantityDom = document.getElementById('remainingquantity');
        const finalOrderAmountDom = document.getElementById('finalOrderAmount');

        let tags = [];
        let selectedTagsArray = [];

        let totalQuantity = 0;
        let totalAmount = 0;

        articleSelectInputDOM.addEventListener('click', () => {
            generateArticlesModal();
        })

        function generateArticlesModal() {
            let data = @json($articles);
            let cardData = [];

            console.log(data);
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
                        onclick: 'selectThisArticle(this)',
                    };
                }));
            }
            
            let modalData = {
                id: 'modalForm',
                cards: {name: 'Articles', count: 3, data: cardData},
            }

            createModal(modalData);
        }

        let selectedArticle = null;

        function selectThisArticle(articleElem) {
            selectedArticle = JSON.parse(articleElem.getAttribute('data-json')).data;

            articleIdInputDOM.value = selectedArticle.id;
            let value = `${selectedArticle.article_no} | ${selectedArticle.season} | ${selectedArticle.size} | ${selectedArticle.category} | ${formatNumbersDigitLess(selectedArticle.quantity)} (pcs) | Rs. ${formatNumbersWithDigits(selectedArticle.sales_rate, 1, 1)}`;
            articleSelectInputDOM.value = value;
            
            articleImageShowDOM.classList.remove('opacity-0');
            articleImageShowDOM.src = articleElem.querySelector('img').src
            
            closeModal('modalForm');
            trackFieldsDisability();
            calculateTotal();

            totalPhysicalQuantityDom.innerText = selectedArticle.physical_quantity;
            
            function formatArticleDate(inputDate) {
                let [day, month, yearWithDay] = inputDate.replace(',', '').split('-');
                let [year] = yearWithDay.split(' ');

                const monthMap = {
                    Jan: '01', Feb: '02', Mar: '03', Apr: '04', May: '05', Jun: '06',
                    Jul: '07', Aug: '08', Sep: '09', Oct: '10', Nov: '11', Dec: '12'
                }

                return `${year}-${monthMap[month]}-${day.padStart(2, '0')}`;
            }

            document.getElementById('date').min = formatArticleDate(selectedArticle.date);
            
            
            if (selectedArticle.pcs_per_packet > 0) {
                pcsPerPacketDom.readOnly = true;
                pcsPerPacketDom.classList.remove('bg-[var(--h-bg-color)]');
                pcsPerPacketDom.classList.add('bg-transparent');
                pcsPerPacketDom.classList.add('cursor-not-allowed');
                pcsPerPacketDom.value = selectedArticle.pcs_per_packet;
                processedByDom.readOnly = true;
                processedByDom.classList.remove('bg-[var(--h-bg-color)]');
                processedByDom.classList.add('bg-transparent');
                processedByDom.classList.add('cursor-not-allowed');
                processedByDom.value = selectedArticle.processed_by;
            } else {
                pcsPerPacketDom.readOnly = false;
                pcsPerPacketDom.classList.add('bg-[var(--h-bg-color)]');
                pcsPerPacketDom.classList.remove('bg-transparent');
                pcsPerPacketDom.classList.remove('cursor-not-allowed');
                pcsPerPacketDom.value = '';
                processedByDom.readOnly = false;
                processedByDom.classList.add('bg-[var(--h-bg-color)]');
                processedByDom.classList.remove('bg-transparent');
                processedByDom.classList.remove('cursor-not-allowed');
                processedByDom.value = '';
            }

            remainingqQuantityDom.innerText = new Intl.NumberFormat('en-US').format(pcsPerPacketDom.value > 0 && parseInt(totalPhysicalQuantityDom.textContent) > 0 ? selectedArticle.quantity - parseInt(totalPhysicalQuantityDom.textContent) : selectedArticle.quantity);
        }

        function trackFieldsDisability() {
            if (!selectedArticle) {
                pcsPerPacketDom.disabled = true;
                packetsDom.disabled = true;
                categoryDom.disabled = true;
            } else {
                pcsPerPacketDom.disabled = false;
                packetsDom.disabled = false;
                categoryDom.disabled = false;
            }
        }
        trackFieldsDisability();

        function calculateTotal() {
            if (selectedArticle) {
                let pcsPerPacket = pcsPerPacketDom.value;
                let packets = packetsDom.value;

                totalQuantity = pcsPerPacket * packets;
                totalAmount = totalQuantity * parseInt(selectedArticle.sales_rate);

                finalOrderedQuantityDom.textContent = new Intl.NumberFormat('en-US').format(totalQuantity);

                finalOrderAmountDom.innerText = new Intl.NumberFormat('en-US', {
                    minimumFractionDigits: 1,
                    maximumFractionDigits: 1
                }).format(totalAmount);
            }
        }

        const totalQtyDom = document.querySelector('.total-qty');
        const totalQtyErrorDom = document.getElementById('total-qty-error');

        function trackArticleQuantity() {
            if (selectedArticle && (totalQuantity + parseInt(totalPhysicalQuantityDom.textContent)) > selectedArticle.quantity) {
                totalQtyDom.classList.add('border-[var(--border-error)]');
                totalQtyErrorDom.innerText = `Quantity exceeds the available stock (${selectedArticle.quantity} pcs)`;
                totalQtyErrorDom.classList.remove('hidden');
            } else {
                totalQtyDom.classList.remove('border-[var(--border-error)]');
                totalQtyDom.classList.add('border-gray-600');
                totalQtyErrorDom.classList.add('hidden');
                totalQtyErrorDom.innerText = '';
            }
        }

        function trackWorkerState(elem) {
            const selectParent = elem.closest('.selectParent');
            const selectedWorkerData = JSON.parse(selectParent.querySelector('li.selected').dataset.option || '{}');
            document.getElementById('balance').value = selectedWorkerData?.balance || 0;
            tags = selectedWorkerData.taags || [];
            elem.value !== '' && gotoStep(2);
        }

        function generateSelectTagModal() {
            let data = tags;
            let cardData = [];

            console.log(data);
            if (data.length > 0) {
                cardData.push(...data.map(item => {
                    return {
                        id: item.tag,
                        name: item.tag,
                        details: {
                            'Supplier': item.supplier_name,
                            'Available Quantity': item.available_quantity,
                            'Selected Quantity': item.selected_quantity || 0,
                        },
                        data: item,
                        onclick: `generateQuantityModal(${JSON.stringify(item)})`,
                    };
                }));
            }
            
            let modalData = {
                id: 'modalForm',
                cards: {name: 'Tags', count: 3, data: cardData},
            }

            createModal(modalData);
        }

        function generateQuantityModal(item) {
            console.log(item);
            
            let modalData = {
                id: 'quantityModal',
                name: 'Enter Quantity',
                class: 'h-auto',
                fields: [
                    {
                        category: 'input',
                        label: 'Unit',
                        value: item.unit,
                        disabled: true,
                    },
                    {
                        category: 'input',
                        id: 'tag',
                        name: 'tag',
                        type: 'hidden',
                        value: item.tag,
                    },
                    {
                        category: 'input',
                        label: 'Avalaible Quantity',
                        value: item.available_quantity,
                        disabled: true,
                    },
                    {
                        category: 'explicitHtml',
                        html: `
                            <x-input label="Quantity" name="quantity" id="quantity" type="number" placeholder="Enter quantity" required oninput="validateInput(this)"/>
                        `,
                    },
                ],
                fieldsGridCount: '1',
                bottomActions: [
                    {id: 'add', text: 'Add', onclick: 'selectWithQuantity(this)'},
                ],
            }

            createModal(modalData)

            document.querySelector('input[name="quantity"]').dataset.validate = `max:${item.available_quantity}`;
        }

        function selectWithQuantity(elem) {
            console.log(elem.closest('form'));
            const inputs = elem.closest('form').querySelectorAll('input:not([disabled])');
            let detail = {};

            inputs.forEach(input => {
                const name = input.getAttribute('name');
                if (name != null) {
                    const value = input.value;

                    if (name == "quantity") {
                        detail[name] = parseInt(value);
                    } else {
                        detail[name] = value;
                    }
                }
            });

            if (isNaN(detail.quantity) || detail.quantity <= 0) {
                detail = {};
            }

            if (Object.keys(detail).length > 0) {
                selectedTagsArray.push(detail);
            }
            closeModal('quantityModal');
        }
        
        function validateForNextStep() {
            return true;
        }
    </script>
@endsection
