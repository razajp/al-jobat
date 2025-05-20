@extends('app')
@section('title', 'Show Articles | ' . app('company')->name)
@section('content')
    <!-- Modals -->
    {{-- article details modal --}}
    <div id="modal"
        class="mainModal hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-[var(--overlay-color)] fade-in">
    </div>
    {{-- add image modal --}}
    <div id="updateImageModal"
        class="mainModal hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-[var(--overlay-color)] fade-in">
    </div>
    {{-- add rate modal --}}
    <div id="addRateModal"
        class="mainModal hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-[var(--overlay-color)] fade-in">
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
            class="show-box mx-auto w-[80%] h-[70vh] bg-[var(--secondary-bg-color)] rounded-xl shadow overflow-y-auto pt-7 pr-2 relative">
                <div class="form-title absolute top-0 left-0 w-full p-1.5">
                    <div class="text-center bg-[var(--primary-color)] py-1 shadow-lg uppercase font-semibold text-sm rounded-lg">
                        <h4>Show Articles</h4>

                        <div class="buttons absolute top-0 right-4.5 text-sm h-full flex items-center">
                            <div class="relative group">
                                <form method="POST" action="{{ route('change-data-layout') }}">
                                    @csrf
                                    <input type="hidden" name="layout" value="{{ $authLayout }}">
                                    @if ($authLayout == 'grid')
                                        <button type="submit" class="group cursor-pointer">
                                            <i class='fas fa-list-ul text-white'></i>
                                            <span
                                                class="absolute shadow-md text-nowrap border border-gray-600 z-10 -right-1 top-8 bg-[var(--h-secondary-bg-color)] text-[var(--text-color)] text-[12px] rounded px-3 py-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">List</span>
                                        </button>
                                    @else
                                        <button type="submit" class="group cursor-pointer">
                                            <i class='fas fa-grip text-white'></i>
                                            <span
                                                class="absolute shadow-md text-nowrap border border-gray-600 z-10 -right-1 top-8 bg-[var(--h-secondary-bg-color)] text-[var(--text-color)] text-[12px] rounded px-3 py-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">Grid</span>
                                        </button>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            @if (count($articles) > 0)
                <div
                    class="add-new-article-btn absolute z-40 bottom-8 right-5 hover:scale-105 hover:bottom-9 transition-all group duration-300 ease-in-out">
                    <a href="{{ route('articles.create') }}"
                        class="bg-[var(--primary-color)] text-[var(--text-color)] px-3 py-2 rounded-full hover:bg-[var(--h-primary-color)] transition-all duration-300 ease-in-out"><i
                            class="fas fa-plus"></i></a>
                    <span
                        class="absolute shadow-xl right-7 top-0 border border-gray-600 transform -translate-x-1/2 bg-[var(--secondary-bg-color)] text-[var(--text-color)] text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
                        Add
                    </span>
                </div>
            @endif

            @if (count($articles) > 0)
                <div class="details h-full">
                    <div class="container-parent h-full overflow-y-auto my-scrollbar-2">
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
                                                        'Season' => str_replace('_', ' ', $article->season),
                                                        'Size' => str_replace('_', ' ', $article->size),
                                                        'Category' => ucfirst(str_replace('_', ' ', $article->category)),
                                                    ],
                                                ]" />
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="grid grid-cols-4 bg-[var(--h-bg-color)] rounded-lg font-medium py-2">
                                        <div>Article No.</div>
                                        <div>Season</div>
                                        <div>Size</div>
                                        <div>Category</div>
                                    </div>
                                    <div class="search_container overflow-y-auto grow my-scrollbar-2">
                                        @forEach ($articles as $article)
                                            <div id="{{ $article->id }}" data-json='{{ $article }}' class="contextMenuToggle modalToggle relative group grid text- grid-cols-4 border-b border-[var(--h-bg-color)] items-center py-2 cursor-pointer hover:bg-[var(--h-secondary-bg-color)] transition-all fade-in ease-in-out">
                                                <span>#{{ $article->article_no }}</span>
                                                <span>{{ str_replace('_', ' ', $article->season) }}</span>
                                                <span>{{ str_replace('_', " ", $article->size) }}</span>
                                                <span>{{ ucfirst(str_replace('_', " ", $article->category)) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="no-article-message w-full h-full flex flex-col items-center justify-center gap-2">
                    <h1 class="text-sm text-[var(--secondary-text)] capitalize">No Article Found</h1>
                    <a href="{{ route('articles.create') }}"
                        class="text-sm bg-[var(--primary-color)] text-[var(--text-color)] px-4 py-2 rounded-md hover:bg-[var(--h-primary-color)] hover:scale-105 hover:mb-2 transition-all duration-300 ease-in-out font-semibold">Add
                        New</a>
                </div>
            @endif
        </div>

        <div class="context-menu absolute top-0 left-0 text-sm z-50" style="display: none;">
            <div
                class="border border-gray-600 w-48 bg-[var(--secondary-bg-color)] text-[var(--text-color)] shadow-md rounded-xl transform transition-all duration-300 ease-in-out z-50">
                <ul class="p-2">
                    <li>
                        <button id="show-details" type="button"
                            class="w-full px-4 py-2 text-left hover:bg-[var(--h-bg-color)] rounded-md transition-all duration-300 ease-in-out cursor-pointer">Show
                            Details</button>
                    </li>
                    <li>
                        <button id="show-details" type="button"
                            class="w-full px-4 py-2 text-left hover:bg-[var(--h-bg-color)] rounded-md transition-all duration-300 ease-in-out cursor-pointer">Print
                            Article</button>
                    </li>
                    <li id="edit-article-in-context" class="hidden">
                        <button id="edit-article-in-context-btn"
                            class="w-full px-4 py-2 text-left hover:bg-[var(--h-bg-color)] rounded-md transition-all duration-300 ease-in-out cursor-pointer">Edit
                            Article</button>
                    </li>
                    <li id="update-img-in-context">
                        <button id="update-img-in-context-btn"
                            class="font-medium text-[var(--border-warning)] w-full px-4 py-2 text-left hover:bg-[var(--bg-warning)] hover:text-[var(--text-warning)] rounded-md transition-all duration-300 ease-in-out cursor-pointer">Update
                            Image</button>
                    </li>
                    <li id="add-rate-in-context" class="hidden">
                        <button id="add-rate-in-context-btn"
                            class="font-medium text-[var(--border-success)] w-full px-4 py-2 text-left hover:bg-[var(--bg-success)] hover:text-[var(--text-success)] rounded-md transition-all duration-300 ease-in-out cursor-pointer">Add
                            Rate</button>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    <script>
        let contextMenu = document.querySelector('.context-menu');
        let updateImgInContext = document.getElementById('update-img-in-context');
        let addRateInContext = document.getElementById('add-rate-in-context');
        let editArticleInContext = document.getElementById('edit-article-in-context');
        let isContextMenuOpened = false;

        function closeContextMenu() {
            contextMenu.classList.remove('fade-in');
            contextMenu.style.display = 'none';
            isContextMenuOpened = false;
        }

        function openContextMenu() {
            closeAllDropdowns()
            contextMenu.classList.add('fade-in');
            contextMenu.style.display = 'block';
            isContextMenuOpened = true;
        }

        let contextMenuToggle = document.querySelectorAll('.contextMenuToggle');

        contextMenuToggle.forEach(toggle => {
            toggle.addEventListener('contextmenu', (e) => {
                generateContextMenu(e);
            });
        });

        function generateContextMenu(e) {
            contextMenu.classList.remove('fade-in');
            addRateInContext.classList.add('hidden');
            editArticleInContext.classList.add('hidden');

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

            document.addEventListener('mousedown', (e) => {
                if (e.target.id === "show-details") {
                    generateModal(item);
                }
            });

            document.addEventListener('mousedown', (e) => {
                if (e.target.id === "update-img-in-context-btn") {
                    generateUpdateImageModal(item);
                }
            });

            document.addEventListener('mousedown', (e) => {
                if (e.target.id === "add-rate-in-context-btn") {
                    generateAddRateModal(item);
                }
            });

            if (data.sales_rate === "0.00") {
                addRateInContext.classList.remove('hidden');
            }

            if (data.ordered_quantity == 0) {
                editArticleInContext.classList.remove('hidden');
            }

            document.addEventListener('mousedown', (e) => {
                if (e.target.id === "edit-article-in-context-btn") {
                    gotoEditArticlePage(item.id);
                }
            });

            // Function to remove context menu
            const removeContextMenu = (event) => {
                if (!contextMenu.contains(event.target)) {
                    closeContextMenu();
                    document.removeEventListener('click', removeContextMenu);
                    document.removeEventListener('contextmenu', removeContextMenu);
                }
            }

            // Wait for a small delay before attaching event listeners to avoid immediate removal
            setTimeout(() => {
                document.addEventListener('mousedown', removeContextMenu);
            }, 10);
        }

        function gotoEditArticlePage(articleId) {
            window.location.href = "{{ route('articles.edit', ':id') }}".replace(':id', articleId);
        }

        function generateUpdateImageModal(item) {
            let modalDom = document.getElementById('updateImageModal')
            let article_details_in_modal = document.querySelector('#article_details_in_modal');
            let data = JSON.parse(item.dataset.json);
            let placeholder = data.image == "no_image_icon.png" ? 'images/no_image_icon.png' : `storage/uploads/images/${data.image}`;

            modalDom.innerHTML = `
                <x-modal id="updateImageModalForm" classForBody="p-5" closeAction="closeUpdateImageModal" action="{{ route('update-image') }}">
                    <!-- Modal Content Slot -->
                    <div class="flex items-start relative">
                        <div class="flex-1 h-full overflow-y-auto my-scrollbar-2">
                            <h5 id="name" class="text-2xl my-1 text-[var(--text-color)] capitalize font-semibold">Article Details</h5>
                            <x-input 
                                value="#${data.article_no} | ${data.season} | ${data.size} | ${data.category} | ${data.fabric_type} | ${data.quantity} | ${formatNumbersWithDigits(data.sales_rate, 1, 1)} - Rs." 
                                disabled
                            />
                            
                            <hr class="border-gray-600 my-3">
                
                            <x-image-upload 
                                id="image_upload"
                                name="image_upload"
                                placeholder="${ placeholder }"
                                uploadText="Upload article image"
                            />
                        </div>
                    </div>
                
                    <!-- Modal Action Slot -->
                    <x-slot name="actions">
                        <button onclick="closeUpdateImageModal()" type="button"
                            class="px-4 py-2 bg-[var(--secondary-bg-color)] border border-gray-600 text-[var(--secondary-text)] rounded-lg hover:bg-[var(--h-bg-color)] transition-all duration-300 ease-in-out cursor-pointer">
                            Cancel
                        </button>
                        <input type="hidden" id="article_id" name="article_id">
                        <button type="submit"
                            class="px-5 py-2 bg-[var(--bg-success)] border border-[var(--bg-success)] text-[var(--text-success)] font-medium text-nowrap rounded-lg hover:bg-[var(--h-bg-success)] transition-all duration-300 ease-in-out cursor-pointer">
                            Update Image
                        </button>
                    </x-slot>
                </x-modal>
            `;

            openUpdateImageModal();

            if (data.image != "no_image_icon.png") {
                const placeholderIcon = document.querySelector(".placeholder_icon");
                placeholderIcon.classList.remove("w-16", "h-16");
                placeholderIcon.classList.add("rounded-md", "w-full", "h-auto");
            }

            document.getElementById('article_id').value = data.id;
            document.getElementById('updateImageModal').classList.remove('hidden');
        }

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
                            <h5 id="name" class="text-2xl my-1 text-[var(--text-color)] capitalize font-semibold">Article Details</h5>
                            <x-input 
                                value="#${data.article_no} | ${data.season} | ${data.size} | ${data.category} | ${data.fabric_type} | ${data.quantity} | ${formatNumbersWithDigits(data.sales_rate, 1, 1)} - Rs." 
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
                                        class="w-full bg-[var(--primary-color)] text-[var(--text-color)] rounded-lg cursor-pointer border border-[var(--primary-color)]"
                                        onclick="addRate()" />
                                </div>
                            </div>
                            {{-- rate showing --}}
                            <div id="rate-table" class="w-full text-left text-sm">
                                <div class="flex justify-between items-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4 my-3">
                                    <div class="grow ml-5">Title</div>
                                    <div class="w-1/4">Rate</div>
                                    <div class="w-[10%] text-center">Action</div>
                                </div>
                                <div id="rate-list" class="space-y-4 h-[250px] overflow-y-auto my-scrollbar-2">
                                    <div class="text-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4">No Rates Added</div>
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
                                    class="final flex justify-between items-center bg-[var(--h-bg-color)] border border-gray-600 rounded-lg py-2 px-4 w-full">
                                    <label for="sales_rate" class="text-nowrap grow">Sales Rate - Rs.</label>
                                    <input type="text" required name="sales_rate" id="sales_rate" value="0.00"
                                        class="text-right bg-transparent outline-none border-none w-[50%]" />
                                </div>
                                <div
                                    class="final flex justify-between items-center bg-[var(--h-bg-color)] border border-gray-600 rounded-lg py-2 px-4 w-full">
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
                            class="px-4 py-2 bg-[var(--secondary-bg-color)] border border-gray-600 text-[var(--secondary-text)] rounded-lg hover:bg-[var(--h-bg-color)] transition-all duration-300 ease-in-out cursor-pointer">
                            Cancel
                        </button>
                        <input type="hidden" id="article_id" name="article_id">
                        <button type="submit"
                            class="px-5 py-2 bg-[var(--bg-success)] border border-[var(--bg-success)] text-[var(--text-success)] font-medium text-nowrap rounded-lg hover:bg-[var(--h-bg-success)] transition-all duration-300 ease-in-out cursor-pointer">
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
            pcsPerPacket = document.getElementById('pcs_per_packet').value;
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
                <div
                    class="final flex justify-between items-center bg-[var(--h-bg-color)] border border-gray-600 rounded-lg py-2 px-4 w-full">
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
        let isUpdateImageModalOpened = false;
        let isAddRateModalOpened = false;

        close.forEach(function(btn) {
            btn.addEventListener("click", (e) => {
                let targetedModal = e.target.closest(".mainModal")
                if (targetedModal.id == 'modal') {
                    if (isModalOpened) {
                        closeModal();
                    }
                } else if (targetedModal.id == 'updateImageModal') {
                    if (isUpdateImageModalOpened) {
                        closeUpdateImageModal();
                    }
                } else if (targetedModal.id == 'addRateModal') {
                    if (isAddRateModalOpened) {
                        closeAddRateModal();
                    }
                }
            });
        });
        
        document.addEventListener('mousedown', (e) => {
            const { id } = e.target;
            if (id === 'modalForm') {
                closeModal();
            } else if (id === 'updateImageModalForm') {
                closeUpdateImageModal();
            } else if (id === 'addRateModalForm') {
                closeAddRateModal();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                if (isModalOpened == true) {
                    closeModal();
                }
                if (isUpdateImageModalOpened == true) {
                    closeUpdateImageModal();
                }
                if (isAddRateModalOpened == true) {
                    closeAddRateModal();
                }
                closeContextMenu();
            }
        });

        function addListenerToCards() {
            let card = document.querySelectorAll('.modalToggle');

            card.forEach(item => {
                item.addEventListener('click', () => {
                    if (!isContextMenuOpened) {
                        generateModal(item);
                    }
                });
            });
        }

        function generateModal(item) {
            let modalDom = document.getElementById('modal')
            let data = JSON.parse(item.dataset.json);
            console.log(data);
            

            modalDom.innerHTML = `
                <x-modal id="modalForm" classForBody="p-5 max-w-5xl" closeAction="closeModal" action="{{ route('update-user-status') }}">
                    <!-- Modal Content Slot -->
                    <div class="flex items-start relative h-[27rem]">
                        <div id="info_in_modal" class="absolute top-0 right-0 border border-gray-600 group bg-[var(--bg-color)] rounded-xl cursor-pointer flex items-center justify-end p-1 overflow-hidden hover:pl-3 transition-all duration-300 ease-in-out">
                            <span class="inline-block max-w-0 opacity-0 overflow-hidden whitespace-nowrap transition-all duration-300 ease-in-out group-hover:opacity-100 group-hover:max-w-[200px] group-hover:mr-2">
                                Added by ${data.creator.name}
                            </span>
                            <div class="flex items-center justify-center bg-[var(--h-bg-color)] rounded-lg p-2">
                                <svg class="size-3 transition-all duration-300 ease-in-out group-hover:size-2.5 fill-[var(--secondary-text)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 192 512">
                                    <path d="M48 80a48 48 0 1 1 96 0A48 48 0 1 1 48 80zM0 224c0-17.7 14.3-32 32-32l64 0c17.7 0 32 14.3 32 32l0 224 32 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 512c-17.7 0-32-14.3-32-32s14.3-32 32-32l32 0 0-192-32 0c-17.7 0-32-14.3-32-32z"/>
                                </svg>
                            </div>
                        </div>
                        <div id="no_rate_dot_modal"
                            class="image_dot absolute top-2 left-2 w-[0.7rem] h-[0.7rem] bg-transparent rounded-full">
                        </div>
                        <div class="rounded-lg h-full aspect-square overflow-hidden">
                            <img id="imageInModal" src="{{ asset('images/no_image_icon.png') }}" alt=""
                                class="w-full h-full object-cover">
                        </div>
                
                        <div class="flex-1 ml-6 h-full overflow-y-auto my-scrollbar-2">
                            <h5 id="name" class="text-2xl my-1 text-[var(--text-color)] capitalize font-semibold">#${data.article_no}</h5>
                            <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm"><strong>Category:</strong> <span>${data.category}</span></p>
                            <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm"><strong>Season:</strong> <span>${data.season}</span></p>
                            <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm"><strong>Size:</strong> <span>${data.size}</span></p>
                            <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm"><strong>Sales Rate:</strong> <span>${formatNumbersWithDigits(data.sales_rate, 1, 1)}</span></p>
                            
                            <hr class="border-gray-600 my-3">
                
                            <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm"><strong>Fabric Type:</strong> <span>${data.fabric_type}</span></p>
                            <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm"><strong>Quantity-Pcs:</strong> <span>${formatNumbersDigitLess(data.quantity)}</span></p>
                            <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm"><strong>Current Stock-Pcs:</strong> <span>${formatNumbersDigitLess(data.quantity - data.ordered_quantity)}</span></p>
                            <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm"><strong>Ready Date:</strong> <span>${formatDate(data.date)}</span></p>

                            <hr class="border-gray-600 my-3">

                            <div class="w-full text-left grow text-sm">
                                <div class="flex justify-between items-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4 mb-4">
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
                            class="px-4 py-2 bg-[var(--secondary-bg-color)] border border-gray-600 text-nowrap text-[var(--secondary-text)] rounded-lg hover:bg-[var(--h-bg-color)] transition-all duration-300 ease-in-out cursor-pointer hover:scale-[0.95]">
                            Print Article
                        </button>
                        
                        <button onclick="" type="button" id="edit-btn-in-modal"
                            class="px-4 py-2 bg-[var(--secondary-bg-color)] border border-gray-600 text-[var(--secondary-text)] rounded-lg hover:bg-[var(--h-bg-color)] transition-all duration-300 ease-in-out cursor-pointer hover:scale-[0.95]">
                            Edit Article
                        </button>

                        <button id="update-image-in-modal" type="button"
                            class="px-4 py-2 bg-[var(--bg-warning)] border border-[var(--bg-warning)] text-[var(--text-warning)] font-medium text-nowrap rounded-lg hover:bg-[var(--h-bg-warning)] transition-all duration-300 ease-in-out cursor-pointer hover:scale-[0.95]">
                            Update Image
                        </button>

                        <button id="add-rate-in-modal" type="button"
                            class="px-4 py-2 bg-[var(--bg-success)] border border-[var(--bg-success)] text-[var(--text-success)] font-medium text-nowrap rounded-lg hover:bg-[var(--h-bg-success)] transition-all duration-300 ease-in-out cursor-pointer hover:scale-[0.95]">
                            Add Rate
                        </button>

                        <button onclick="closeModal()" type="button"
                            class="px-4 py-2 bg-[var(--secondary-bg-color)] border border-gray-600 text-[var(--secondary-text)] rounded-lg hover:bg-[var(--h-bg-color)] transition-all duration-300 ease-in-out cursor-pointer hover:scale-[0.95]">
                            Cancel
                        </button>
                    </x-slot>
                </x-modal>
            `;

            let imageInModal = document.getElementById('imageInModal');
            let updateImageInModal = document.getElementById('update-image-in-modal');
            let addRateInModal = document.getElementById('add-rate-in-modal');
            let editArticleInModal = document.getElementById('edit-btn-in-modal');
            let no_rate_dot_modal = document.getElementById('no_rate_dot_modal');

            if (data.image == "no_image_icon.png") {
                imageInModal.src = `images/no_image_icon.png`;
                imageInModal.parentElement.classList.add('scale-75');
            } else {
                imageInModal.src = `storage/uploads/images/${data.image}`
            }
            
            updateImageInModal.addEventListener('click', function() {
                generateUpdateImageModal(item);
            })

            if (data.sales_rate == "0.00") {
                no_rate_dot_modal.classList.add('bg-[var(--border-error)]');
                no_rate_dot_modal.classList.add('shadow-md');
                no_rate_dot_modal.classList.remove('bg-transparent');

                addRateInModal.classList.remove('hidden');
                addRateInModal.addEventListener('click', function() {
                    generateAddRateModal(item);
                })
            } else {
                no_rate_dot_modal.classList.remove('bg-[var(--border-error)]');
                no_rate_dot_modal.classList.remove('shadow-md');
                no_rate_dot_modal.classList.add('bg-transparent');

                addRateInModal.classList.add('hidden');
            }

            if (data.ordered_quantity == 0) {
                editArticleInModal.classList.remove('hidden');
                editArticleInModal.addEventListener('click', function() {
                    gotoEditArticlePage(item.id);
                })
            } else {
                editArticleInModal.classList.add('hidden');
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
                        <div class="grow text-center text-[var(--border-error)]">No rates added yet.</div>
                    </div>
                `;
            }

            openModal();
            document.getElementById('modal').classList.remove('hidden');
            document.getElementById('modal').classList.add('flex');
        }

        addListenerToCards();

        function openModal() {
            isModalOpened = true;
            closeAllDropdowns();
            closeContextMenu();
        }

        function openUpdateImageModal() {
            isUpdateImageModalOpened = true;
            closeAllDropdowns();
            closeContextMenu();
        }

        function openAddRateModal() {
            isAddRateModalOpened = true;
            closeAllDropdowns();
            closeContextMenu();
        }
        
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

        function closeUpdateImageModal() {
            isUpdateImageModalOpened = false;
            let modal = document.getElementById('updateImageModal')
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
