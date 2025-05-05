@extends('app')
@section('title', 'Show Payment Programs | ' . app('company')->name)
@section('content')
    <!-- Modal -->
    <div id="modal"
        class="hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-[var(--overlay-color)] fade-in">
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
                class="show-box mx-auto w-full md:w-[80%] h-[70vh] bg-[var(--secondary-bg-color)] rounded-xl shadow-lg overflow-y-auto p-7 pt-12 relative">
                <div
                    class="form-title text-center absolute top-0 left-0 w-full bg-[var(--primary-color)] py-1 uppercase font-semibold">
                    <h4>Show Payment Programs</h4>
                </div>

                <div
                    class="add-new-article-btn absolute z-[999] bottom-8 right-5 hover:scale-105 hover:bottom-9 transition-all group duration-300 ease-in-out">
                    <a href="{{ route('payment-programs.create') }}"
                        class="bg-[var(--primary-color)] text-[var(--text-color)] px-3 py-2 rounded-full hover:bg-[var(--h-primary-color)] transition-all duration-300 ease-in-out"><i
                            class="fas fa-plus"></i></a>
                    <span
                        class="absolute shadow-xl right-7 top-0 border border-gray-600 transform -translate-x-1/2 bg-[var(--secondary-bg-color)] text-[var(--text-color)] text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
                        Add
                    </span>
                </div>

                @if (count($finalData) > 0)
                    <div class="data_container">
                        <div class="grid grid-cols-9 bg-[var(--h-bg-color)] rounded-lg font-medium py-2">
                            <div class="text-center">Date</div>
                            <div class="text-center">Customer</div>
                            <div class="text-center">O/P No.</div>
                            <div class="text-center">Category</div>
                            <div class="text-center">Beneficiary</div>
                            <div class="text-center">Amount</div>
                            <div class="text-center">Document</div>
                            <div class="text-center">Payment</div>
                            <div class="text-center">Balance</div>
                        </div>
                        
                        <div class="search_container overflow-y-auto grow my-scrollbar-2">
                            @foreach ($finalData as $data)
                                <div id="{{ $data['id'] }}" data-json="{{ json_encode($data) }}" class="contextMenuToggle modalToggle relative group grid grid-cols-9 border-b border-[var(--h-bg-color)] items-center py-2 cursor-pointer hover:bg-[var(--h-secondary-bg-color)] transition-all fade-in ease-in-out">
                                    <span class="text-center">{{ $data['date'] }}</span>
                                    <span class="text-center">{{ $data['customer']['customer_name'] }}</span>
                                    <span class="text-center">{{ $data['order_no'] ?? $data['program_no'] }}</span>
                                    <span class="text-center">{{ $data['category'] ?? ($data['payment_programs']['category'] ?? '-') }}</span>
                                    <span class="text-center">
                                        @php
                                            $beneficiary = '-';
                                            if (isset($data['category'])) {
                                                if ($data['category'] == 'supplier' && isset($data['sub_category']['supplier_name'])) {
                                                    $beneficiary = $data['sub_category']['supplier_name'];
                                                } elseif ($data['category'] == 'customer' && isset($data['sub_category']['customer_name'])) {
                                                    $beneficiary = $data['sub_category']['customer_name'];
                                                } elseif ($data['category'] == 'waiting' && isset($data['remarks'])) {
                                                    $beneficiary = $data['remarks'];
                                                } elseif ($data['category'] == 'self_account' && isset($data['sub_category']['account_title'])) {
                                                    $beneficiary = $data['sub_category']['account_title'];
                                                }
                                            } else if (isset($data['payment_programs']['category'])) {
                                                if ($data['payment_programs']['category'] == 'supplier' && isset($data['payment_programs']['sub_category']['supplier_name'])) {
                                                    $beneficiary = $data['payment_programs']['sub_category']['supplier_name'];
                                                } elseif ($data['payment_programs']['category'] == 'customer' && isset($data['payment_programs']['sub_category']['customer_name'])) {
                                                    $beneficiary = $data['payment_programs']['sub_category']['customer_name'];
                                                } elseif ($data['payment_programs']['category'] == 'waiting' && isset($data['payment_programs']['remarks'])) {
                                                    $beneficiary = $data['payment_programs']['remarks'];
                                                } elseif ($data['payment_programs']['category'] == 'self_account' && isset($data['payment_programs']['sub_category']['account_title'])) {
                                                    $beneficiary = $data['payment_programs']['sub_category']['account_title'];
                                                }
                                            }
                                        @endphp
                                        {{ $beneficiary }}
                                    </span>
                                    <span class="text-center">{{ number_format($data['amount'] ?? $data['netAmount'], 1) }}</span>
                                    <span class="text-center">{{ $data['document'] ?? '-' }}</span>
                                    <span class="text-center">{{ number_format($data['payment'] ?? '0', 1) }}</span>
                                    <span class="text-center">{{ number_format($data['balance'] ?? '0', 1) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="no-article-message w-full h-full flex flex-col items-center justify-center gap-2">
                        <h1 class="text-md text-[var(--secondary-text)] capitalize">No Payment Programs yet</h1>
                        <a href="{{ route('payment-programs.create') }}"
                            class="text-md bg-[var(--primary-color)] text-[var(--text-color)] px-4 py-2 rounded-md hover:bg-blue-600 transition-all duration-300 ease-in-out uppercase font-semibold">Add
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
            console.log(data);
            
            if (data.payments.length > 0) {
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
                                <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm"><strong>Name:</strong> <span>${data.category == 'supplier' ? data.sub_category.supplier_name : data.category == 'customer' ? data.sub_category.customer_name : data.category == 'self' ? data.sub_category.name : ''}</span></p>
                                <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm"><strong>Date:</strong> <span>${data.date}</span></p>
                                <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm capitalize"><strong>Bank:</strong> <span>${data.bank.title}</span></p>
                                <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm"><strong>Account No.:</strong> <span>${data.account_no}</span></p>
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
            }

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
