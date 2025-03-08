@extends('app')
@section('title', 'Add Article | ' . app('company')->name)
@section('content')
    <!-- Main Content -->
    <h1 class="text-3xl font-bold mb-6 text-center text-[--primary-color] fade-in"> Add Article </h1>

    <!-- Progress Bar -->
    <div class="mb-5 max-w-5xl mx-auto">
        <x-progress-bar 
            :steps="['Enter Details', 'Enter Rates', 'Upload Image']" 
            :currentStep="1"
        />
    </div>

    <div class="row max-w-5xl mx-auto flex gap-4">
        <!-- Form -->
        <form id="form" action="{{ route('articles.store') }}" method="post" enctype="multipart/form-data"
            class="bg-[--secondary-bg-color] text-sm rounded-xl shadow-lg p-8 border border-[--h-bg-color] pt-12 grow relative overflow-hidden">
            @csrf
            <div
                class="form-title text-center absolute top-0 left-0 w-full bg-[--primary-color] py-1 capitalize tracking-wide font-medium text-sm">
                <h4>Add New Article</h4>
            </div>
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
                        type="date" 
                        required 
                    />
                    
                    <x-input 
                        label="Category"
                        name="category" 
                        id="category" 
                        type="text" 
                        placeholder="Enter category"
                        autocomplete="off"
                        list="category_list"
                        :listOptions="$categories"
                        required 
                    />
                    
                    <x-input 
                        label="Size"
                        name="size" 
                        id="size" 
                        type="text" 
                        placeholder="Enter size"
                        autocomplete="off"
                        list="size_list"
                        :listOptions="$sizes"
                        required 
                    />
                    
                    <x-input 
                        label="Season"
                        name="season" 
                        id="season" 
                        type="text" 
                        placeholder="Enter season"
                        autocomplete="off"
                        list="season_list"
                        :listOptions="$seasons"
                        required 
                    />
                    
                    {{-- quantity --}}
                    <x-input 
                        label="Quantity"
                        name="quantity" 
                        id="quantity" 
                        type="number"
                        placeholder="Enter quantity" 
                        required 
                    />
                    
                    {{-- extra_pcs --}}
                    <x-input 
                        label="Extra Pcs"
                        name="extra_pcs" 
                        id="extra_pcs" 
                        type="number"
                        placeholder="Enter extra pcs" 
                        required 
                    />

                    {{-- fabric_type --}}
                    <x-input 
                        label="Fabric Type" 
                        name="fabric_type" 
                        id="fabric_type" 
                        type="text"
                        placeholder="Enter fabric type" 
                        required
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
                                class="w-full bg-[--primary-color] text-[--text-color] rounded-lg cursor-pointer border border-[--primary-color]"
                                onclick="addRate()" />
                        </div>
                    </div>
                    {{-- rate showing --}}
                    <div id="rate-table" class="w-full text-left text-sm">
                        <div class="flex justify-between items-center bg-[--h-bg-color] rounded-lg py-2 px-4 mb-4">
                            <div class="grow ml-5">Title</div>
                            <div class="w-1/4">Rate</div>
                            <div class="w-[10%] text-center">Action</div>
                        </div>
                        <div id="rate-list" class="space-y-4 h-[250px] overflow-y-auto my-scroller-2">
                            <div class="text-center bg-[--h-bg-color] rounded-lg py-2 px-4">No Rates Added</div>
                        </div>
                    </div>
                    {{-- calc bottom --}}
                    <div id="calc-bottom" class="flex w-full gap-4 text-sm">
                        <div
                            class="total flex justify-between items-center bg-[--h-bg-color] rounded-lg py-2 px-4 w-full">
                            <div class="grow ml-5">Total - Rs.</div>
                            <div class="w-1/4">0.00</div>
                        </div>
                        <div
                            class="final flex justify-between items-center bg-[--h-bg-color] rounded-lg py-2 px-4 w-full">
                            <div class="text-nowrap grow">Sales Rate - Rs.</div>
                            <input type="text" name="sales_rate" id="sales_rate" value="0.00"
                                class="text-right bg-transparent outline-none border-none" />
                        </div>
                    </div>
                    <input type="hidden" name="rates_array" id="rates_array" value="[]" />
                </div>
            </div>

            <!-- Step 3: Image -->
            <div class="step3 hidden space-y-4">
                <x-image-upload 
                    id="image_upload"
                    name="image_upload"
                    placeholder="{{ asset('images/image_icon.png') }}"
                    uploadText="Upload article image"
                />
            </div>
        </form>

        <div
            class="bg-[--secondary-bg-color] rounded-xl shadow-xl p-8 border border-[--h-bg-color] w-[35%] pt-12 relative overflow-hidden fade-in">
            <div
                class="form-title text-center absolute top-0 left-0 w-full bg-[--primary-color] py-1 shadow-lg uppercase font-semibold">
                <h4>Last Record</h4>
            </div>

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
                            value="{{ $lastRecord->date }}" 
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
                    <div class="text-center text-xs text-[--border-error]">No records found</div>
                @endif
            </div>

            <!-- Step 2: Production Details -->
            <div class="step2 hidden space-y-6  h-full flex flex-col">
                @if ($lastRecord)
                    <div class="w-full text-left grow">
                        <div class="flex justify-between items-center bg-[--h-bg-color] rounded-lg py-2 px-4 mb-4">
                            <div class="grow ml-5">Title</div>
                            <div class="w-1/4">Rate</div>
                        </div>
                        <div id="rate-list" class="space-y-4 h-[250px] overflow-y-auto my-scroller-2">
                            @if (count($lastRecord->rates_array) === 0)
                                <div class="text-center bg-[--h-bg-color] rounded-lg py-2 px-4">No Rates Added
                                </div>
                            @else
                                @foreach ($lastRecord->rates_array as $rate)
                                    @php
                                        $lastRecord->total_rate += $rate['rate'];
                                    @endphp
                                    <div
                                        class="flex justify-between items-center bg-[--h-bg-color] rounded-lg py-2 px-4">
                                        <div class="grow ml-5">{{ $rate['title'] }}</div>
                                        <div class="w-1/4">{{ number_format($rate['rate'], 2, '.', '') }}</div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-col w-full gap-4">
                        <div
                            class="flex justify-between items-center bg-[--h-bg-color] rounded-lg py-2 px-4 w-full">
                            <div class="grow">Total - Rs.</div>
                            <div class="w-1/4 text-right">{{ number_format($lastRecord->total_rate, 2, '.', '') }}
                            </div>
                        </div>
                        <div
                            class="flex justify-between items-center bg-[--h-bg-color] rounded-lg py-2 px-4 w-full">
                            <div class="text-nowrap grow">Sales Rate - Rs.</div>
                            <div class="w-1/4 text-right">{{ number_format($lastRecord->sales_rate, 2, '.', '') }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center text-xs text-[--border-error]">No records found</div>
                @endif
            </div>

            <!-- Step 3: Production Details -->
            <div class="step3 hidden space-y-6  text-sm">
                @if ($lastRecord)
                    <div class="grid grid-cols-1 md:grid-cols-1">
                        @if ($lastRecord->image == 'no_image_icon.png')
                            <x-image-upload 
                                id="image_upload"
                                name="image_upload"
                                placeholder="{{ asset('images/no_image_icon.png') }}"
                                uploadText="Image"
                            />
                        @else
                            <x-image-upload 
                                id="image_upload"
                                name="image_upload"
                                placeholder="{{ asset('storage/uploads/images/' . $lastRecord->image) }}"
                                uploadText="Image"
                            />
                        @endif
                    </div>
                @else
                    <div class="text-center text-xs text-[--border-error]">No records found</div>
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
                rateRow.classList.add('flex', 'justify-between', 'items-center', 'bg-[--h-bg-color]', 'rounded-lg', 'py-2',
                    'px-4');
                rateRow.innerHTML = `
                    <div class="grow ml-5">${title}</div>
                    <div class="w-1/4">${parseFloat(rate).toFixed(2)}</div>
                    <div class="w-[10%] text-center">
                        <button onclick="deleteRate(this)" type="button" class="text-[--danger-color] text-xs px-2 py-1 rounded-lg hover:text-[--h-danger-color] transition-all duration-300 ease-in-out">
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
                    <div class="text-center bg-[--h-bg-color] rounded-lg py-2 px-4">No Rates Added</div>
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
                <div class="total flex justify-between items-center bg-[--h-bg-color] rounded-lg py-2 px-4 w-full">
                    <div class="grow ml-5">Total - Rs.</div>
                    <div class="w-1/4">${totalRate.toFixed(2)}</div>
                </div>
                <div class="final flex justify-between items-center bg-[--h-bg-color] rounded-lg py-2 px-4 w-full">
                    <div class="text-nowrap grow">Sales Rate - Rs.</div>
                    <input type="text" name="sales_rate" id="sales_rate" value="${totalRate.toFixed(2)}" class="text-right bg-transparent outline-none border-none"/>
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
        const categoryDom = document.getElementById('category');
        const categoryError = document.getElementById('category-error');
        const sizeDom = document.getElementById('size');
        const sizeError = document.getElementById('size-error');
        const seasonDom = document.getElementById('season');
        const seasonError = document.getElementById('season-error');
        const quantityDom = document.getElementById('quantity');
        const quantityError = document.getElementById('quantity-error');
        const extraPcsDom = document.getElementById('extra_pcs');
        const extraPcsError = document.getElementById('extra_pcs-error');
        const fabricTyprDom = document.getElementById('fabric_type');
        const fabricTyprError = document.getElementById('fabric_type-error');

        function validateArticleNo() {
            let articleNoValue = parseFloat(articleNoDom.value);
            let existingArticle = articles.some(a => a.article_no === articleNoValue)

            if (!articleNoValue) {
                articleNoDom.classList.add("border-[--border-error]");
                articleNoError.classList.remove("hidden");
                articleNoError.textContent = "Article No field is required.";
                return false;
            } else if (existingArticle) {
                articleNoDom.classList.add("border-[--border-error]");
                articleNoError.classList.remove("hidden");
                articleNoError.textContent = "Article No is already exist.";
                return false;
            } else {
                articleNoDom.classList.remove("border-[--border-error]");
                articleNoError.classList.add("hidden");
                return true;
            }
        }

        function validateDate() {
            if (dateDom.value === "") {
                dateDom.classList.add("border-[--border-error]");
                dateError.classList.remove("hidden");
                dateError.textContent = "Date field is required.";
                return false;
            } else {
                dateDom.classList.remove("border-[--border-error]");
                dateError.classList.add("hidden");
                return true;
            }
        }

        function validateCategory() {
            if (categoryDom.value === "") {
                categoryDom.classList.add("border-[--border-error]");
                categoryError.classList.remove("hidden");
                categoryError.textContent = "Category field is required.";
                return false;
            } else {
                categoryDom.classList.remove("border-[--border-error]");
                categoryError.classList.add("hidden");
                return true;
            }
        }

        function validateSize() {
            if (sizeDom.value === "") {
                sizeDom.classList.add("border-[--border-error]");
                sizeError.classList.remove("hidden");
                sizeError.textContent = "Size field is required.";
                return false;
            } else {
                sizeDom.classList.remove("border-[--border-error]");
                sizeError.classList.add("hidden");
                return true;
            }
        }

        function validateSeason() {
            if (seasonDom.value === "") {
                seasonDom.classList.add("border-[--border-error]");
                seasonError.classList.remove("hidden");
                seasonError.textContent = "Season field is required.";
                return false;
            } else {
                seasonDom.classList.remove("border-[--border-error]");
                seasonError.classList.add("hidden");
                return true;
            }
        }

        function validateQuantity() {
            if (quantityDom.value === "") {
                quantityDom.classList.add("border-[--border-error]");
                quantityError.classList.remove("hidden");
                quantityError.textContent = "Quantity field is required.";
                return false;
            } else if (quantityDom.value < 0) {
                quantityDom.classList.add("border-[--border-error]");
                quantityError.classList.remove("hidden");
                quantityError.textContent = "Quantity is lessthen 0.";
                return false;
            } else {
                quantityDom.classList.remove("border-[--border-error]");
                quantityError.classList.add("hidden");
                return true;
            }
        }

        function validateExtraPcs() {
            if (extraPcsDom.value === "") {
                extraPcsDom.classList.add("border-[--border-error]");
                extraPcsError.classList.remove("hidden");
                extraPcsError.textContent = "Extra Pcs field is required.";
                return false;
            } else {
                extraPcsDom.classList.remove("border-[--border-error]");
                extraPcsError.classList.add("hidden");
                return true;
            }
        }

        function validateFabricType() {
            if (fabricTyprDom.value === "") {
                fabricTyprDom.classList.add("border-[--border-error]");
                fabricTyprError.classList.remove("hidden");
                fabricTyprError.textContent = "Quantity field is required.";
                return false;
            } else {
                fabricTyprDom.classList.remove("border-[--border-error]");
                fabricTyprError.classList.add("hidden");
                return true;
            }
        }

        articleNoDom.addEventListener("input", validateArticleNo);
        dateDom.addEventListener("change", validateDate);
        categoryDom.addEventListener("input", validateCategory);
        sizeDom.addEventListener("input", validateSize);
        seasonDom.addEventListener("input", validateSeason);
        quantityDom.addEventListener("input", validateQuantity);
        extraPcsDom.addEventListener("input", validateExtraPcs);
        fabricTyprDom.addEventListener("input", validateFabricType);

        function validateForNextStep() {
            let isValidArticleNo = validateArticleNo();
            let isValidDate = validateDate();
            let isValidCategory = validateCategory();
            let isValidSize = validateSize();
            let isValidSeason = validateSeason();
            let isValidQuantity = validateQuantity();
            let isValidExtraPcs = validateExtraPcs();
            let isValidFabricType = validateFabricType();

            let isValid = isValidArticleNo || isValidDate || isValidCategory || isValidSize || isValidSeason || isValidQuantity || isValidExtraPcs || isValidFabricType;

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

<!-- Step 1: Basic Information -->
{{-- <div class="step1 space-y-4 ">
    @if ($lastRecord)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-group">
                <h3 class="block text-xs font-medium text-[--secondary-text] mb-1">Article No</h3>
                <input disabled
                    class="w-full bg-transparent rounded-lg border-gray-600 text-[--text-color] text-sm px-3 py-2 border focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300 ease-in-out"
                    value="{{ $lastRecord->article_no }}" />
            </div>
            <div class="form-group">
                <h3 class="block text-xs font-medium text-[--secondary-text] mb-1">Date</h3>
                <input disabled
                    class="w-full bg-transparent rounded-lg border-gray-600 text-[--text-color] text-sm px-3 py-2 border focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300 ease-in-out"
                    value="{{ $lastRecord->date }}" />
            </div>
            <div class="form-group">
                <h3 class="block text-xs font-medium text-[--secondary-text] mb-1">Category</h3>
                <input disabled
                    class="w-full bg-transparent rounded-lg border-gray-600 text-[--text-color] text-sm px-3 py-2 border focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300 ease-in-out"
                    value="{{ $lastRecord->category->title }}" />
            </div>
            <div class="form-group">
                <h3 class="block text-xs font-medium text-[--secondary-text] mb-1">Size</h3>
                <input disabled
                    class="w-full bg-transparent rounded-lg border-gray-600 text-[--text-color] text-sm px-3 py-2 border focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300 ease-in-out"
                    value="{{ $lastRecord->size->title }}" />
            </div>
            <div class="form-group">
                <h3 class="block text-xs font-medium text-[--secondary-text] mb-1">Season</h3>
                <input disabled
                    class="w-full bg-transparent rounded-lg border-gray-600 text-[--text-color] text-sm px-3 py-2 border focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300 ease-in-out"
                    value="{{ $lastRecord->season->title }}" />
            </div>
            <div class="form-group">
                <h3 class="block text-xs font-medium text-[--secondary-text] mb-1">Quantity-Dz</h3>
                <input disabled
                    class="w-full bg-transparent rounded-lg border-gray-600 text-[--text-color] text-sm px-3 py-2 border focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300 ease-in-out"
                    value="{{ $lastRecord->quantity }}" />
            </div>
            <div class="form-group">
                <h3 class="block text-xs font-medium text-[--secondary-text] mb-1">Extra Pcs</h3>
                <input disabled
                    class="w-full bg-transparent rounded-lg border-gray-600 text-[--text-color] text-sm px-3 py-2 border focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300 ease-in-out"
                    value="{{ $lastRecord->extra_pcs }}" />
            </div>
            <div class="form-group">
                <h3 class="block text-xs font-medium text-[--secondary-text] mb-1">Fabric Type</h3>
                <input disabled
                    class="w-full bg-transparent rounded-lg border-gray-600 text-[--text-color] text-sm px-3 py-2 border focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300 ease-in-out text-nowrap overflow-x-auto"
                    value="{{ $lastRecord->fabric_type }}" />
            </div>
        </div>
    @else
        <div class="text-center text-xs text-[--border-error]">No records found</div>
    @endif
</div>

<!-- Step 2: Production Details -->
<div class="step2 hidden space-y-6  h-full text-sm flex flex-col">
    @if ($lastRecord)
        <div class="w-full text-left grow">
            <div class="flex justify-between items-center bg-[--h-bg-color] rounded-lg py-2 px-4 mb-4">
                <div class="grow ml-5">Title</div>
                <div class="w-1/4">Rate</div>
            </div>
            <div id="rate-list" class="space-y-4 h-[250px] overflow-y-auto my-scroller-2">
                @if (count($lastRecord->rates_array) === 0)
                    <div class="text-center bg-[--h-bg-color] rounded-lg py-2 px-4">No Rates Added
                    </div>
                @else
                    @foreach ($lastRecord->rates_array as $rate)
                        @php
                            $lastRecord->total_rate += $rate['rate'];
                        @endphp
                        <div
                            class="flex justify-between items-center bg-[--h-bg-color] rounded-lg py-2 px-4">
                            <div class="grow ml-5">{{ $rate['title'] }}</div>
                            <div class="w-1/4">{{ number_format($rate['rate'], 2, '.', '') }}</div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
        <div class="flex flex-col w-full gap-4">
            <div
                class="flex justify-between items-center bg-[--h-bg-color] rounded-lg py-2 px-4 w-full">
                <div class="grow">Total - Rs.</div>
                <div class="w-1/4 text-right">{{ number_format($lastRecord->total_rate, 2, '.', '') }}
                </div>
            </div>
            <div
                class="flex justify-between items-center bg-[--h-bg-color] rounded-lg py-2 px-4 w-full">
                <div class="text-nowrap grow">Sales Rate - Rs.</div>
                <div class="w-1/4 text-right">{{ number_format($lastRecord->sales_rate, 2, '.', '') }}
                </div>
            </div>
        </div>
    @else
        <div class="text-center text-xs text-[--border-error]">No records found</div>
    @endif
</div>

<!-- Step 3: Production Details -->
<div class="step3 hidden space-y-6  text-sm">
    @if ($lastRecord)
        <div class="grid grid-cols-1 md:grid-cols-1">
            <div
                class="border-dashed border-2 border-gray-300 rounded-lg p-6 flex flex-col items-center justify-center cursor-pointer hover:border-primary transition-all duration-300 ease-in-out">
                <input id="image_upload" type="file" name="image_upload" accept="image/*"
                    class="hidden" onchange="previewImage(event)" />
                <div id="image_preview" class="flex flex-col items-center max-w-[50%]">
                    <img src="{{ asset('storage/uploads/images/' . $lastRecord->image) }}"
                        alt="Last Image" class="placeholder_icon mb-2 rounded-lg w-full h-auto" />
                    <p class="upload_text text-md text-gray-500">Image</p>
                </div>
            </div>
        </div>
    @else
        <div class="text-center text-xs text-[--border-error]">No records found</div>
    @endif
</div> --}}
