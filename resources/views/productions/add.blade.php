@extends('app')
@section('title', 'Add Production | ' . app('company')->name)
@section('content')
@php
    $productionType = Auth::user()->production_type;
@endphp

    <div class="switch-btn-container flex absolute top-3 md:top-17 left-3 md:left-5 z-40">
        <div class="switch-btn relative flex border-3 border-[var(--secondary-bg-color)] bg-[var(--secondary-bg-color)] rounded-2xl overflow-hidden">
            <!-- Highlight rectangle -->
            <div id="highlight" class="absolute h-full rounded-xl bg-[var(--bg-color)] transition-all duration-300 ease-in-out z-0"></div>

            <!-- Buttons -->
            <button
                id="issueBtn"
                type="button"
                class="relative z-10 px-3.5 md:px-5 py-1.5 md:py-2 cursor-pointer rounded-xl transition-colors duration-300"
                onclick="setProductionType(this, 'issue')"
            >
                <div class="hidden md:block">Issue</div>
                <div class="block md:hidden"><i class="fas fa-cart-shopping text-xs"></i></div>
            </button>
            <button
                id="reciveBtn"
                type="button"
                class="relative z-10 px-3.5 md:px-5 py-1.5 md:py-2 cursor-pointer rounded-xl transition-colors duration-300"
                onclick="setProductionType(this, 'receive')"
            >
                <div class="hidden md:block">Receive</div>
                <div class="block md:hidden"><i class="fas fa-box-open text-xs"></i></div>
            </button>
        </div>
    </div>

    <script>
        let btnTypeGlobal = "issue";

        function setProductionType(btn, btnType) {
            // check if its already selected
            if (btnTypeGlobal == btnType) {
                return;
            }

            doHide = true;

            $.ajax({
                url: "/set-production-type",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    production_type: btnType
                },
                success: function () {
                    location.reload();
                },
                error: function () {
                    alert("Failed to update production type.");
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
            @if($productionType == 'issue')
                const activeBtn = document.querySelector("#issueBtn");
                moveHighlight(activeBtn, "issue");
            @else
                const activeBtn = document.querySelector("#reciveBtn");
                moveHighlight(activeBtn, "receive");
            @endif
        };
    </script>
    @if ($productionType == 'issue')
    @else
        <!-- Main Content -->
        <div class="max-w-4xl mx-auto">
            <x-search-header heading="Add Production" link linkText="Show Productions" linkHref="{{ route('productions.index') }}"/>
            <x-progress-bar :steps="['Master Information', 'Details']" :currentStep="1" />
        </div>

        <!-- Form -->
        <form id="form" action="{{ route('productions.store') }}" method="post"
            class="bg-[var(--secondary-bg-color)] text-sm rounded-xl shadow-lg p-8 border border-[var(--glass-border-color)]/20 pt-14 max-w-4xl mx-auto relative overflow-hidden">
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
                        name="work_id"
                        id="work"
                        :options="[]"
                        showDefault
                        required
                        onchange="trackWorkState(this)"
                        disabled
                    />

                    {{-- worker --}}
                    <x-select
                        label="Worker"
                        name="worker_id"
                        id="worker"
                        :options="[]"
                        showDefault
                        required
                        onchange="trackWorkerState(this)"
                        disabled
                    />

                    {{-- balance --}}
                    <x-input label="Balance" id="balance" placeholder='Balance' disabled />
                </div>
            </div>

            <div class="step2 space-y-4 hidden">
                <div id="secondStep" class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="col-span-full text-center text-[var(--border-error)]">No Detailes yet.</div>
                </div>
            </div>
        </form>

        <script>
            let allWorks = Object.entries(@json($work_options));
            let allWorkers = Object.values(@json($worker_options));
            let allRates = @json($rates);
            let tagModalData = {};
            const articleSelectInputDOM = document.getElementById("article");
            const articleIdInputDOM = document.getElementById("article_id");
            const articleImageShowDOM = document.getElementById("img-article");

            let tags = [];
            let selectedTagsArray = [];

            articleSelectInputDOM.addEventListener('click', () => {
                generateArticlesModal();
            })

            function generateArticlesModal() {
                let data = @json($articles);
                let cardData = [];

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

                document.querySelector('input[name="work_name"]').disabled = false;

                const ul = document.querySelector('ul[data-for="work"]');

                ul.innerHTML = `
                    <li data-for="work" data-value="" onmousedown="selectThisOption(this)" class="py-2 px-3 cursor-pointer rounded-lg hover:bg-[var(--h-bg-color)] selected">-- Select Work --</li>
                `;

                allWorks.forEach(([key, value]) => {
                    ul.innerHTML += `
                        <li data-for="work" data-value="${key}" onmousedown="selectThisOption(this)" class="py-2 px-3 cursor-pointer rounded-lg hover:bg-[var(--h-bg-color)]">${value.text}</li>
                    `;
                })

                const selectedLi = ul.querySelector('li.selected');
                if (selectedLi) {
                    selectedLi.dispatchEvent(new MouseEvent('mousedown', { bubbles: true }));
                }
            }

            function trackWorkState(elem) {
                if (elem.value != '') {
                    let correctWorkers = allWorkers.filter(worker => worker.data_option.type.id == elem.value);

                    if (correctWorkers.length > 0) {
                        document.querySelector('input[name="worker_name"]').disabled = false;
                        const ul = document.querySelector('ul[data-for="worker"]');

                        ul.innerHTML = `
                            <li data-for="worker" data-value="" onmousedown="selectThisOption(this)" class="py-2 px-3 cursor-pointer rounded-lg hover:bg-[var(--h-bg-color)] selected">-- Select Worker --</li>
                        `;

                        correctWorkers.forEach((worker) => {
                            ul.innerHTML += `
                                <li data-for="worker" data-value="${worker.data_option.id}" data-option='${JSON.stringify(worker.data_option)}' onmousedown="selectThisOption(this)" class="py-2 px-3 cursor-pointer rounded-lg hover:bg-[var(--h-bg-color)]">${worker.text}</li>
                            `;
                        })

                        const selectedLi = ul.querySelector('li.selected');
                        if (selectedLi) {
                            selectedLi.dispatchEvent(new MouseEvent('mousedown', { bubbles: true }));
                        }
                    } else {
                        document.querySelector('input[name="worker_name"]').disabled = true;
                        document.querySelector('input[name="worker_name"]').value = '';
                    }
                    generateSecondStep(elem.closest('.selectParent').querySelector('li.selected').textContent.trim());

                    let filteredRates = allRates.filter(rate => rate.type.id == elem.value && rate.categories.includes(selectedArticle.category) && rate.seasons.includes(selectedArticle.season) && rate.sizes.includes(selectedArticle.size));
                    let selectRateNameDom = document.querySelector('input[name="select_rate_name"]');

                    if (selectRateNameDom) {
                        if (filteredRates.length > 0) {
                            selectRateNameDom.value = '-- Select Rates --';
                            selectRateNameDom.disabled = false;
                            let ratesUL = document.querySelector('ul[data-for="select_rate"]');
                            ratesUL.innerHTML = `
                                <li data-for="select_rate" data-value="" onmousedown="selectThisOption(this)" class="py-2 px-3 cursor-pointer rounded-lg hover:bg-[var(--h-bg-color)] selected">-- Select Rates --</li>
                            `;

                            filteredRates.forEach((rate) => {
                                ratesUL.innerHTML += `
                                    <li data-for="select_rate" data-value="${rate.id}" onmousedown="selectThisOption(this)" class="py-2 px-3 cursor-pointer rounded-lg hover:bg-[var(--h-bg-color)]">${rate.title} | ${rate.rate}</li>
                                `;
                            })

                            ratesUL.innerHTML += `
                                <li data-for="select_rate" data-value="0" onmousedown="selectThisOption(this)" class="py-2 px-3 cursor-pointer rounded-lg hover:bg-[var(--h-bg-color)]">Other</li>
                            `;

                        } else {
                            selectRateNameDom.value = '';
                            selectRateNameDom.disabled = true;
                        }
                    }
                }
            }

            function trackWorkerState(elem) {
                const selectParent = elem.closest('.selectParent');
                const selectedWorkerData = JSON.parse(selectParent.querySelector('li.selected').dataset.option || '{}');
                document.getElementById('balance').value = selectedWorkerData?.balance || 0;
                tags = selectedWorkerData.taags || [];
                elem.value !== '' && gotoStep(2);
                selectedTagsArray = [];
            }

            function generateSelectTagModal(animate = 'animate') {
                let data = tags;
                let cardData = [];

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

                tagModalData = {
                    id: 'tagModalForm',
                    cards: {name: 'Tags', count: 3, data: cardData},
                }

                createModal(tagModalData, animate);
            }

            function generateQuantityModal(item) {
                let quantityModalData = {
                    id: 'quantityModalForm',
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
                            focus: 'quantity',
                        },
                    ],
                    fieldsGridCount: '1',
                    bottomActions: [
                        {id: 'add', text: 'Add', onclick: 'selectWithQuantity(this)'},
                    ],
                }

                createModal(quantityModalData)

                document.querySelector('input[name="quantity"]').value = item.selected_quantity || '';
                document.querySelector('input[name="quantity"]').dataset.validate = `max:${item.available_quantity + (item.selected_quantity || 0)}`;
            }

            function selectWithQuantity(elem) {
                const inputs = elem.closest('form').querySelectorAll('input:not([disabled])');
                let detail = {};

                inputs.forEach(input => {
                    const name = input.getAttribute('name');
                    if (name != null && name != '_token') {
                        const value = input.value;

                        if (name == "quantity") {
                            detail[name] = parseInt(value);
                        } else {
                            detail[name] = value;
                        }
                    }
                });

                let existingTag = selectedTagsArray.find(tag => tag.tag == detail.tag);

                if (detail.quantity > 0) {
                    existingTag ? existingTag.quantity = detail.quantity : selectedTagsArray.push(detail);
                } else if (existingTag) {
                    selectedTagsArray = selectedTagsArray.filter(tag => tag.tag !== detail.tag);
                }
                tags.find(tag => tag.tag === detail.tag).selected_quantity = detail.quantity;
                tags.find(tag => tag.tag === detail.tag).available_quantity -= tags.find(tag => tag.tag === detail.tag).selected_quantity;
                document.querySelector('input[name="tags"]').value = JSON.stringify(selectedTagsArray);
                closeModal('quantityModalForm');
                closeModal('tagModalForm', 'notAnimate');
                generateSelectTagModal('notAnimate');
                document.getElementById('tags').value = selectedTagsArray.length > 0 ? `${selectedTagsArray.length} Selected` : '';
            }

            function generateSecondStep(work) {
                let secondStepHTML = '';
                if (work == 'Cutting') {
                    secondStepHTML += `
                        {{-- article --}}
                        <x-input label="Article" name="article" id="article" disabled value="${selectedArticle.article_no} | ${selectedArticle.season} | ${selectedArticle.size} | ${selectedArticle.category} | ${formatNumbersDigitLess(selectedArticle.quantity)} (pcs) | Rs. ${formatNumbersWithDigits(selectedArticle.sales_rate, 1, 1)}" />

                        {{-- tags  --}}
                        <x-input label="Tags" id="tags" placeholder="Select Tags" class="cursor-pointer" required onclick="generateSelectTagModal()" autoComplete="off" />
                        <input type="hidden" name="tags" value="" />

                        ${!selectedArticle.quantity > 0 ? `
                            {{-- quantity --}}
                            <x-input label="Quantity" name="article_quantity" id="article_quantity" type="number" placeholder="Enter Quantity" required oninput="calculateAmount()" />
                        ` : `
                            {{-- quantity --}}
                            <x-input label="Quantity" name="article_quantity" id="article_quantity" type="number" value="${selectedArticle.quantity}" disabled />
                        `}

                        {{-- select_rate --}}
                        <x-select
                            label="Select Rate"
                            id="select_rate"
                            :options="[]"
                            showDefault
                            required
                            onchange="trackSelectRateState(this)"
                        />

                        <div id="titleContainer" class="col-span-full hidden">
                            {{-- title --}}
                            <x-input label="Title" name="title" id="title" placeholder="Enter Title" required/>
                        </div>

                        {{-- rate --}}
                        <x-input label="Rate" name="rate" id="rate" readonly placeholder="Rate" oninput="calculateAmount()" />

                        {{-- amount --}}
                        <x-input label="Amount" name="amount" id="amount" disabled placeholder="Amount" />

                        {{-- receving_date --}}
                        <x-input label="Receving Date" name="receving_date" id="receving_date" required type="date" validateMin min="{{ now()->subDays(14)->toDateString() }}" validateMax max="{{ now()->toDateString() }}" />

                        {{-- token --}}
                        <x-input label="Token" name="token" id="token" disabled placeholder="Token" />
                    `;
                }
                document.getElementById('secondStep').innerHTML = secondStepHTML;
            }

            function trackSelectRateState(elem) {
                const rateInput = document.getElementById('rate');
                const titleInput = document.getElementById('title');
                const titleContainer = document.getElementById('titleContainer');

                if (elem.value !== '' && elem.value !== '0') {
                    titleContainer.classList.add('hidden');
                    rateInput.readOnly = true;
                    const selectedText = elem.closest('.selectParent').querySelector('li.selected').textContent;
                    rateInput.value = selectedText.split('|')[1].trim();
                    titleInput.value = selectedText.split('|')[0].trim();
                    calculateAmount();
                } else if (elem.value === '0') {
                    titleContainer.classList.remove('hidden');
                    titleInput.value = '';
                    rateInput.value = '';
                    rateInput.readOnly = false;
                } else {
                    titleInput.value = '';
                    rateInput.value = '';
                    titleContainer.classList.add('hidden');
                    rateInput.readOnly = true;
                }
            }

            function calculateAmount() {
                let quantity = parseInt(document.getElementById('article_quantity').value);
                let rate = parseInt(document.getElementById('rate').value);
                document.getElementById('amount').value = rate * quantity;
            }

            function validateForNextStep() {
                return true;
            }
        </script>
    @endif
@endsection
