@extends('app')
@section('title', 'Show Payments | ' . app('company')->name)
@section('content')
    @php $authLayout = Auth::user()->layout; @endphp
    <!-- Modals -->
    <div id="modal"
        class="mainModal hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-black bg-opacity-50 fade-in">
    </div>
    
    <x-search-header heading="Payments" :filter_items="[
        'all' => 'All',
        'customer_name' => 'Customer Name',
        'type' => 'Type',
        'date' => 'Date',
    ]"/>
    
    <!-- Main Content -->
    <section class="text-center mx-auto ">
        <div
            class="show-box mx-auto w-[80%] h-[70vh] bg-[--secondary-bg-color] rounded-xl shadow overflow-y-auto @if ($authLayout == 'grid') pt-7 pr-2 @endif relative">
            @if ($authLayout == 'grid')
                <div
                    class="form-title text-center absolute top-0 left-0 w-full bg-[--primary-color] py-1 shadow-lg uppercase font-semibold text-sm">
                    <h4>Show Payments</h4>
                </div>
            @endif

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

            @if (count($payments) > 0)
                <div
                    class="add-new-article-btn absolute bottom-8 right-5 hover:scale-105 hover:bottom-9 transition-all group duration-300 ease-in-out">
                    <a href="{{ route('payments.create') }}"
                        class="bg-[--primary-color] text-[--text-color] px-3 py-2 rounded-full hover:bg-[--h-primary-color] transition-all duration-300 ease-in-out"><i
                            class="fas fa-plus"></i></a>
                    <span
                        class="absolute shadow-xl right-7 top-0 border border-gray-600 transform -translate-x-1/2 bg-[--secondary-bg-color] text-[--text-color] text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
                        Add
                    </span>
                </div>
            @endif

            @if (count($payments) > 0)
                <div class="details h-full">
                    <div class="container-parent h-full overflow-y-auto my-scroller">
                        @if ($authLayout == 'grid')
                            <div class="card_container p-5 pr-3 grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">
                                @foreach ($payments as $payment)
                                    <div id="{{ $payment->id }}" data-json='{{ $payment }}'
                                        class="contextMenuToggle modalToggle card relative border border-gray-600 shadow rounded-xl min-w-[100px] flex gap-4 py-4 px-5 cursor-pointer overflow-hidden fade-in">
                                        <x-card :data="[
                                            'name' => 'Customer: ' . $payment->customer->customer_name,
                                            'details' => [
                                                'Type' => $payment->type,
                                                'Date' => $payment->date,
                                                'Amount' => $payment->amount,
                                            ],
                                        ]" />
                                    </div>
                                @endforeach
                            </div>
                        @else
                            {{-- <div class="table_container rounded-tl-lg rounded-tr-lg overflow-hidden text-sm">
                                <div class="grid grid-cols-5 bg-[--primary-color] font-medium">
                                    <div class="p-2">Article No</div>
                                    <div class="p-2">Season</div>
                                    <div class="p-2">Size</div>
                                    <div class="p-2">Category</div>
                                    <div class="p-2">Sales Rate</div>
                                </div>
                                @foreach ($payments as $article)
                                    <div data-article="{{ $article }}"
                                        class="contextMenuToggle modalToggle relative group grid grid-cols-5 text-center border-b border-gray-600 items-center py-0.5 cursor-pointer hover:bg-[--h-secondary-bg-color] transition-all fade-in ease-in-out"
                                        onclick="toggleDetails(this)">
                                        @if ($article->image == 'no_image_icon.png')
                                            <div
                                                class="warning_dot absolute top-4 left-3 w-[0.5rem] h-[0.5rem] bg-[--border-warning] rounded-full group-hover:opacity-0 transition-all 0.3s ease-in-out">
                                            </div>
                                            <div
                                                class="text-xs absolute opacity-0 top-3 left-3 text-nowrap text-[--border-warning] h-[1rem] group-hover:opacity-100 transition-all 0.3s ease-in-out">
                                                No Image</div>
                                        @endif
                                        <div class="p-2">#{{ $article->article_no }}</div>
                                        <div class="p-2">{{ $article->season->title }}</div>
                                        <div class="p-2">{{ $article->size->title }}</div>
                                        <div class="p-2">{{ $article->category->title }}</div>
                                        <div class="p-2">{{ $article->sales_rate }}</div>
                                    </div>
                                @endforeach
                            </div> --}}
                        @endif
                    </div>
                </div>
            @else
                <div class="no-article-message w-full h-full flex flex-col items-center justify-center gap-2">
                    <h1 class="text-sm text-[--secondary-text] capitalize">No Payment Found</h1>
                    <a href="{{ route('payments.create') }}"
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
                            Order</button>
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

        const close = document.querySelectorAll('#close');

        let isModalOpened = false;

        close.forEach(function(btn) {
            btn.addEventListener("click", (e) => {
                let targetedModal = e.target.closest(".mainModal")
                if (targetedModal.id == 'modal') {
                    if (isModalOpened) {
                        closeModal();
                    }
                }
            });
        });
        
        document.addEventListener('click', (e) => {
            const { id } = e.target;
            if (id === 'modalForm') {
                closeModal();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                if (isModalOpened == true) {
                    closeModal();
                }
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
                <x-modal id="modalForm" closeAction="closeModal">
                    <!-- Modal Content Slot -->
                    <div class="flex items-start relative h-[15rem]">
                        <div class="flex-1 h-full overflow-y-auto my-scroller-2">
                            <div class="px-2">
                                <h5 id="name" class="text-2xl mb-2 text-[--text-color] capitalize font-semibold leading-none">Customer: ${data.customer.customer_name}</h5>
                                <p class="text-[--secondary-text] mb-1 tracking-wide text-sm"><strong>Date:</strong> <span>${data.date}</span></p>
                                <p class="text-[--secondary-text] mb-1 tracking-wide text-sm"><strong>Amount:</strong> <span>${data.amount}</span></p>
                                <p class="text-[--secondary-text] mb-1 tracking-wide text-sm capitalize"><strong>Type:</strong> <span>${data.type}</span></p>
                            </div>
                            
                            <hr class="border-gray-600 my-3"/>

                            <div id="paymentDetails" class="px-2">
                                <p class="text-[--secondary-text] mb-1 tracking-wide text-sm capitalize"><strong>Remarks:</strong> <span>${data.remarks}</span></p>
                            </div>
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

            let paymentDetails = document.getElementById('paymentDetails');

            if (data.type == 'cheque') {
                paymentDetails.innerHTML = `
                    <p class="text-[--secondary-text] mb-1 tracking-wide text-sm capitalize"><strong>Cheque No.:</strong> <span>${data.cheque_no}</span></p>
                    <p class="text-[--secondary-text] mb-1 tracking-wide text-sm capitalize"><strong>Bank:</strong> <span>${data.bank}</span></p>
                    <p class="text-[--secondary-text] mb-1 tracking-wide text-sm capitalize"><strong>Cheque Date:</strong> <span>${data.cheque_date}</span></p>
                    <p class="text-[--secondary-text] mb-1 tracking-wide text-sm capitalize"><strong>Clear Date:</strong> <span>${data.clear_date}</span></p>
                    <p class="text-[--secondary-text] mb-1 tracking-wide text-sm capitalize"><strong>Remarks:</strong> <span>${data.remarks}</span></p>
                `;
            } else if (data.type == 'slip') {
                paymentDetails.innerHTML = `
                    <p class="text-[--secondary-text] mb-1 tracking-wide text-sm capitalize"><strong>Slip No.:</strong> <span>${data.slip_no}</span></p>
                    <p class="text-[--secondary-text] mb-1 tracking-wide text-sm capitalize"><strong>Slip Date:</strong> <span>${data.slip_date}</span></p>
                    <p class="text-[--secondary-text] mb-1 tracking-wide text-sm capitalize"><strong>Clear Date:</strong> <span>${data.clear_date}</span></p>
                    <p class="text-[--secondary-text] mb-1 tracking-wide text-sm capitalize"><strong>Remarks:</strong> <span>${data.remarks}</span></p>
                `;
            } else if (data.type == 'online') {
                paymentDetails.innerHTML = `
                    <p class="text-[--secondary-text] mb-1 tracking-wide text-sm capitalize"><strong>Bank:</strong> <span>${data.bank}</span></p>
                    <p class="text-[--secondary-text] mb-1 tracking-wide text-sm capitalize"><strong>Transition Id:</strong> <span>${data.transition_id}</span></p>
                    <p class="text-[--secondary-text] mb-1 tracking-wide text-sm capitalize"><strong>Remarks:</strong> <span>${data.remarks}</span></p>
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

        // Function for Search
        function filterData(search) {
            const filteredData = cardsDataArray.filter(item => {
                switch (filterType) {
                    case 'all':
                        return (
                            item.customer.customer_name.toLowerCase().includes(search) ||
                            item.type.toLowerCase().includes(search) ||
                            item.date.toLowerCase().includes(search)
                        );
                        break;
                        
                    case 'customer_name':
                        return (
                            item.customer.customer_name.toLowerCase().includes(search)
                        );
                        break;
                        
                    case 'type':
                        return (
                            item.type.toLowerCase().includes(search)
                        );
                        break;
                        
                    case 'date':
                        return (
                            item.date.toLowerCase().includes(search)
                        );
                        break;
                
                    default:
                        return (
                            item.customer.customer_name.toLowerCase().includes(search) ||
                            item.type.toLowerCase().includes(search) ||
                            item.date.toLowerCase().includes(search)
                        );
                        break;
                }
            });

            return filteredData;
        }
    </script>
@endsection
