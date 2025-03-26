@extends('app')
@section('title', 'Show Articles | ' . app('company')->name)
@section('content')
    
    {{-- header --}}
    <div class="w-[80%] mx-auto">
        <x-search-header heading="Physical Quantity" :filter_items="[
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
                <h4>Show physical Quantities</h4>
            </div>

            @if (count($physicalQuantities) > 0)
                <div
                    class="add-new-article-btn absolute bottom-8 right-5 hover:scale-105 hover:bottom-9 transition-all group duration-300 ease-in-out">
                    <a href="{{ route('physical-quantities.create') }}"
                        class="bg-[--primary-color] text-[--text-color] px-3 py-2 rounded-full hover:bg-[--h-primary-color] transition-all duration-300 ease-in-out"><i
                            class="fas fa-plus"></i></a>
                    <span
                        class="absolute shadow-xl right-7 top-0 border border-gray-600 transform -translate-x-1/2 bg-[--secondary-bg-color] text-[--text-color] text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
                        Add
                    </span>
                </div>
            @endif

            @if (count($physicalQuantities) > 0)
                <div class="details h-full">
                    <div class="container-parent h-full overflow-y-auto my-scrollbar">
                        <div class="card_container p-5 pr-3">
                            <div class="table_container overflow-hidden text-sm">
                                <div class="grid grid-cols-4 bg-[--h-bg-color] rounded-lg font-medium py-2">
                                    <div>Article No.</div>
                                    <div>Date</div>
                                    <div>Pc/Pkt</div>
                                    <div>Pakets</div>
                                </div>
                                @forEach ($physicalQuantities as $physicalQuantity)
                                    <div data-article="hello"
                                        class="contextMenuToggle modalToggle relative group grid grid-cols-4 text-center border-b border-[--h-bg-color] items-center py-2 cursor-pointer hover:bg-[--h-secondary-bg-color] transition-all fade-in ease-in-out"
                                        onclick="toggleDetails(this)">
                                        <span>#{{ $physicalQuantity->article->article_no }}</span>
                                        <span>{{ $physicalQuantity->date }}</span>
                                        <span>{{ $physicalQuantity->article->pcs_per_packet }}</span>
                                        <span>{{ $physicalQuantity->packets }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="no-record-message w-full h-full flex flex-col items-center justify-center gap-2">
                    <h1 class="text-sm text-[--secondary-text] capitalize">No Record Found</h1>
                    <a href="{{ route('physical-quantities.create') }}"
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
                </ul>
            </div>
        </div>
    </section>

    <script>
        let contextMenu = document.querySelector('.context-menu');
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

        function addContextMenuListenerToCards() {
            let contextMenuToggle = document.querySelectorAll('.contextMenuToggle');

            contextMenuToggle.forEach(toggle => {
                toggle.addEventListener('contextmenu', (e) => {
                    generateContextMenu(e);
                });
            });
        }

        addContextMenuListenerToCards();

        function generateContextMenu(e) {
            addImgInContext.classList.add('hidden');

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
                }
            });

            document.addEventListener('click', (e) => {
                if (e.target.id === "add-img-in-context-btn") {
                    generateAddImageModal(item);
                }
            });

            if (data.image === "no_image_icon.png") {
                addImgInContext.classList.remove('hidden');
            }

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
                document.addEventListener('click', removeContextMenu);
            }, 10);
        }

        // Function for Search
        // function filterData(search) {
        //     const filteredData = cardsDataArray.filter(item => {
        //         switch (filterType) {
        //             case 'all':
        //                 return (
        //                     item.article_no.toString().includes(search) ||
        //                     item.category.toLowerCase().includes(search) ||
        //                     item.season.toLowerCase().includes(search) ||
        //                     item.size.toLowerCase().includes(search)
        //                 );
        //                 break;
                        
        //             case '#':
        //                 return (
        //                     item.article_no.toString().includes(search)
        //                 );
        //                 break;
                        
        //             case 'category':
        //                 return (
        //                     item.category.toLowerCase().includes(search)
        //                 );
        //                 break;
                        
        //             case 'season':
        //                 return (
        //                     item.season.toLowerCase().includes(search)
        //                 );
        //                 break;
                        
        //             case 'size':
        //                 return (
        //                     item.size.toLowerCase().includes(search)
        //                 );
        //                 break;
                
        //             default:
        //                 return (
        //                     item.article_no.toString().includes(search) ||
        //                     item.category.toLowerCase().includes(search) ||
        //                     item.season.toLowerCase().includes(search) ||
        //                     item.size.toLowerCase().includes(search)
        //                 );
        //                 break;
        //         }
        //     });

        //     return filteredData;
        // }
    </script>
@endsection
