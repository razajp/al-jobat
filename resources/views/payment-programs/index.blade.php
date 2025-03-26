@extends('app')
@section('title', 'Show Payment Programs | ' . app('company')->name)
@section('content')
    <!-- Modal -->
    <div id="modal"
        class="hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-black bg-opacity-50 fade-in">
    </div>
    <div>
        <div class="w-[80%] mx-auto">
            <x-search-header heading="Payment Programs" :filter_items="[
                'all' => 'All',
                'title' => 'Title',
                'category' => 'Category',
                'name' => 'Name',
            ]"/>
        </div>

        <!-- Main Content -->
        <section class="text-center mx-auto">
            <div
                class="show-box mx-auto w-full md:w-[80%] h-[70vh] bg-[--secondary-bg-color] rounded-xl shadow-lg overflow-y-auto p-7 pt-12 relative">
                <div
                    class="form-title text-center absolute top-0 left-0 w-full bg-[--primary-color] py-1 uppercase font-semibold">
                    <h4>Show Payment Programs</h4>

                    <div class="buttons absolute top-0 right-4 text-sm h-full flex items-center">
                        <div class="relative group">
                            <form method="POST" action="{{ route('change-data-layout') }}">
                                @csrf
                                <input type="hidden" name="layout" value="{{ $authLayout }}">
                                @if ($authLayout == 'grid')
                                    <button type="submit" class="group cursor-pointer">
                                        <i class='fas fa-list-ul text-white'></i>
                                        <span
                                            class="absolute shadow-md text-nowrap border border-gray-600 z-10 -right-1 top-8 bg-[--h-secondary-bg-color] text-[--text-color] text-[12px] rounded px-3 py-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">List</span>
                                    </button>
                                @else
                                    <button type="submit" class="group cursor-pointer">
                                        <i class='fas fa-grip text-white'></i>
                                        <span
                                            class="absolute shadow-md text-nowrap border border-gray-600 z-10 -right-1 top-8 bg-[--h-secondary-bg-color] text-[--text-color] text-[12px] rounded px-3 py-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">Grid</span>
                                    </button>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>

                <div
                    class="add-new-article-btn absolute bottom-8 right-5 hover:scale-105 hover:bottom-9 transition-all group duration-300 ease-in-out">
                    <a href="{{ route('payment-programs.create') }}"
                        class="bg-[--primary-color] text-[--text-color] px-3 py-2 rounded-full hover:bg-[--h-primary-color] transition-all duration-300 ease-in-out"><i
                            class="fas fa-plus"></i></a>
                    <span
                        class="absolute shadow-xl right-7 top-0 border border-gray-600 transform -translate-x-1/2 bg-[--secondary-bg-color] text-[--text-color] text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
                        Add
                    </span>
                </div>

                @if (count($paymentPrograms) > 0)
                    <div class="card_container">
                        @if ($authLayout == 'grid')
                            <div class="search_container grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                                @foreach ($paymentPrograms as $paymentProgram)
                                    <div id='{{ $paymentProgram->id }}' data-json='{{ $paymentProgram }}'
                                        class="contextMenuToggle modalToggle card relative border border-gray-600 shadow rounded-xl min-w-[100px] flex gap-4 p-4 cursor-pointer overflow-hidden fade-in">
                                        
                                        @php
                                            $details = [
                                                'Customer' => $paymentProgram->customer->customer_name,
                                                'Category' => $paymentProgram->category,
                                            ];
                                        @endphp
                                
                                        @switch($paymentProgram->category)
                                            @case('customer')
                                                @php $details['Name'] = $paymentProgram->subCategory->customer_name; @endphp
                                                @break
                                            @case('supplier')
                                                @php $details['Name'] = $paymentProgram->subCategory->supplier_name; @endphp
                                                @break
                                            @case('self_account')
                                                @php $details['Name'] = $paymentProgram->subCategory->account_title; @endphp
                                                @break
                                            @case('waiting')
                                                @php $details['Remarks'] = $paymentProgram->remarks; @endphp
                                                @break
                                        @endswitch

                                        <x-card :data="[
                                            'name' => 'Prg No. ' . $paymentProgram->prg_no,
                                            'details' => $details,
                                        ]" />
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="grid grid-cols-5 bg-[--h-bg-color] rounded-lg font-medium py-2">
                                <div class="text-left pl-5">Account Title</div>
                                <div class="text-left pl-5">Name</div>
                                <div class="text-center">Category</div>
                                <div class="text-right">Date</div>
                                <div class="text-right pr-5">Status</div>
                            </div>
                            
                            <div class="search_container overflow-y-auto grow my-scrollbar-2">
                                @foreach ($paymentPrograms as $paymentProgram)
                                    @php
                                        $owner = '';
                                        switch ($paymentProgram->category) {
                                            case 'self':
                                                $owner = $paymentProgram->subCategory->name;
                                                break;
                                            case 'customer':
                                                $owner = $paymentProgram->subCategory->customer_name;
                                                break;
                                            case 'supplier':
                                                $owner = $paymentProgram->subCategory->supplier_name;
                                                break;
                                        }
                                    @endphp
                            
                                    <div id="{{ $paymentProgram->id }}" data-json='{{ $paymentProgram }}' class="contextMenuToggle modalToggle relative group grid grid-cols-5 border-b border-[--h-bg-color] items-center py-2 cursor-pointer hover:bg-[--h-secondary-bg-color] transition-all fade-in ease-in-out">
                                        <span class="text-left pl-5">{{ $paymentProgram->account_title }}</span>
                                        <span class="text-left pl-5">{{ $owner }}</span>
                                        <span class="text-center capitalize">{{ $paymentProgram->category }}</span>
                                        <span class="text-right">{{ $paymentProgram->date }}</span>
                                        <span class="text-right pr-5 capitalize">{{ $paymentProgram->status ?? 'N/A' }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @else
                    <div class="no-article-message w-full h-full flex flex-col items-center justify-center gap-2">
                        <h1 class="text-md text-[--secondary-text] capitalize">No Payment Programs yet</h1>
                        <a href="{{ route('payment-programs.create') }}"
                            class="text-md bg-[--primary-color] text-[--text-color] px-4 py-2 rounded-md hover:bg-blue-600 transition-all duration-300 ease-in-out uppercase font-semibold">Add
                            New</a>
                    </div>
                @endif
            </div>
        </section>
        <div class="context-menu absolute top-0 text-sm" style="display: none;">
            <div
                class="border border-gray-600 w-48 bg-[--secondary-bg-color] text-[--text-color] shadow-lg rounded-xl transform transition-all 0.3s ease-in-out z-50">
                <ul class="p-2">
                    <li>
                        <button id="show-details" type="button"
                            class="flex items-center w-full px-4 py-2 text-left hover:bg-[--h-bg-color] rounded-md transition-all 0.3s ease-in-out">Show
                            Details</button>
                    </li>
                </ul>
            </div>
        </div>
    </div>

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

        let contextMenuToggle = document.querySelectorAll('.contextMenuToggle');

        contextMenuToggle.forEach(toggle => {
            toggle.addEventListener('contextmenu', (e) => {
                generateContextMenu(e);
            });
        });

        function generateContextMenu(e) {
            contextMenu.classList.remove('fade-in');

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
                    generateModal(item)
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
                document.addEventListener('click', removeContextMenu);
            }, 10);
        }

        let isModalOpened = false;
        let card = document.querySelectorAll('.modalToggle')

        card.forEach(item => {
            item.addEventListener('click', () => {
                generateModal(item);
            });
        });

        function generateModal(item) {
            let modalDom = document.getElementById('modal')
            let data = JSON.parse(item.dataset.json);

            modalDom.innerHTML = `
                <x-modal id="modalForm" closeAction="closeModal" action="{{ route('update-user-status') }}">
                    <!-- Modal Content Slot -->
                    <div id="active_inactive_dot_modal"
                        class="absolute top-3 left-3 w-[0.7rem] h-[0.7rem] bg-[--border-success] rounded-full">
                    </div>
                    <div class="flex items-start relative h-[15rem]">
                        <div class="rounded-full h-full aspect-square overflow-hidden">
                            <img id="imageInModal" src="{{ asset('images/default_avatar.png') }}" alt=""
                                class="w-full h-full object-cover">
                        </div>
                
                        <div class="flex-1 ml-8 h-full overflow-y-auto my-scrollbar-2">
                            <h5 id="name" class="text-2xl my-1 text-[--text-color] capitalize font-semibold">${data.account_title}</h5>
                            <p class="text-[--secondary-text] mb-1 tracking-wide text-sm"><strong>Category:</strong> <span>${data.category}</span></p>
                            <p class="text-[--secondary-text] mb-1 tracking-wide text-sm"><strong>Name:</strong> <span>${data.category == 'supplier' ? data.sub_category.supplier_name : data.category == 'customer' ? data.sub_category.customer_name : data.category == 'self' ? data.sub_category.name : ''}</span></p>
                            <p class="text-[--secondary-text] mb-1 tracking-wide text-sm"><strong>Date:</strong> <span>${data.date}</span></p>
                            <p class="text-[--secondary-text] mb-1 tracking-wide text-sm capitalize"><strong>Bank:</strong> <span>${data.bank.replace(/_/g, ' ')}</span></p>
                            <p class="text-[--secondary-text] mb-1 tracking-wide text-sm"><strong>Account No.:</strong> <span>${data.account_no}</span></p>
                        </div>
                    </div>
                
                    <!-- Modal Action Slot -->
                    <x-slot name="actions">
                        <button onclick="closeModal()" type="button"
                            class="px-4 py-2 bg-[--secondary-bg-color] border border-gray-600 text-[--secondary-text] rounded-lg hover:bg-[--h-bg-color] transition-all duration-300 ease-in-out">
                            Cancel
                        </button>
                    </x-slot>
                </x-modal>
            `;

            openModal()
        }

        document.addEventListener('click', (e) => {
            const { id } = e.target;
            if (id === 'modalForm') {
                closeModal();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && isModalOpened) {
                closeContextMenu();
                closeModal();
            }
        })

        function openModal() {
            isModalOpened = true;
            document.getElementById('modal').classList.remove('hidden');
            closeAllDropdowns();
            closeContextMenu();
        }

        function closeModal() {
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
                let name = '';
                switch (filterType) {
                    case 'all':
                        switch (item.category) {
                            
                            case 'supplier':
                                name = item.sub_category.supplier_name;
                                break;
                            case 'customer':
                                name = item.sub_category.customer_name;
                                break;
                            case 'self':
                                name = item.sub_category.name;
                                break;
                            default:
                                break;
                        }

                        return (
                            item.account_title.toLowerCase().includes(search) ||
                            item.category.toLowerCase().includes(search) ||
                            name.toLowerCase().includes(search)
                        );
                        break;
                        
                    case 'title':
                        return (
                            item.account_title.toLowerCase().includes(search)
                        );
                        break;
                        
                    case 'category':
                        return (
                            item.category.toLowerCase().includes(search)
                        );
                        break;
                        
                    case 'name':
                        // let name = '';
                        switch (item.category) {
                            case 'supplier':
                                name = item.sub_category.supplier_name;
                                break;
                            case 'customer':
                                name = item.sub_category.customer_name;
                                break;
                            case 'self':
                                name = item.sub_category.name;
                                break;
                            default:
                                break;
                        }

                        return (
                            name.toLowerCase().includes(search)
                        );
                        break;
                
                    default:
                        // let name = '';
                        switch (item.category) {
                            case 'supplier':
                                name = item.sub_category.supplier_name;
                                break;
                            case 'customer':
                                name = item.sub_category.customer_name;
                                break;
                            case 'self':
                                name = item.sub_category.name;
                                break;
                            default:
                                break;
                        }

                        return (
                            item.account_title.toLowerCase().includes(search) ||
                            item.category.toLowerCase().includes(search) ||
                            name.toLowerCase().includes(search)
                        );
                        break;
                }
            });

            return filteredData;
        }
    </script>
@endsection
