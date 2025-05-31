@extends('app')
@section('title', 'Show Customer Payments | ' . app('company')->name)
@section('content')
@php
    $searchFields = [
        "Customer Name" => [
            "id" => "customer_name",
            "type" => "text",
            "placeholder" => "Enter customer name",
        ],
        "Type" => [
            "id" => "type",
            "type" => "select",
            "options" => [
                        'normal' => ['text' => 'Normal'],
                        'payment_program' => ['text' => 'Payment Program'],
                    ],
        ],
        "Method" => [
            "id" => "method",
            "type" => "select",
            "options" => [
                        'cash' => ['text' => 'Cash'],
                        'cheque' => ['text' => 'Cheque'],
                        'slip' => ['text' => 'Slip'],
                        'adjustment' => ['text' => 'Adjustment'],
                    ],
        ],
        "Date Range" => [
            "id" => "date_range_start",
            "type" => "date",
            "id2" => "date_range_end",
            "type2" => "date",
        ]
    ];
@endphp
    <!-- Modals -->
    <div id="modal"
        class="mainModal hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-[var(--overlay-color)] fade-in">
    </div>
    
    <div class="w-[80%] mx-auto">
        <x-search-header heading="Customer Payments" :search_fields=$searchFields/>
    </div>
    
    <!-- Main Content -->
    <section class="text-center mx-auto ">
        <div
            class="show-box mx-auto w-[80%] h-[70vh] bg-[var(--secondary-bg-color)] rounded-xl shadow overflow-y-auto pt-7 pr-2 relative">
            <x-form-title-bar title="Show Customer Payments" changeLayoutBtn layout="{{ $authLayout }}" />

            @if (count($payments) > 0)
                <div class="absolute bottom-3 right-3 flex items-center gap-2 w-fll z-50">
                    <x-section-navigation-button link="{{ route('customer-payments.create') }}" title="Add New Payment" icon="fa-plus" />
                </div>
                
                <div class="details h-full">
                    <div class="container-parent h-full overflow-y-auto my-scrollbar-2">
                        <div class="card_container p-5 pr-3">
                            @if ($authLayout == 'grid')
                                <div class="search_container grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                                    @foreach ($payments as $payment)
                                        <div id="{{ $payment->id }}" data-json='{{ $payment }}'
                                            class="contextMenuToggle modalToggle card relative border border-gray-600 shadow rounded-xl min-w-[100px] flex gap-4 py-4 px-5 cursor-pointer overflow-hidden fade-in">
                                            <x-card :data="[
                                                'name' => 'Customer: ' . $payment->customer->customer_name,
                                                'details' => [
                                                    'Type' => str_replace('_', ' ',$payment->type),
                                                    'Date' => date('d-M-Y D', strtotime($payment->date)),
                                                    'Amount' => number_format($payment->amount, 1),
                                                ],
                                            ]" />
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="grid grid-cols-4 bg-[var(--h-bg-color)] rounded-lg font-medium py-2">
                                    <div class="text-center">Customer</div>
                                    <div class="text-center">Type</div>
                                    <div class="text-center">Date</div>
                                    <div class="text-center">Amount</div>
                                </div>
                                <div class="search_container overflow-y-auto grow my-scrollbar-2">
                                    @forEach ($payments as $payment)
                                        <div id="{{ $payment->id }}" data-json='{{ $payment }}' class="contextMenuToggle modalToggle relative group grid text- grid-cols-4 border-b border-[var(--h-bg-color)] items-center py-2 cursor-pointer hover:bg-[var(--h-secondary-bg-color)] transition-all fade-in ease-in-out">
                                            <span class="text-center">{{ $payment->customer->customer_name }}</span>
                                            <span class="text-center">{{ str_replace('_', ' ',$payment->type) }}</span>
                                            <span class="text-center">{{ date('d-M-Y D', strtotime($payment->date)) }}</span>
                                            <span class="text-center">{{ number_format($payment->amount, 1) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="no-article-message w-full h-full flex flex-col items-center justify-center gap-2">
                    <h1 class="text-sm text-[var(--secondary-text)] capitalize">No Payment Found</h1>
                    <a href="{{ route('customer-payments.create') }}"
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
        
        document.addEventListener('mousedown', (e) => {
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

            modalDom.innerHTML = `
                <x-modal id="modalForm" closeAction="closeModal">
                    <!-- Modal Content Slot -->
                    <div class="flex items-start relative h-[15rem]">
                        <div class="flex-1 h-full overflow-y-auto my-scrollbar-2">
                            <div class="px-2">
                                <h5 id="name" class="text-2xl mb-2 text-[var(--text-color)] capitalize font-semibold leading-none">Customer: ${data.customer.customer_name}</h5>
                                <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm"><strong>Date:</strong> <span>${formatDate(data.date)}</span></p>
                                <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm"><strong>Amount:</strong> <span>${formatNumbersWithDigits(data.amount)}</span></p>
                                <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm capitalize"><strong>Type:</strong> <span>${data.type.replace('_', ' ')}</span></p>
                            </div>
                            
                            <hr class="border-gray-600 my-3"/>

                            <div id="paymentDetails" class="px-2">
                                <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm capitalize"><strong>Remarks:</strong> <span>${data.remarks}</span></p>
                            </div>
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

            let paymentDetails = document.getElementById('paymentDetails');

            if (data.type == 'cheque') {
                paymentDetails.innerHTML = `
                    <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm capitalize"><strong>Cheque No.:</strong> <span>${data.cheque_no}</span></p>
                    <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm capitalize"><strong>Bank:</strong> <span>${data.bank}</span></p>
                    <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm capitalize"><strong>Cheque Date:</strong> <span>${data.cheque_date}</span></p>
                    <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm capitalize"><strong>Clear Date:</strong> <span>${data.clear_date}</span></p>
                    <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm capitalize"><strong>Remarks:</strong> <span>${data.remarks}</span></p>
                `;
            } else if (data.type == 'slip') {
                paymentDetails.innerHTML = `
                    <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm capitalize"><strong>Slip No.:</strong> <span>${data.slip_no}</span></p>
                    <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm capitalize"><strong>Slip Date:</strong> <span>${data.slip_date}</span></p>
                    <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm capitalize"><strong>Clear Date:</strong> <span>${data.clear_date}</span></p>
                    <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm capitalize"><strong>Remarks:</strong> <span>${data.remarks}</span></p>
                `;
            } else if (data.type == 'online') {
                paymentDetails.innerHTML = `
                    <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm capitalize"><strong>Bank:</strong> <span>${data.bank}</span></p>
                    <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm capitalize"><strong>Transition Id:</strong> <span>${data.transition_id}</span></p>
                    <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm capitalize"><strong>Remarks:</strong> <span>${data.remarks}</span></p>
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

        document.getElementById("customer_name").addEventListener("input", function () {
            const inputValue = this.value.toLowerCase().trim();
            const items = document.querySelectorAll(".search_container > div");

            items.forEach(item => {
                const jsonData = item.getAttribute("data-json");
                if (!jsonData) return;

                const parsed = JSON.parse(jsonData);
                const customerName = parsed.customer?.customer_name?.toLowerCase() || "";

                if (customerName.includes(inputValue)) {
                    item.style.display = ""; // show
                    item.classList.remove("opacity-50", "pointer-events-none"); // re-enable
                } else {
                    item.style.display = "none"; // hide
                    // Alternatively: 
                    // item.classList.add("opacity-50", "pointer-events-none"); // disable visually
                }
            });
        });

        // Function for Search
        function filterData(search) {
            const filteredData = cardsDataArray.filter(item => {
                switch (filterType) {
                    case 'all':
                        return (
                            item.customer.customer_name.toLowerCase().includes(search) ||
                            item.type.toLowerCase().includes(search) ||
                            item.method.toLowerCase().includes(search) ||
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
                        
                    case 'method':
                        return (
                            item.method.toLowerCase().includes(search)
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
                            item.method.toLowerCase().includes(search) ||
                            item.date.toLowerCase().includes(search)
                        );
                        break;
                }
            });

            return filteredData;
        }
    </script>
@endsection
