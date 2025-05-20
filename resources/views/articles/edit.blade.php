@extends('app')
@section('title', 'Edit Article | ' . app('company')->name)
@section('content')
    <!-- Main Content -->
    <!-- Progress Bar -->
    <div class="mb-5 max-w-3xl mx-auto">
        <x-search-header heading="Edit Article" link linkText="Show Articles" linkHref="{{ route('articles.index') }}"/>
        <x-progress-bar 
            :steps="['Enter Details', 'Enter Rates', 'Upload Image']" 
            :currentStep="1"
        />
    </div>

    <div class="row max-w-3xl mx-auto flex gap-4">
        <!-- Form -->
        <form id="form" action="{{ route('articles.update', ['article' => $article->id]) }}" method="POST" enctype="multipart/form-data"
            class="bg-[var(--secondary-bg-color)] text-sm rounded-xl shadow-lg p-8 border border-[var(--h-bg-color)] pt-12 grow relative overflow-hidden">
            @csrf
            @method('PUT')
            <div
                class="form-title text-center absolute top-0 left-0 w-full bg-[var(--primary-color)] py-1 capitalize tracking-wide font-medium text-sm">
                <h4>Edit Article</h4>
            </div>
            <!-- Step 1: Basic Information -->
            <div class="step1 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- article_no -->
                    <x-input 
                        label="Article No"
                        value="{{ $article->article_no }}"
                        disabled
                    />
                    <input type="hidden" name="article_no" id="article_no" value="{{ $article->article_no }}" />
                    
                    <!-- date -->
                    <x-input 
                        label="Date"
                        value="{{ $article->date }}"
                        disabled
                    />
                    <input type="hidden" name="date" id="date" value="{{ $article->date }}" />
                    
                    <x-input 
                        label="Category"
                        name="category" 
                        id="category" 
                        type="text" 
                        value="{{ $article->category }}"
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
                        value="{{ $article->size }}"
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
                        value="{{ $article->season }}"
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
                        value="{{ $article->quantity }}"
                        placeholder="Enter quantity" 
                        required 
                    />
                    
                    {{-- extra_pcs --}}
                    <x-input 
                        label="Extra Pcs"
                        name="extra_pcs" 
                        id="extra_pcs" 
                        type="number"
                        value="{{ $article->extra_pcs }}"
                        placeholder="Enter extra pcs" 
                        required 
                    />

                    {{-- fabric_type --}}
                    <x-input 
                        label="Fabric Type" 
                        name="fabric_type" 
                        id="fabric_type" 
                        type="text"
                        value="{{ $article->fabric_type }}"
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
                        @if(is_array($article->rates_array) && count($article->rates_array) > 0)
                            <div id="rate-list" class="space-y-4 h-[250px] overflow-y-auto my-scrollbar-2">
                                @php
                                    $article->totalRate = 0.00;
                                @endphp
                                @foreach ($article->rates_array as $rate)
                                    @php
                                        $article->totalRate += number_format($rate['rate'], 2);
                                    @endphp
                                    <div class="flex justify-between items-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4">
                                        <div class="grow ml-5">{{ $rate['title'] }}</div>
                                        <div class="w-1/4">{{ number_format($rate['rate'], 2) }}</div>
                                        <div class="w-[10%] text-center">
                                            <button onclick="deleteRate(this)" type="button"
                                                class="text-[var(--danger-color)] text-xs px-2 py-1 rounded-lg hover:text-[var(--h-danger-color)] transition-all duration-300 ease-in-out cursor-pointer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div id="rate-list" class="space-y-4 h-[250px] overflow-y-auto my-scrollbar-2">
                                <div class="text-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4">No Rates Added</div>
                            </div>
                        @endif
                    </div>
                    {{-- calc bottom --}}
                    <div id="calc-bottom" class="flex w-full gap-4 text-sm">
                        <div
                            class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full cursor-not-allowed">
                            <div>Total - Rs.</div>
                            <div class="text-right">{{ number_format($article->totalRate, 2) }}</div>
                        </div>
                        <div
                            class="final flex justify-between items-center bg-[var(--h-bg-color)] border border-gray-600 rounded-lg py-2 px-4 w-full">
                            <label for="sales_rate" class="text-nowrap grow">Sales Rate - Rs.</label>
                            <input type="text" required name="sales_rate" id="sales_rate" value="{{ number_format($article->sales_rate, 2) }}"
                                class="text-right bg-transparent outline-none border-none w-[50%]" />
                        </div>
                    </div>
                    @if(is_array($article->rates_array) && count($article->rates_array) > 0)
                        <input type="hidden" name="rates_array" id="rates_array" value="{{ json_encode($article->rates_array) }}" />
                    @else
                        <input type="hidden" name="rates_array" id="rates_array" value="[]" />
                    @endif
                </div>
            </div>

            <!-- Step 3: Image -->
            <div class="step3 hidden space-y-4">
                @if ($article->image == 'no_image_icon.png')
                    <x-image-upload 
                        id="image_upload"
                        name="image_upload"
                        placeholder="{{ asset('images/image_icon.png') }}"
                        uploadText="Upload article image"
                    />
                @else
                    <x-image-upload 
                        id="image_upload"
                        name="image_upload"
                        placeholder="{{ asset('storage/uploads/images/' . $article->image) }}"
                        uploadText="Preview"
                    />
                    <script>
                        const placeholderIcon = document.querySelector(".placeholder_icon");
                        placeholderIcon.classList.remove("w-16", "h-16");
                        placeholderIcon.classList.add("rounded-md", "w-full", "h-auto");
                    </script>
                @endif
            </div>
        </form>
    </div>

    <script>
        let titleDom = document.getElementById('title');
        let rateDom = document.getElementById('rate');
        let calcBottom = document.querySelector('#calc-bottom');
        let ratesArrayDom = document.getElementById('rates_array');
        let rateCount = 0;

        let totalRate = 0.00;

        let ratesArray = ratesArrayDom.value ? JSON.parse(ratesArrayDom.value) : [];
        if (ratesArray.length > 0) {
            ratesArray.forEach(rate => {
                rateCount++;
                totalRate += parseFloat(rate.rate);
            });
        }

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

        function validateForNextStep() {
            return true;
        }
    </script>
@endsection
