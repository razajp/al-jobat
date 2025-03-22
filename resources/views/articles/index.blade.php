@extends('app')
@section('title', 'Show Articles | ' . app('company')->name)
@section('content')
    <!-- Modals -->
    {{-- article details modal --}}
    <div id="modal"
        class="mainModal hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-black bg-opacity-50 fade-in">
    </div>
    {{-- add image modal --}}
    <div id="addImageModal"
        class="mainModal hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-black bg-opacity-50 fade-in">
    </div>
    {{-- add rate modal --}}
    <div id="addRateModal"
        class="mainModal hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-black bg-opacity-50 fade-in">
    </div>
    
    {{-- header --}}
    <div class="w-[80%] mx-auto">
        <x-search-header heading="Articles" :filter_items="[
            'all' => 'All',
            '#' => 'Article No.',
            'category' => 'Category',
            'season' => 'Season',
            'size' => 'Size',
        ]"/>
    </div>

    <!-- Main Content -->
    <section class="text-center mx-auto ">
        <div
            class="show-box mx-auto w-[80%] h-[70vh] bg-[--secondary-bg-color] rounded-xl shadow overflow-y-auto pt-7 pr-2 relative">
                <div
                    class="form-title text-center absolute top-0 left-0 w-full bg-[--primary-color] py-1 shadow-lg uppercase font-semibold text-sm">
                    <h4>Show Articles</h4>
                </div>

            <div class="buttons absolute {{ $authLayout == 'grid' ? 'top-0' : 'top-0.5' }} right-4 text-sm">
                <div class="relative group">
                    {{-- <form method="POST" action="{{ route('update-user-layout') }}">
                        @csrf
                        <input type="hidden" name="layout" value="{{ $authLayout }}">
                        <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                        @if ($authLayout == 'grid')
                            <button type="submit" class="group cursor-pointer">
                                <i class='bx bx-list-ul text-xl text-white'></i>
                                <span
                                    class="absolute shadow-md text-nowrap border border-gray-600 z-10 -right-1 top-8 bg-[--h-secondary-bg-color] text-[--text-color] text-[12px] rounded px-3 py-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">List</span>
                            </button>
                        @else
                            <button type="submit" class="group cursor-pointer">
                                <i class='bx bx-grid-horizontal text-2xl text-white'></i>
                                <span
                                    class="absolute shadow-md text-nowrap border border-gray-600 z-10 -right-1 top-8 bg-[--h-secondary-bg-color] text-[--text-color] text-[12px] rounded px-3 py-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">Grid</span>
                            </button>
                        @endif
                    </form> --}}
                </div>
            </div>

            @if (count($articles) > 0)
                <div
                    class="add-new-article-btn absolute bottom-8 right-5 hover:scale-105 hover:bottom-9 transition-all group duration-300 ease-in-out z-50">
                    <a href="{{ route('articles.create') }}"
                        class="bg-[--primary-color] text-[--text-color] px-3 py-2 rounded-full hover:bg-[--h-primary-color] transition-all duration-300 ease-in-out"><i
                            class="fas fa-plus"></i></a>
                    <span
                        class="absolute shadow-xl right-7 top-0 border border-gray-600 transform -translate-x-1/2 bg-[--secondary-bg-color] text-[--text-color] text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
                        Add
                    </span>
                </div>
            @endif

            @if (count($articles) > 0)
                <div class="details h-full">
                    <div class="container-parent h-full overflow-y-auto my-scrollbar">
                        <div class="card_container p-5 pr-3 h-full flex flex-col">
                                @if ($authLayout == 'grid')
                                    <div class="search_container grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                                        @foreach ($articles as $article)
                                            <div id="{{ $article->id }}" data-json='{{ $article }}'
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
                                @else
                                    <div class="grid grid-cols-4 bg-[--h-bg-color] rounded-lg font-medium py-2">
                                        <div>Article No.</div>
                                        <div>Season</div>
                                        <div>Size</div>
                                        <div>Category</div>
                                    </div>
                                    <div class="search_container overflow-y-auto grow my-scrollbar-2">
                                        @forEach ($articles as $article)
                                            <div id="{{ $article->id }}" data-json='{{ $article }}' class="contextMenuToggle modalToggle relative group grid text- grid-cols-4 border-b border-[--h-bg-color] items-center py-2 cursor-pointer hover:bg-[--h-secondary-bg-color] transition-all fade-in ease-in-out">
                                                <span>#{{ $article->article_no }}</span>
                                                <span>{{ $article->season }}</span>
                                                <span>{{ $article->size }}</span>
                                                <span>{{ $article->category }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="no-article-message w-full h-full flex flex-col items-center justify-center gap-2">
                    <h1 class="text-sm text-[--secondary-text] capitalize">No Article Found</h1>
                    <a href="{{ route('articles.create') }}"
                        class="text-sm bg-[--primary-color] text-[--text-color] px-4 py-2 rounded-md hover:bg-[--h-primary-color] hover:scale-105 hover:mb-2 transition-all 0.3s ease-in-out font-semibold">Add
                        New</a>
                </div>
            @endif
        </div>

        <div class="context-menu absolute top-0 left-0 text-sm z-50" style="display: none;">
            <div
                class="border border-gray-600 w-48 bg-[--secondary-bg-color] text-[--text-color] shadow-md rounded-xl transform transition-all 0.3s ease-in-out z-50">
                <ul class="p-2">
                    <li>
                        <button id="show-details" type="button"
                            class="w-full px-4 py-2 text-left hover:bg-[--h-bg-color] rounded-md transition-all 0.3s ease-in-out">Show
                            Details</button>
                    </li>
                    <li>
                        <button id="show-details" type="button"
                            class="w-full px-4 py-2 text-left hover:bg-[--h-bg-color] rounded-md transition-all 0.3s ease-in-out">Print
                            Article</button>
                    </li>
                    <li id="add-img-in-context" class="hidden">
                        <button id="add-img-in-context-btn"
                            class="font-medium text-[--border-warning] w-full px-4 py-2 text-left hover:bg-[--bg-warning] hover:text-[--text-warning] rounded-md transition-all 0.3s ease-in-out">Add
                            Image</button>
                    </li>
                    <li id="add-rate-in-context" class="hidden">
                        <button id="add-rate-in-context-btn"
                            class="font-medium text-[--border-success] w-full px-4 py-2 text-left hover:bg-[--bg-success] hover:text-[--text-success] rounded-md transition-all 0.3s ease-in-out">Add
                            Rate</button>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    <script>
        let contextMenu = document.querySelector('.context-menu');
        let addImgInContext = document.getElementById('add-img-in-context');
        let addRateInContext = document.getElementById('add-rate-in-context');
        let isContextMenuOpened = false;

        function closeContextMenu() {
            contextMenu.classList.remove('fade-in');
            contextMenu.style.display = 'none';
            isContextMenuOpened = false;
        };

        function openContextMenu() {
            closeAllDropdowns()
            contextMenu.classList.add('fade-in');
            contextMenu.style.display = 'block';
            isContextMenuOpened = true;
        };

        function addContextMenuListenerToCards() {
            let contextMenuToggle = document.querySelectorAll('.contextMenuToggle');

            contextMenuToggle.forEach(toggle => {
                toggle.addEventListener('contextmenu', (e) => {
                    generateContextMenu(e);
                });
            });
        };

        addContextMenuListenerToCards();

        function generateContextMenu(e) {
            addImgInContext.classList.add('hidden');
            addRateInContext.classList.add('hidden');

            let ac_in_btn_context = document.getElementById('ac_in_btn_context');
            let item = e.target.closest('.modalToggle');
            let data = JSON.parse(item.dataset.json);

            const wrapper = document.querySelector(".wrapper"); // Replace with your wrapper's ID

            if (!contextMenu || !wrapper) return;

            const wrapperRect = wrapper.getBoundingClientRect(); // Get wrapper's position

            let x = e.clientX - wrapperRect.left; // Adjust X relative to wrapper
            let y = e.clientY - wrapperRect.top; // Adjust Y relative to wrapper

            // Prevent right edge overflow
            if (x + contextMenu.offsetWidth > wrapperRect.width) {
                x -= contextMenu.offsetWidth;
            }

            // Prevent bottom edge overflow
            if (y + contextMenu.offsetHeight > wrapperRect.height) {
                y -= contextMenu.offsetHeight;
            }

            contextMenu.style.left = `${x}px`;
            contextMenu.style.top = `${y}px`;

            openContextMenu();

            document.addEventListener('click', (e) => {
                if (e.target.id === "show-details") {
                    generateModal(item);
                };
            });

            document.addEventListener('click', (e) => {
                if (e.target.id === "add-img-in-context-btn") {
                    generateAddImageModal(item);
                };
            });

            document.addEventListener('click', (e) => {
                if (e.target.id === "add-rate-in-context-btn") {
                    generateAddRateModal(item);
                };
            });

            if (data.image === "no_image_icon.png") {
                addImgInContext.classList.remove('hidden');
            };

            if (data.sales_rate === "0.00") {
                addRateInContext.classList.remove('hidden');
            };

            // Function to remove context menu
            const removeContextMenu = (event) => {
                if (!contextMenu.contains(event.target)) {
                    closeContextMenu();
                    document.removeEventListener('click', removeContextMenu);
                    document.removeEventListener('contextmenu', removeContextMenu);
                };
            };

            // Wait for a small delay before attaching event listeners to avoid immediate removal
            setTimeout(() => {
                document.addEventListener('click', removeContextMenu);
            }, 10);
        };

        function generateAddImageModal(item) {
            let modalDom = document.getElementById('addImageModal')
            let article_details_in_modal = document.querySelector('#article_details_in_modal');
            let data = JSON.parse(item.dataset.json);

            modalDom.innerHTML = `
                <x-modal id="addImageModalForm" classForBody="p-5" closeAction="closeAddImageModal" action="{{ route('add-image') }}">
                    <!-- Modal Content Slot -->
                    <div class="flex items-start relative">
                        <div class="flex-1 h-full overflow-y-auto my-scrollbar-2">
                            <h5 id="name" class="text-2xl my-1 text-[--text-color] capitalize font-semibold">Article Details</h5>
                            <x-input 
                                value="#${data.article_no} | ${data.season} | ${data.size} | ${data.category} | ${data.fabric_type} | ${data.quantity} | ${data.sales_rate} - Rs." 
                                disabled
                            />
                            
                            <hr class="border-gray-600 my-3">
                
                            <x-image-upload 
                                id="image_upload"
                                name="image_upload"
                                placeholder="{{ asset('images/image_icon.png') }}"
                                uploadText="Upload article image"
                            />
                        </div>
                    </div>
                
                    <!-- Modal Action Slot -->
                    <x-slot name="actions">
                        <button onclick="closeAddImageModal()" type="button"
                            class="px-4 py-2 bg-[--secondary-bg-color] border border-gray-600 text-[--secondary-text] rounded-lg hover:bg-[--h-bg-color] transition-all duration-300 ease-in-out">
                            Cancel
                        </button>
                        <input type="hidden" id="article_id" name="article_id">
                        <button type="submit"
                            class="px-5 py-2 bg-[--bg-success] border border-[--bg-success] text-[--text-success] font-medium text-nowrap rounded-lg hover:bg-[--h-bg-success] transition-all 0.3s ease-in-out">
                            Add Image
                        </button>
                    </x-slot>
                </x-modal>
            `;

            openAddImageModal();

            document.getElementById('article_id').value = data.id;
            document.getElementById('addImageModal').classList.remove('hidden');
        };

        // rate modal code start
        let titleDom;
        let rateDom;
        let calcBottom;
        let ratesArrayDom;
        let rateCount = 0;

        let totalRate = 0.00;

        let ratesArray = [];

        function generateAddRateModal(item) {
            let modalDom = document.getElementById('addRateModal')
            let article_details_in_modal = document.querySelector('#article_details_in_modal');
            let data = JSON.parse(item.dataset.json);

            modalDom.innerHTML = `
                <x-modal id="addRateModalForm" classForBody="max-w-3xl p-3" closeAction="closeAddRateModal" action="{{ route('add-rate') }}">
                    <!-- Modal Content Slot -->
                    <div class="flex items-start relative">
                        <div class="flex-1 h-full overflow-y-auto my-scrollbar-2 p-2">
                            <h5 id="name" class="text-2xl my-1 text-[--text-color] capitalize font-semibold">Article Details</h5>
                            <x-input 
                                value="#${data.article_no} | ${data.season} | ${data.size} | ${data.category} | ${data.fabric_type} | ${data.quantity} | ${data.sales_rate} - Rs." 
                                disabled
                            />
                            
                            <hr class="border-gray-600 my-3">
                
                            <div class="flex justify-between gap-3">
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
                                <div class="flex justify-between items-center bg-[--h-bg-color] rounded-lg py-2 px-4 my-3">
                                    <div class="grow ml-5">Title</div>
                                    <div class="w-1/4">Rate</div>
                                    <div class="w-[10%] text-center">Action</div>
                                </div>
                                <div id="rate-list" class="space-y-4 h-[250px] overflow-y-auto my-scrollbar-2">
                                    <div class="text-center bg-[--h-bg-color] rounded-lg py-2 px-4">No Rates Added</div>
                                </div>
                            </div>
                            {{-- calc bottom --}}
                            <div id="calc-bottom" class="flex w-full gap-3 text-sm">
                                <div
                                    class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full cursor-not-allowed">
                                    <div>Total - Rs.</div>
                                    <div class="text-right">0.00</div>
                                </div>
                                <div
                                    class="final flex justify-between items-center bg-[--h-bg-color] border border-gray-600 rounded-lg py-2 px-4 w-full">
                                    <label for="sales_rate" class="text-nowrap grow">Sales Rate - Rs.</label>
                                    <input type="text" required name="sales_rate" id="sales_rate" value="0.00"
                                        class="text-right bg-transparent outline-none border-none w-[50%]" />
                                </div>
                                <div
                                    class="final flex justify-between items-center bg-[--h-bg-color] border border-gray-600 rounded-lg py-2 px-4 w-full">
                                    <label for="pcs_per_packet" class="text-nowrap grow">Pcs / Packet</label>
                                    <input type="text" required name="pcs_per_packet" id="pcs_per_packet" value="0"
                                        class="text-right bg-transparent outline-none border-none w-[50%]" />
                                </div>
                            </div>
                            <input type="hidden" name="rates_array" id="rates_array" value="[]" />
                        </div>
                    </div>
                
                    <!-- Modal Action Slot -->
                    <x-slot name="actions">
                        <button onclick="closeAddRateModal()" type="button"
                            class="px-4 py-2 bg-[--secondary-bg-color] border border-gray-600 text-[--secondary-text] rounded-lg hover:bg-[--h-bg-color] transition-all duration-300 ease-in-out">
                            Cancel
                        </button>
                        <input type="hidden" id="article_id" name="article_id">
                        <button type="submit"
                            class="px-5 py-2 bg-[--bg-success] border border-[--bg-success] text-[--text-success] font-medium text-nowrap rounded-lg hover:bg-[--h-bg-success] transition-all 0.3s ease-in-out">
                            Add Rate
                        </button>
                    </x-slot>
                </x-modal>
            `;

            titleDom = document.getElementById('title');
            rateDom = document.getElementById('rate');
            calcBottom = document.querySelector('#calc-bottom');
            ratesArrayDom = document.getElementById('rates_array');
            
            openAddRateModal();
            addListenerToRateDom();

            document.getElementById('article_id').value = data.id;
            document.getElementById('addRateModal').classList.remove('hidden');
        };

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
            pcsPerPacket = document.getElementById('pcs_per_packet').value;
            calcBottom.innerHTML = `
                <div
                    class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full cursor-not-allowed">
                    <div>Total - Rs.</div>
                    <div class="text-right">${totalRate.toFixed(2)}</div>
                </div>
                <div
                    class="final flex justify-between items-center bg-[--h-bg-color] border border-gray-600 rounded-lg py-2 px-4 w-full">
                    <label for="sales_rate" class="text-nowrap grow">Sales Rate - Rs.</label>
                    <input type="text" required name="sales_rate" id="sales_rate" value="${totalRate.toFixed(2)}"
                        class="text-right bg-transparent outline-none border-none w-[50%]" />
                </div>
                <div
                    class="final flex justify-between items-center bg-[--h-bg-color] border border-gray-600 rounded-lg py-2 px-4 w-full">
                    <label for="pcs_per_packet" class="text-nowrap grow">Pcs / Packet</label>
                    <input type="text" required name="pcs_per_packet" id="pcs_per_packet" value="${pcsPerPacket}"
                        class="text-right bg-transparent outline-none border-none w-[50%]" />
                </div>
            `;

            ratesArrayDom.value = JSON.stringify(ratesArray);
        }

        function addListenerToRateDom() {
            rateDom.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    addRate();
                }
            });
        }
        // rate modal code end

        const close = document.querySelectorAll('#close');

        let isModalOpened = false;
        let isAddImageModalOpened = false;
        let isAddRateModalOpened = false;

        close.forEach(function(btn) {
            btn.addEventListener("click", (e) => {
                let targetedModal = e.target.closest(".mainModal")
                if (targetedModal.id == 'modal') {
                    if (isModalOpened) {
                        closeModal();
                    }
                } else if (targetedModal.id == 'addImageModal') {
                    if (isAddImageModalOpened) {
                        closeAddImageModal();
                    }
                } else if (targetedModal.id == 'addRateModal') {
                    if (isAddRateModalOpened) {
                        closeAddRateModal();
                    }
                }
            });
        });
        
        document.addEventListener('click', (e) => {
            const { id } = e.target;
            if (id === 'modalForm') {
                closeModal();
            } else if (id === 'addImageModalForm') {
                closeAddImageModal();
            } else if (id === 'addRateModalForm') {
                closeAddRateModal();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                if (isModalOpened == true) {
                    closeModal();
                }
                if (isAddImageModalOpened == true) {
                    closeAddImageModal();
                };
                if (isAddRateModalOpened == true) {
                    closeAddRateModal();
                };
                closeContextMenu();
            };
        });

        function addListenerToCards() {
            let card = document.querySelectorAll('.modalToggle');

            card.forEach(item => {
                item.addEventListener('click', () => {
                    if (!isContextMenuOpened) {
                        generateModal(item);
                    };
                });
            });
        };

        function generateModal(item) {
            let modalDom = document.getElementById('modal')
            let data = JSON.parse(item.dataset.json);

            modalDom.innerHTML = `
                <x-modal id="modalForm" classForBody="p-5 max-w-5xl" closeAction="closeModal" action="{{ route('update-user-status') }}">
                    <!-- Modal Content Slot -->
                    <div class="flex items-start relative h-[27rem]">
                        <div id="no_image_dot_modal"
                            class="image_dot absolute top-2 left-2 w-[0.7rem] h-[0.7rem] bg-transparent rounded-full">
                        </div>
                        <div class="rounded-lg h-full aspect-square overflow-hidden">
                            <img id="imageInModal" src="{{ asset('images/no_image_icon.png') }}" alt=""
                                class="w-full h-full object-cover">
                        </div>
                
                        <div class="flex-1 ml-6 h-full overflow-y-auto my-scrollbar-2">
                            <h5 id="name" class="text-2xl my-1 text-[--text-color] capitalize font-semibold">#${data.article_no}</h5>
                            <p class="text-[--secondary-text] mb-1 tracking-wide text-sm"><strong>Category:</strong> <span>${data.category}</span></p>
                            <p class="text-[--secondary-text] mb-1 tracking-wide text-sm"><strong>Season:</strong> <span>${data.season}</span></p>
                            <p class="text-[--secondary-text] mb-1 tracking-wide text-sm"><strong>Size:</strong> <span>${data.size}</span></p>
                            <p class="text-[--secondary-text] mb-1 tracking-wide text-sm"><strong>Sales Rate:</strong> <span>${data.sales_rate}</span></p>
                            
                            <hr class="border-gray-600 my-3">
                
                            <p class="text-[--secondary-text] mb-1 tracking-wide text-sm"><strong>Fabric Type:</strong> <span>${data.fabric_type}</span></p>
                            <p class="text-[--secondary-text] mb-1 tracking-wide text-sm"><strong>Quantity-Pcs:</strong> <span>${data.quantity}</span></p>
                            <p class="text-[--secondary-text] mb-1 tracking-wide text-sm"><strong>Current Stock-Pcs:</strong> <span>${data.quantity - data.ordered_quantity}</span></p>
                            <p class="text-[--secondary-text] mb-1 tracking-wide text-sm"><strong>Ready Date:</strong> <span>${data.date}</span></p>

                            <hr class="border-gray-600 my-3">

                            <div class="w-full text-left grow text-sm">
                                <div class="flex justify-between items-center bg-[--h-bg-color] rounded-lg py-2 px-4 mb-4">
                                    <div class="w-1/5">#</div>
                                    <div class="grow ml-5">Title</div>
                                    <div class="w-1/4">Rate</div>
                                </div>
                                <div id="modal-rate-list" class="overflow-y-auto my-scrollbar-2">
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <!-- Modal Action Slot -->
                    <x-slot name="actions">
                        <button type="button"
                            class="px-4 py-2 bg-[--secondary-bg-color] border border-gray-600 text-nowrap text-[--secondary-text] rounded-lg hover:bg-[--h-bg-color] transition-all 0.3s ease-in-out">
                            Print Article
                        </button>

                        <button onclick="closeModal()" type="button"
                            class="px-4 py-2 bg-[--secondary-bg-color] border border-gray-600 text-[--secondary-text] rounded-lg hover:bg-[--h-bg-color] transition-all 0.3s ease-in-out">
                            Cancel
                        </button>

                        <button id="add-image-in-modal" type="button"
                            class="px-4 py-2 bg-[--bg-warning] border border-[--bg-warning] text-[--text-warning] font-medium text-nowrap rounded-lg hover:bg-[--h-bg-warning] transition-all 0.3s ease-in-out">
                            Add Image
                        </button>

                        <button id="add-rate-in-modal" type="button"
                            class="px-4 py-2 bg-[--bg-success] border border-[--bg-success] text-[--text-success] font-medium text-nowrap rounded-lg hover:bg-[--h-bg-success] transition-all 0.3s ease-in-out">
                            Add Rate
                        </button>
                    </x-slot>
                </x-modal>
            `;

            let imageInModal = document.getElementById('imageInModal');
            let addImageInModal = document.getElementById('add-image-in-modal');
            let addRateInModal = document.getElementById('add-rate-in-modal');
            let no_image_dot_modal = document.getElementById('no_image_dot_modal');

            if (data.image == "no_image_icon.png") {
                imageInModal.src = `images/no_image_icon.png`;
                imageInModal.parentElement.classList.add('scale-75');
                addImageInModal.classList.remove('hidden');
                addImageInModal.addEventListener('click', function() {
                    generateAddImageModal(item);
                })
            } else {
                imageInModal.src = `storage/uploads/images/${data.image}`
                addImageInModal.classList.add('hidden');
            }

            if (data.sales_rate == "0.00") {
                no_image_dot_modal.classList.add('bg-[--border-error]');
                no_image_dot_modal.classList.add('shadow-md');
                no_image_dot_modal.classList.remove('bg-transparent');

                addRateInModal.classList.remove('hidden');
                addRateInModal.addEventListener('click', function() {
                    generateAddRateModal(item);
                })
            } else {
                no_image_dot_modal.classList.remove('bg-[--border-error]');
                no_image_dot_modal.classList.remove('shadow-md');
                no_image_dot_modal.classList.add('bg-transparent');

                addRateInModal.classList.add('hidden');
            }

            let articleRatesArray = data.rates_array;
            let modalRateList = document.getElementById('modal-rate-list');
            modalRateList.innerHTML = '';

            if (articleRatesArray.length > 0) {
                articleRatesArray.forEach((rate, index) => {
                    let rateItem = `
                        <div class="flex justify-between items-center border-t border-gray-600 py-2 px-4">
                            <div class="w-1/5">${index + 1}</div>
                            <div class="grow ml-5">${rate.title}</div>
                            <div class="w-1/4">${new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(rate.rate)}</div>
                        </div>
                    `;
                    modalRateList.innerHTML += rateItem;
                });
            } else {
                modalRateList.innerHTML = `
                    <div class="flex justify-between items-center border-t border-gray-600 py-2 px-4">
                        <div class="grow text-center text-[--border-error]">No rates added yet.</div>
                    </div>
                `;
            }

            openModal();
            document.getElementById('modal').classList.remove('hidden');
            document.getElementById('modal').classList.add('flex');
        };

        addListenerToCards();

        function openModal() {
            isModalOpened = true;
            closeAllDropdowns();
            closeContextMenu();
        };

        function openAddImageModal() {
            isAddImageModalOpened = true;
            closeAllDropdowns();
            closeContextMenu();
        };

        function openAddRateModal() {
            isAddRateModalOpened = true;
            closeAllDropdowns();
            closeContextMenu();
        };
        
        function closeModal() {
            isModalOpened = false;
            let modal = document.getElementById('modal');
            modal.classList.add('fade-out');

            modal.addEventListener('animationend', () => {
                modal.classList.add('hidden');
                modal.classList.remove('fade-out');
            }, {
                once: true
            });
        }

        function closeAddImageModal() {
            isAddImageModalOpened = false;
            let modal = document.getElementById('addImageModal')
            modal.classList.add('fade-out');

            modal.addEventListener('animationend', () => {
                modal.classList.add('hidden');
                modal.classList.remove('fade-out');
            }, {
                once: true
            });
        }

        function closeAddRateModal() {
            isAddRateModalOpened = false;
            let modal = document.getElementById('addRateModal')
            modal.classList.add('fade-out');

            modal.addEventListener('animationend', () => {
                modal.classList.add('hidden');
                modal.classList.remove('fade-out');
            }, {
                once: true
            });
        }

        // Function for Search
        function filterData(search) {
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

            return filteredData;
        }
    </script>
@endsection
