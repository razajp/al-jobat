@extends('app')
@section('title', 'Add Physical Quantities | ' . app('company')->name)
@section('content')
    <!-- Modal -->
    <div id="articleModal"
        class="hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-black bg-opacity-50 fade-in">
    </div>
    <!-- Main Content -->
    <h1 class="text-3xl font-bold mb-6 text-center text-[--primary-color] fade-in"> Add Physical Quantity </h1>

    <!-- Form -->
    <form id="form" action="{{ route('physical-quantities.store') }}" method="post"
        class="bg-[--secondary-bg-color] text-sm rounded-xl shadow-lg p-8 border border-[--h-bg-color] pt-12 max-w-4xl mx-auto  relative overflow-hidden">
        @csrf
        <div
            class="form-title text-center absolute top-0 left-0 w-full bg-[--primary-color] py-1 capitalize tracking-wide font-medium text-sm">
            <h4>Add Physical Quantity</h4>
        </div>

        <div class="space-y-4 ">
            <div class="flex justify-between gap-4">
                {{-- article --}}
                <div class="grow">
                    <x-input label="Article" id="article" placeholder='Select Article' class="cursor-pointer" withImg imgUrl="" readonly required />
                    <input type="hidden" name="article_id" id="article_id" value="" />
                </div>

                {{-- date --}}
                <div class="w-1/3">
                    <x-input label="Date" name="date" id="date" type="date" required />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 gap-4">
                {{-- pcs_per_packet  --}}
                <div>
                    <x-input label="Pcs./packet" name="pcs_per_packet" id="pcs_per_packet" type="number" placeholder="Enter pcs. count per packet" required />
                </div>

                {{-- packets --}}
                <div>
                    <x-input label="Packets" name="packets" id="packets" type="number" placeholder="Enter packet count" required />
                </div>
            </div>

            <hr class="border-gray-600 my-3">

            <div class="flex w-full gap-4 text-sm mt-5 items-start">
                <div class="first w-full">
                    <div class="current-phys-qty flex justify-between items-center bg-[--h-bg-color] rounded-lg py-2 px-4">
                        <div class="grow">Total Physical Stock - Pcs</div>
                        <div id="currentPhysicalQuantity">0</div>
                    </div>
                </div>
                <div class="second w-full">
                    <div class="total-qty flex justify-between items-center bg-[--h-bg-color] rounded-lg py-2 px-4">
                        <div class="grow">Total Quantity - Pcs</div>
                        <div id="finalOrderedQuantity">0</div>
                    </div>
                    <div id="total-qty-error" class="text-[--border-error] text-xs mt-1 hidden transition-all 0.3s ease-in-out"></div>
                </div>
                <div class="thered w-full">
                    <div class="final flex justify-between items-center bg-[--h-bg-color] rounded-lg py-2 px-4">
                        <div class="grow">Total Amount - Rs.</div>
                        <div id="finalOrderAmount">0.0</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-full flex justify-end mt-4">
            <button type="submit"
                class="px-6 py-1 bg-[--bg-success] border border-[--bg-success] text-[--text-success] font-medium text-nowrap rounded-lg hover:bg-[--h-bg-success] transition-all 0.3s ease-in-out">
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
        const packetsDom = document.getElementById('packets');

        const totalPhysicalQuantityDom = document.getElementById('currentPhysicalQuantity');
        const finalOrderedQuantityDom = document.getElementById('finalOrderedQuantity');
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
                        <div class="flex-1 h-full overflow-y-auto my-scroller-2 flex flex-col">
                            <h5 id="name" class="text-2xl my-1 text-[--text-color] capitalize font-semibold">Articles</h5>
                            
                            <hr class="border-gray-600 my-3">
                
                            @if (count($articles) > 0)
                                <div class='overflow-y-auto my-scroller-2 pt-2 grow'>
                                    <div class="card_container grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                                        @foreach ($articles as $article)
                                            <div data-json='{{ $article }}' id='{{ $article->id }}' onclick='selectThisArticle(this)'
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
                        </div>
                    </div>
                </x-modal>
            `;

            openArticlesModal();

            // if (selectedArticles.length > 0) {
            //     selectedArticles.forEach(selectedArticle => {
            //         let card = document.getElementById(selectedArticle.id);
            //         card.innerHTML += `
            //             <div
            //                 class="quantity-label absolute text-xs text-[--border-success] top-1 right-2 h-[1rem]">
            //                 ${selectedArticle.orderedQuantity} Pcs
            //             </div>
            //         `;
            //     });
            // }
        }

        function openArticlesModal() {
            isModalOpened = true;
            closeAllDropdowns();
            document.getElementById('articleModal').classList.remove('hidden');
        };

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
            let value = `#${selectedArticle.article_no} | ${selectedArticle.season} | ${selectedArticle.size} | ${selectedArticle.category} | ${selectedArticle.quantity} (pcs) | Rs. ${selectedArticle.sales_rate}`;
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
                };

                return `${year}-${monthMap[month]}-${day.padStart(2, '0')}`;
            }

            document.getElementById('date').min = formatArticleDate(selectedArticle.date);
            
            
            if (selectedArticle.pcs_per_packet > 0) {
                pcsPerPacketDom.readOnly = true;
                pcsPerPacketDom.value = selectedArticle.pcs_per_packet;
            } else {
                pcsPerPacketDom.readOnly = false;
                pcsPerPacketDom.value = '';
            }
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
            } else {
                pcsPerPacketDom.disabled = false;
                packetsDom.disabled = false;
            }
        }
        trackFieldsDisability();

        function calculateTotal() {
            if (selectedArticle) {
                let pcsPerPacket = pcsPerPacketDom.value;
                let packets = packetsDom.value;

                totalQuantity = pcsPerPacket * packets;
                totalAmount = totalQuantity * parseInt(selectedArticle.sales_rate);

                finalOrderedQuantityDom.innerText = new Intl.NumberFormat('en-US').format(totalQuantity);

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
                totalQtyDom.classList.add('border', 'border-[--border-error]');
                totalQtyErrorDom.innerText = `Quantity exceeds the available stock (${selectedArticle.quantity} pcs)`;
                totalQtyErrorDom.classList.remove('hidden');
            } else {
                totalQtyDom.classList.remove('border', 'border-[--border-error]');
                totalQtyErrorDom.classList.add('hidden');
                totalQtyErrorDom.innerText = '';
            }
        }

        function validateForNextStep() {
            return true;
        }
    </script>
@endsection
