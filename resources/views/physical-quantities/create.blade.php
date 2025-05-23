@extends('app')
@section('title', 'Add Physical Quantities | ' . app('company')->name)
@section('content')
@php
    $category_options = [
        'a' => ['text'  => 'A'],
        'b' => ['text'  => 'B'],
];
@endphp
    <!-- Modal -->
    <div id="articleModal"
        class="hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-[var(--overlay-color)] fade-in">
    </div>
    <!-- Main Content -->

    <div class="max-w-5xl mx-auto">
        <x-search-header heading="Add Physical Quantity" link linkText="Show Physical Quantities" linkHref="{{ route('physical-quantities.index') }}"/>
    </div>

    <!-- Form -->
    <form id="form" action="{{ route('physical-quantities.store') }}" method="post"
        class="bg-[var(--secondary-bg-color)] text-sm rounded-xl shadow-lg p-8 border border-[var(--h-bg-color)] pt-12 max-w-5xl mx-auto  relative overflow-hidden">
        @csrf
        <x-form-title-bar title="Add Physical Quantity" />

        <div class="space-y-4 ">
            <div class="flex justify-between gap-4">
                {{-- article --}}
                <div class="grow">
                    <x-input label="Article" id="article" placeholder='Select Article' class="cursor-pointer" withImg imgUrl="" readonly required />
                    <input type="hidden" name="article_id" id="article_id" value="" />
                </div>

                {{-- date --}}
                <div class="w-1/4">
                    <x-input label="Date" name="date" id="date" type="date" max="{{ Now()->toDateString() }}" value="{{ now()->toDateString() }}" required />
                </div>

                {{-- processed_by --}}
                <div class="w-1/4">
                    <x-input label="Processed By" name="processed_by" id="processed_by" placeholder="Enter processed by" required />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-3 gap-4">
                {{-- pcs_per_packet  --}}
                <x-input label="Maste Unit" name="pcs_per_packet" id="pcs_per_packet" type="number" placeholder="Enter pcs. count per packet" required />

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
        <div class="w-full flex justify-end mt-4">
            <button type="submit"
                class="px-6 py-1 bg-[var(--bg-success)] border border-[var(--bg-success)] text-[var(--text-success)] font-medium text-nowrap rounded-lg hover:bg-[var(--h-bg-success)] transition-all 0.3s ease-in-out cursor-pointer">
                <i class='fas fa-save mr-1'></i> Save
            </button>
        </div>
    </form>

    <script>
        const articleModalDom = document.getElementById("articleModal");
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

        let isModalOpened = false;

        let totalQuantity = 0;
        let totalAmount = 0;

        articleSelectInputDOM.addEventListener('click', () => {
            generateArticlesModal();
        })

        function generateArticlesModal() {
            articleModalDom.innerHTML = `
                <x-modal id="articlesModalForm" classForBody="p-5 max-w-6xl h-[45rem]" closeAction="closeArticlesModal">
                    <!-- Modal Content Slot -->
                    <div class="flex items-start relative h-full">
                        <div class="flex-1 h-full overflow-y-auto my-scrollbar-2 flex flex-col">
                            <h5 id="name" class="text-2xl my-1 text-[var(--text-color)] capitalize font-semibold">Articles</h5>
                            
                            <hr class="border-gray-600 my-3">
                
                            @if (count($articles) > 0)
                                <div class='overflow-y-auto my-scrollbar-2 pt-2 grow'>
                                    <div class="card_container grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                                        @foreach ($articles as $article)
                                            <div data-json='{{ $article }}' id='{{ $article->id }}' onclick='selectThisArticle(this)'
                                                class="contextMenuToggle modalToggle card relative border border-gray-600 shadow rounded-xl min-w-[100px] h-[8rem] flex gap-4 p-2 cursor-pointer overflow-hidden fade-in">
                                                <x-card :data="[
                                                    'image' => $article->image == 'no_image_icon.png' 
                                                        ? asset('images/no_image_icon.png') 
                                                        : asset('storage/uploads/images/' . $article->image),
                                                    'status' => $article->sales_rate == '0.00' ? 'no_rate' : 'transparent',
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
                                <div class="text-[var(--border-error)] text-center font-medium h-full col-span-full">Data Not Found</div>
                            @endif
                        </div>
                    </div>
                </x-modal>
            `;

            openArticlesModal();
        }

        function openArticlesModal() {
            isModalOpened = true;
            closeAllDropdowns();
            document.getElementById('articleModal').classList.remove('hidden');
        }

        function closeArticlesModal() {
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

        document.addEventListener('mousedown', (e) => {
            const {
                id
            } = e.target;
            if (id === 'articlesModalForm') {
                closeArticlesModal();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && isModalOpened) {
                closeArticlesModal();
            }
        });

        let selectedArticle = null;

        function selectThisArticle(articleElem) {
            selectedArticle = JSON.parse(articleElem.getAttribute('data-json'));

            articleIdInputDOM.value = selectedArticle.id;
            let value = `${selectedArticle.article_no} | ${selectedArticle.season} | ${selectedArticle.size} | ${selectedArticle.category} | ${formatNumbersDigitLess(selectedArticle.quantity)} (pcs) | Rs. ${formatNumbersWithDigits(selectedArticle.sales_rate, 1, 1)}`;
            articleSelectInputDOM.value = value;
            
            articleImageShowDOM.classList.remove('opacity-0');
            articleImageShowDOM.src = articleElem.querySelector('img').src
            
            closeArticlesModal();
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

        document.getElementById('pcs_per_packet').addEventListener('input', () => {
            calculateTotal();
            trackArticleQuantity();
        });

        document.getElementById('packets').addEventListener('input', () => {
            calculateTotal();
            trackArticleQuantity();
        });

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

        function validateForNextStep() {
            return true;
        }
    </script>
@endsection
