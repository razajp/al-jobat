@extends('app')
@section('title', 'Add Article | ' . $client_company->name)
@section('content')
@php
    $categories_options = app('article')->categories;

    $seasons_options = app('article')->seasons;

    $sizes_options = app('article')->sizes;
@endphp
    <!-- Main Content -->
    <!-- Progress Bar -->
    <div class="mb-5 max-w-5xl mx-auto">
        <x-search-header heading="Add Article" link linkText="Show Articles" linkHref="{{ route('articles.index') }}"/>
        <x-progress-bar
            :steps="['Enter Details', 'Enter Rates', 'Upload Image']"
            :currentStep="1"
        />
    </div>

    <div class="row max-w-5xl mx-auto flex gap-4">
        <!-- Form -->
        <form id="form" action="{{ route('articles.store') }}" method="post" enctype="multipart/form-data"
            class="bg-[var(--secondary-bg-color)] text-sm rounded-xl shadow-lg p-8 border border-[var(--glass-border-color)]/20 pt-14 grow relative overflow-hidden">
            @csrf
            <x-form-title-bar title="Add Article" />
            <!-- Step 1: Basic Information -->
            <div class="step1 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- article_no -->
                    <x-input
                        label="Article No"
                        name="article_no"
                        id="article_no"
                        type="number"
                        placeholder="Enter article no"
                        required
                    />

                    <!-- date -->
                    <x-input
                        label="Date"
                        name="date"
                        id="date"
                        validateMin
                        min="2024-01-01"
                        validateMax
                        max="{{ now()->toDateString() }}"
                        type="date"
                        required
                    />

                    {{-- category --}}
                    <x-select
                        label="Category"
                        name="category"
                        id="category"
                        :options="$categories_options"
                        showDefault
                    />

                    {{-- size --}}
                    <x-select
                        label="Size"
                        name="size"
                        id="size"
                        :options="$sizes_options"
                        required
                        showDefault
                        searchable
                    />

                    {{-- season --}}
                    <x-select
                        label="Season"
                        name="season"
                        id="season"
                        :options="$seasons_options"
                        required
                        showDefault
                    />

                    {{-- quantity --}}
                    <x-input
                        label="Quantity - Pcs."
                        name="quantity"
                        id="quantity"
                        type="number"
                        placeholder="Enter quantity"
                    />

                    {{-- extra_pcs --}}
                    <x-input
                        label="Extra Pcs."
                        name="extra_pcs"
                        id="extra_pcs"
                        type="number"
                        placeholder="Enter extra pcs"
                    />

                    {{-- fabric_type --}}
                    <x-input
                        label="Fabric Type"
                        name="fabric_type"
                        id="fabric_type"
                        type="text"
                        placeholder="Enter fabric type"
                    />
                </div>
            </div>

            <!-- Step 2: Production Details -->
            <div class="step2 hidden space-y-4">
                <div class="step2 hidden space-y-4 ">
                    <div class="flex justify-between gap-4">
                        {{-- title --}}
                        <div class="grow">
                            <x-input
                                id="title"
                                placeholder="Enter title"
                            />
                        </div>

                        {{-- rate --}}
                        <x-input
                            id="rate"
                            type="number"
                            placeholder="Enter rate"
                        />

                        {{-- add rate button --}}
                        <div class="form-group flex w-10 shrink-0">
                            <input type="button" value="+"
                                class="w-full bg-[var(--primary-color)] text-[var(--text-color)] rounded-lg cursor-pointer border border-[var(--primary-color)]"
                                onclick="addRate()" />
                        </div>
                    </div>
                    {{-- rate showing --}}
                    <div id="rate-table" class="w-full text-left text-sm">
                        <div class="flex justify-between items-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4 mb-4">
                            <div class="grow ml-5">Title</div>
                            <div class="w-1/4">Rate</div>
                            <div class="w-[10%] text-center">Action</div>
                        </div>
                        <div id="rate-list" class="space-y-4 h-[250px] overflow-y-auto my-scrollbar-2">
                            <div class="text-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4">No Rates Added</div>
                        </div>
                    </div>
                    {{-- calc bottom --}}
                    <div id="calc-bottom" class="flex w-full gap-4 text-sm">
                        <div
                            class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full cursor-not-allowed">
                            <div>Total - Rs.</div>
                            <div class="text-right">0.00</div>
                        </div>
                        <div
                            class="final flex justify-between items-center bg-[var(--h-bg-color)] border border-gray-600 rounded-lg py-2 px-4 w-full">
                            <label for="sales_rate" class="text-nowrap grow">Sales Rate - Rs.</label>
                            <input type="text" required name="sales_rate" id="sales_rate" value="0.00"
                                class="text-right bg-transparent outline-none border-none w-[50%]" />
                        </div>
                    </div>
                    <input type="hidden" name="rates_array" id="rates_array" value="[]" />
                </div>
            </div>

            <!-- Step 3: Image -->
            <div class="step3 hidden space-y-4">
                <x-file-upload
                    id="image_upload"
                    name="image_upload"
                    placeholder="{{ asset('images/image_icon.png') }}"
                    uploadText="Upload article image"
                />
            </div>
        </form>

        <div
            class="bg-[var(--secondary-bg-color)] rounded-xl shadow-xl p-8 border border-[var(--glass-border-color)]/20 w-[35%] pt-14 relative overflow-hidden fade-in">
            <x-form-title-bar title="Last Record" />

            <!-- Step 1: Basic Information -->
            <div class="step1 space-y-4 ">
                @if ($lastRecord)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-input
                            label="Article No"
                            value="{{ $lastRecord->article_no }}"
                            disabled
                        />
                        <x-input
                            label="Date"
                            value="{{ $lastRecord->date->format('d-M-Y, D') }}"
                            disabled
                        />
                        <x-input
                            label="Category"
                            value="{{ $lastRecord->category }}"
                            disabled
                        />
                        <x-input
                            label="Size"
                            value="{{ $lastRecord->size }}"
                            disabled
                        />
                        <x-input
                            label="Season"
                            value="{{ $lastRecord->season }}"
                            disabled
                        />
                        <x-input
                            label="Quantity-Pcs"
                            value="{{ $lastRecord->quantity }}"
                            disabled
                        />
                        <x-input
                            label="Extra Pcs"
                            value="{{ $lastRecord->extra_pcs }}"
                            disabled
                        />
                        <x-input
                            label="Fabric Type"
                            value="{{ $lastRecord->fabric_type }}"
                            disabled
                        />
                    </div>
                @else
                    <div class="text-center text-xs text-[var(--border-error)]">No records found</div>
                @endif
            </div>

            <!-- Step 2: Production Details -->
            <div class="step2 hidden space-y-6  h-full flex flex-col">
                @if ($lastRecord)
                    <div class="w-full text-left grow">
                        <div class="flex justify-between items-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4 mb-4">
                            <div class="grow ml-5">Title</div>
                            <div class="w-1/4">Rate</div>
                        </div>
                        <div id="rate-list" class="space-y-4 h-[250px] overflow-y-auto my-scrollbar-2">
                            @if (count($lastRecord->rates_array) === 0)
                                <div class="text-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4">No Rates Added
                                </div>
                            @else
                                @foreach ($lastRecord->rates_array as $rate)
                                    @php
                                        $lastRecord->total_rate += $rate['rate'];
                                    @endphp
                                    <div
                                        class="flex justify-between items-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4">
                                        <div class="grow ml-5">{{ $rate['title'] }}</div>
                                        <div class="w-1/4">{{ number_format($rate['rate'], 2, '.', '') }}</div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-col w-full gap-4">
                        <div
                            class="flex justify-between items-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4 w-full">
                            <div class="grow">Total - Rs.</div>
                            <div class="w-1/4 text-right">{{ number_format($lastRecord->total_rate, 2, '.', '') }}
                            </div>
                        </div>
                        <div
                            class="flex justify-between items-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4 w-full">
                            <div class="text-nowrap grow">Sales Rate - Rs.</div>
                            <div class="w-1/4 text-right">{{ number_format($lastRecord->sales_rate, 2, '.', '') }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center text-xs text-[var(--border-error)]">No records found</div>
                @endif
            </div>

            <!-- Step 3: Production Details -->
            <div class="step3 hidden space-y-6  text-sm">
                @if ($lastRecord)
                    <div class="grid grid-cols-1 md:grid-cols-1">
                        @if ($lastRecord->image == 'no_image_icon.png')
                            <x-file-upload
                                id="image_upload"
                                name="image_upload"
                                placeholder="{{ asset('images/no_image_icon.png') }}"
                                uploadText="Image"
                            />
                        @else
                            <x-file-upload
                                id="image_upload"
                                name="image_upload"
                                placeholder="{{ asset('storage/uploads/images/' . rawurlencode(html_entity_decode($lastRecord->image))) }}"
                                uploadText="Image"
                            />
                        @endif
                    </div>
                @else
                    <div class="text-center text-xs text-[var(--border-error)]">No records found</div>
                @endif
            </div>
        </div>
    </div>

    <script>
        let titleDom = document.getElementById('title');
        let rateDom = document.getElementById('rate');
        let calcBottom = document.querySelector('#calc-bottom');
        let ratesArrayDom = document.getElementById('rates_array');
        let rateCount = 0;

        let totalRate = 0.00;

        let ratesArray = [];

        function addRate() {
            let title = titleDom.value;
            let rate = rateDom.value;

            if (title && rate && ratesArray.filter(rate => rate.title === title).length === 0) {
                let rateList = document.querySelector('#rate-list');

                if (rateCount === 0) {
                    rateList.innerHTML = '';
                }

                rateCount++;
                let rateRow = document.createElement('div');
                rateRow.classList.add('flex', 'justify-between', 'items-center', 'bg-[var(--h-bg-color)]', 'rounded-lg', 'py-2',
                    'px-4');
                rateRow.innerHTML = `
                    <div class="grow ml-5">${title}</div>
                    <div class="w-1/4">${parseFloat(rate).toFixed(2)}</div>
                    <div class="w-[10%] text-center">
                        <button onclick="deleteRate(this)" type="button" class="text-[var(--danger-color)] text-xs px-2 py-1 rounded-lg hover:text-[var(--h-danger-color)] transition-all duration-300 ease-in-out cursor-pointer">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
                rateList.insertBefore(rateRow, rateList.firstChild);

                titleDom.value = '';
                rateDom.value = '';

                titleDom.focus();

                totalRate += parseFloat(rate);

                ratesArray.push({
                    title: title,
                    rate: rate
                });

                updateRates();
            }
        }

        function deleteRate(element) {
            element.parentElement.parentElement.remove();
            rateCount--;
            if (rateCount === 0) {
                let rateList = document.querySelector('#rate-list');
                rateList.innerHTML = `
                    <div class="text-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4">No Rates Added</div>
                `;
            }

            titleDom.focus();

            let rate = parseFloat(element.parentElement.previousElementSibling.innerText);
            totalRate -= rate;

            let title = element.parentElement.previousElementSibling.previousElementSibling.innerText;
            ratesArray = ratesArray.filter(rate => rate.title !== title);

            updateRates();
        }

        function updateRates() {
            calcBottom.innerHTML = `
                <div
                    class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full cursor-not-allowed">
                    <div>Total - Rs.</div>
                    <div class="text-right">${totalRate.toFixed(2)}</div>
                </div>
                <div
                    class="final flex justify-between items-center bg-[var(--h-bg-color)] border border-gray-600 rounded-lg py-2 px-4 w-full">
                    <label for="sales_rate" class="text-nowrap grow">Sales Rate - Rs.</label>
                    <input type="text" required name="sales_rate" id="sales_rate" value="${totalRate.toFixed(2)}"
                        class="text-right bg-transparent outline-none border-none w-[50%]" />
                </div>
            `;

            ratesArrayDom.value = JSON.stringify(ratesArray);
        }

        rateDom.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                addRate();
            }
        });

        const articles = @json($articles);
        const articleNoDom = document.getElementById('article_no');
        const articleNoError = document.getElementById('article_no-error');
        const dateDom = document.getElementById('date');
        const dateError = document.getElementById('date-error');
        const sizeDom = document.getElementById('size');
        const sizeError = document.getElementById('size-error');
        const seasonDom = document.getElementById('season');
        const seasonError = document.getElementById('season-error');
        const quantityDom = document.getElementById('quantity');
        const quantityError = document.getElementById('quantity-error');
        const extraPcsDom = document.getElementById('extra_pcs');
        const extraPcsError = document.getElementById('extra_pcs-error');

        function validateArticleNo() {
            let articleNoValue = parseFloat(articleNoDom.value);
            let existingArticle = articles.some(a =>
                a.article_no.slice(4).split('|')[1] == articleNoValue
            );

            if (!articleNoValue) {
                articleNoDom.classList.add("border-[var(--border-error)]");
                articleNoError.classList.remove("hidden");
                articleNoError.textContent = "Article No field is required.";
                return false;
            } else if (existingArticle) {
                articleNoDom.classList.add("border-[var(--border-error)]");
                articleNoError.classList.remove("hidden");
                articleNoError.textContent = "Article No is already exist.";
                return false;
            } else {
                articleNoDom.classList.remove("border-[var(--border-)]");
                articleNoError.classList.add("hidden");
                return true;
            }
        }

        function validateDate() {
            if (dateDom.value === "") {
                dateDom.classList.add("border-[var(--border-error)]");
                dateError.classList.remove("hidden");
                dateError.textContent = "Date field is required.";
                return false;
            } else {
                dateDom.classList.remove("border-[var(--border-error)]");
                dateError.classList.add("hidden");
                return true;
            }
        }

        function validateSize() {
            if (sizeDom.value === "") {
                sizeDom.classList.add("border-[var(--border-error)]");
                sizeError.classList.remove("hidden");
                sizeError.textContent = "Size field is required.";
                return false;
            } else {
                sizeDom.classList.remove("border-[var(--border-error)]");
                sizeError.classList.add("hidden");
                return true;
            }
        }

        function validateSeason() {
            if (seasonDom.value === "") {
                seasonDom.classList.add("border-[var(--border-error)]");
                seasonError.classList.remove("hidden");
                seasonError.textContent = "Season field is required.";
                return false;
            } else {
                seasonDom.classList.remove("border-[var(--border-error)]");
                seasonError.classList.add("hidden");
                return true;
            }
        }

        function validateQuantity() {
            if (quantityDom.value === "") {
                quantityDom.classList.add("border-[var(--border-error)]");
                quantityError.classList.remove("hidden");
                quantityError.textContent = "Quantity field is required.";
                return false;
            } else if (quantityDom.value < 0) {
                quantityDom.classList.add("border-[var(--border-error)]");
                quantityError.classList.remove("hidden");
                quantityError.textContent = "Quantity is lessthen 0.";
                return false;
            } else {
                quantityDom.classList.remove("border-[var(--border-error)]");
                quantityError.classList.add("hidden");
                return true;
            }
        }

        function validateExtraPcs() {
            if (extraPcsDom.value === "") {
                extraPcsDom.classList.add("border-[var(--border-error)]");
                extraPcsError.classList.remove("hidden");
                extraPcsError.textContent = "Extra Pcs field is required.";
                return false;
            } else {
                extraPcsDom.classList.remove("border-[var(--border-error)]");
                extraPcsError.classList.add("hidden");
                return true;
            }
        }

        articleNoDom.addEventListener("input", validateArticleNo);
        dateDom.addEventListener("change", validateDate);
        sizeDom.addEventListener("input", validateSize);
        seasonDom.addEventListener("input", validateSeason);
        quantityDom.addEventListener("input", validateQuantity);
        extraPcsDom.addEventListener("input", validateExtraPcs);

        function validateForNextStep() {
            let isValidArticleNo = validateArticleNo();
            let isValidDate = validateDate();
            let isValidSize = validateSize();
            let isValidSeason = validateSeason();
            let isValidQuantity = validateQuantity();
            let isValidExtraPcs = validateExtraPcs();

            let isValid = isValidArticleNo || isValidDate || isValidSize || isValidSeason || isValidQuantity || isValidExtraPcs;

            if (!isValid) {
                messageBox.innerHTML = `
                    <x-alert type="error" :messages="'Invalid details, please correct them.'" />
                `;
                messageBoxAnimation();
            } else {
                isValid = true
            }

            return isValid;
        }
    </script>
@endsection
