@extends('app')
@section('title', 'Show Bank Accounts | ' . app('company')->name)
@section('content')
    <!-- Modal -->
    <div id="modal"
        class="hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-[var(--overlay-color)] fade-in">
    </div>
    <div>
        <div class="w-[80%] mx-auto">
            <x-search-header heading="Bank Accounts" :filter_items="[
                'all' => 'All',
                'title' => 'Title',
                'category' => 'Category',
            ]"/>
        </div>

        <!-- Main Content -->
        <section class="text-center mx-auto">
            <div
                class="show-box mx-auto w-full md:w-[80%] h-[70vh] bg-[var(--secondary-bg-color)] rounded-xl shadow-lg overflow-y-auto p-7 pt-12 relative">
                <x-form-title-bar title="Show Bank Accounts" changeLayoutBtn layout="{{ $authLayout }}" />

                @if (count($bankAccounts) > 0)
                    <div class="absolute bottom-3 right-3 flex items-center gap-2 w-fll z-50">
                        <x-section-navigation-button link="{{ route('bank-accounts.create') }}" title="Add New Account" icon="fa-plus" />
                    </div>
                    
                    <div class="card_container">
                        @if ($authLayout == 'grid')
                            <div class="search_container grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                                @foreach ($bankAccounts as $bankAccount)
                                    <div id='{{ $bankAccount->id }}' data-json='{{ $bankAccount }}'
                                        class="contextMenuToggle modalToggle card relative border border-gray-600 shadow rounded-xl min-w-[100px] flex gap-4 p-4 cursor-pointer overflow-hidden fade-in">

                                        @php
                                            $details = [
                                                'Category' => $bankAccount->category,
                                            ];
                                        @endphp
                                
                                        @switch($bankAccount->category)
                                            @case('customer')
                                                @php $details['Name'] = $bankAccount->subCategory->customer_name; @endphp
                                                @break
                                            @case('supplier')
                                                @php $details['Name'] = $bankAccount->subCategory->supplier_name; @endphp
                                                @break
                                        @endswitch

                                        @php $details['Date'] = $bankAccount->date->format('d-M-Y, D'); @endphp
                                        
                                        <x-card :data="[
                                            'name' => $bankAccount->account_title,
                                            'details' => $details,
                                        ]" />
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="grid grid-cols-4 bg-[var(--h-bg-color)] rounded-lg font-medium py-2">
                                <div class="text-left pl-5">Account Title</div>
                                <div class="text-left pl-5">Name</div>
                                <div class="text-center">Category</div>
                                <div class="text-right pr-5">Date</div>
                            </div>
                            
                            <div class="search_container overflow-y-auto grow my-scrollbar-2">
                                @foreach ($bankAccounts as $bankAccount)
                                    @php
                                        $owner = '-';
                                        switch ($bankAccount->category) {
                                            case 'customer':
                                                $owner = $bankAccount->subCategory->customer_name;
                                                break;
                                            case 'supplier':
                                                $owner = $bankAccount->subCategory->supplier_name;
                                                break;
                                        }
                                    @endphp
                            
                                    <div id="{{ $bankAccount->id }}" data-json='{{ $bankAccount }}' class="contextMenuToggle modalToggle relative group grid grid-cols-4 border-b border-[var(--h-bg-color)] items-center py-2 cursor-pointer hover:bg-[var(--h-secondary-bg-color)] transition-all fade-in ease-in-out">
                                        <span class="text-left pl-5">{{ $bankAccount->account_title }}</span>
                                        <span class="text-left pl-5">{{ $owner }}</span>
                                        <span class="text-center capitalize">{{ $bankAccount->category }}</span>
                                        <span class="text-right pr-5">{{ $bankAccount->date->format('d-M-Y, D') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @else
                    <div class="no-article-message w-full h-full flex flex-col items-center justify-center gap-2">
                        <h1 class="text-md text-[var(--secondary-text)] capitalize">No Bank Account yet</h1>
                        <a href="{{ route('bank-accounts.create') }}"
                        class="text-sm bg-[var(--primary-color)] text-[var(--text-color)] px-4 py-2 rounded-md hover:bg-[var(--h-primary-color)] hover:scale-105 hover:mb-2 transition-all duration-300 ease-in-out font-semibold">Add
                            New</a>
                    </div>
                @endif
            </div>
        </section>
        <div class="context-menu absolute top-0 text-sm" style="display: none;">
            <div
                class="border border-gray-600 w-48 bg-[var(--secondary-bg-color)] text-[var(--text-color)] shadow-lg rounded-xl transform transition-all duration-300 ease-in-out z-50">
                <ul class="p-2">
                    <li>
                        <button id="show-details" type="button"
                            class="flex items-center w-full px-4 py-2 text-left hover:bg-[var(--h-bg-color)] rounded-md transition-all duration-300 ease-in-out cursor-pointer">Show
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

            document.addEventListener('mousedown', (e) => {
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
                document.addEventListener('mousedown', removeContextMenu);
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
                        class="absolute top-3 left-3 w-[0.7rem] h-[0.7rem] bg-[var(--border-success)] rounded-full">
                    </div>
                    <div class="flex items-start relative h-[15rem]">
                        <div class="rounded-full h-full aspect-square overflow-hidden">
                            <img id="imageInModal" src="{{ asset('images/default_avatar.png') }}" alt=""
                                class="w-full h-full object-cover">
                        </div>
                
                        <div class="flex-1 ml-8 h-full overflow-y-auto my-scrollbar-2">
                            <h5 id="name" class="text-2xl my-1 text-[var(--text-color)] capitalize font-semibold">${data.account_title}</h5>
                            <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm"><strong>Category:</strong> <span>${data.category}</span></p>
                            <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm ${data.category === 'self' ? 'hidden' : ''}"><strong>Name:</strong> <span>${data.category == 'supplier' ? data.sub_category.supplier_name : data.category == 'customer' ? data.sub_category.customer_name : ''}</span></p>
                            <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm"><strong>Date:</strong> <span>${formatDate(data.date)}</span></p>
                            <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm capitalize"><strong>Bank:</strong> <span>${data.bank.title}</span></p>
                            <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm ${data.category !== 'self' ? 'hidden' : ''}"><strong>Account No.:</strong> <span>${data.account_no}</span></p>
                            <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm ${data.category !== 'self' ? 'hidden' : ''}"><strong>Cheque Book Serial:</strong> <span>Start: ${data.chqbk_serial_start}</span> | <span>End: ${data.chqbk_serial_end}</span></p>
                        </div>
                    </div>
                
                    <!-- Modal Action Slot -->
                    <x-slot name="actions">
                        <button onclick="closeModal()" type="button"
                            class="px-4 py-2 bg-[var(--secondary-bg-color)] border border-gray-600 text-[var(--secondary-text)] rounded-lg hover:bg-[var(--h-bg-color)] transition-all duration-300 ease-in-out cursor-pointer hover:scale-[0.95]">
                            Cancel
                        </button>
                    </x-slot>
                </x-modal>
            `;

            openModal()
        }

        document.addEventListener('mousedown', (e) => {
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
                        return (
                            item.account_title.toLowerCase().includes(search) ||
                            item.category.toLowerCase().includes(search)
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
                        
                    default:
                        return (
                            item.account_title.toLowerCase().includes(search) ||
                            item.category.toLowerCase().includes(search)
                        );
                        break;
                }
            });

            return filteredData;
        }
    </script>
@endsection
