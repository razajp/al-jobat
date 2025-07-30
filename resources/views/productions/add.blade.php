@extends('app')
@section('title', 'Add Production | ' . app('company')->name)
@section('content')
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
                    name="work"
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
                    name="worker"
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
                <div class="cols-span-full text-center text-[var(--border-error)]">No Rates yet.</div>
            </div>
        </div>
    </form>

    <script>
        let allWorkers = Object.values(@json($worker_options));
        let allWorks = Object.values(@json($work_options));
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

            allWorks.forEach((work) => {
                ul.innerHTML += `
                    <li data-for="work" data-value="${work.text}" onmousedown="selectThisOption(this)" class="py-2 px-3 cursor-pointer rounded-lg hover:bg-[var(--h-bg-color)]">${work.text}</li>
                `;
            })

            const selectedLi = ul.querySelector('li.selected');
            if (selectedLi) {
                selectedLi.dispatchEvent(new MouseEvent('mousedown', { bubbles: true }));
            }
        }

        function trackWorkState(elem) {
            if (elem.value != '') {
                let correctWorkers = allWorkers.filter(worker => worker.data_option.type.title == elem.value);
                
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
                generateSecondStep(elem.value);

                let filteredRates = allRates.filter(rate => rate.type.title == elem.value && rate.categories.includes(selectedArticle.category) && rate.seasons.includes(selectedArticle.season) && rate.sizes.includes(selectedArticle.size));
                if (filteredRates.length > 0) {
                    document.querySelector('input[name="select_rate_name"]').value = '-- Select Rates --';
                    document.querySelector('input[name="select_rate_name"]').disabled = false;
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
                        <li data-for="select_rate" data-value="other" onmousedown="selectThisOption(this)" class="py-2 px-3 cursor-pointer rounded-lg hover:bg-[var(--h-bg-color)]">Other</li>
                    `;

                } else {
                    document.querySelector('input[name="select_rate_name"]').value = '';
                    document.querySelector('input[name="select_rate_name"]').disabled = true;
                }
            }
        }

        function trackWorkerState(elem) {
            const selectParent = elem.closest('.selectParent');
            const selectedWorkerData = JSON.parse(selectParent.querySelector('li.selected').dataset.option || '{}');
            console.log(selectedWorkerData);
            document.getElementById('balance').value = selectedWorkerData?.balance || 0;
            tags = selectedWorkerData.taags || [];
            elem.value !== '' && gotoStep(2);
        }

        function generateSelectTagModal(animate = 'animate') {
            console.log('hello yahan open hua');
            
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
                tags.find(tag => tag.tag === detail.tag).selected_quantity = detail.quantity;
                tags.find(tag => tag.tag === detail.tag).available_quantity -= tags.find(tag => tag.tag === detail.tag).selected_quantity;
                
                closeModal('quantityModalForm');
                closeModal('tagModalForm', 'notAnimate');
                generateSelectTagModal('notAnimate')
            }
        }

        function generateSecondStep(work) {
            console.log(work);
            
            let secondStepHTML = '';
            if (work == 'Cutting') {
                secondStepHTML += `
                    {{-- tags  --}}
                    <x-input label="Tags" name="tags" id="tags" placeholder="Select Tags" class="cursor-pointer" required onclick="generateSelectTagModal()"/>

                    ${!selectedArticle.quantity > 0 ? `        
                        {{-- quantity --}}
                        <x-input label="Quantity" name="article_quantity" id="article_quantity" type="number" placeholder="Enter Quantity" required />
                    ` : `
                        {{-- quantity --}}
                        <x-input label="Quantity" name="article_quantity" id="article_quantity" type="number" value="${selectedArticle.quantity}" disabled />
                    `}

                    <div class="col-span-full grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- select_rate --}}
                    <x-select 
                        label="Select Rate"
                        name="select_rate"
                        id="select_rate"
                        :options="[]"
                        showDefault
                        required
                        onchange="trackSelectRateState(this)"
                    /> 

                    {{-- rate --}}
                    <x-input label="Rate" name="rate" id="rate" disabled placeholder="Rate" />

                    {{-- amount --}}
                    <x-input label="Amount" name="amount" id="amount" disabled placeholder="Amount" />
                `;
            }
            document.getElementById('secondStep').innerHTML = secondStepHTML;
        }

        function trackSelectRateState(elem) {
            if (elem.value != '' && elem.value != 'other') {
                document.getElementById('rate').disabled = true;
                document.getElementById('rate').value = elem.closest('.selectParent').querySelector('li.selected').textContent.split('|')[1].trim();
                calculateAmount();
            } else if (elem.value == 'other') {
                document.getElementById('rate').disabled = false;
            } else {
                document.getElementById('rate').disabled = true;
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
@endsection
