@extends('app')
@section('title', 'Show Expenses | ' . app('company')->name)
@section('content')
    @php
        $searchFields = [
            "Supplier Name" => [
                "id" => "supplier_name",
                "type" => "text",
                "placeholder" => "Enter supplier name",
                "oninput" => "runDynamicFilter()",
                "dataFilterPath" => "supplier.supplier_name",
            ],
            "Reff. No" => [
                "id" => "reff_no",
                "type" => "text",
                "placeholder" => "Enter reff. no",
                "oninput" => "runDynamicFilter()",
                "dataFilterPath" => "reff_no",
            ],
            "Expense" => [
                "id" => "expense",
                "type" => "select",
                "options" => $expenseOptions,
                "onchange" => "runDynamicFilter()",
                "dataFilterPath" => "expense_setups.title",
            ],
            "Remarks" => [
                "id" => "remarks",
                "type" => "text",
                "placeholder" => "Enter remarks",
                "oninput" => "runDynamicFilter()",
                "dataFilterPath" => "remarks",
            ],
            "Date Range" => [
                "id" => "date_range_start",
                "type" => "date",
                "id2" => "date_range_end",
                "type2" => "date",
                "oninput" => "runDynamicFilter()",
                "dataFilterPath" => "date",
            ]
        ];
    @endphp
    <!-- Modals -->
    {{-- article details modal --}}
    <div id="modal"
        class="mainModal hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-[var(--overlay-color)] fade-in">
    </div>
    
    <div class="w-[80%] mx-auto">
        <x-search-header heading="Expenses" :search_fields=$searchFields/>
    </div>
    
    <!-- Main Content -->
    <section class="text-center mx-auto ">
        <div
            class="show-box mx-auto w-[80%] h-[70vh] bg-[var(--secondary-bg-color)] rounded-xl shadow overflow-y-auto pt-7 pr-2 relative">
            <x-form-title-bar title="Show Expenses"/>

            @if (count($expenses) > 0)
                <div class="absolute bottom-3 right-3 flex items-center gap-2 w-fll z-50">
                    <x-section-navigation-button link="{{ route('expenses.create') }}" title="Add New Expense" icon="fa-plus" />
                </div>
                
                <div class="details h-full">
                    <div class="container-parent h-full overflow-y-auto my-scrollbar-2">
                        <div class="card_container p-5 pr-3">
                            <div class="grid grid-cols-7 bg-[var(--h-bg-color)] rounded-lg font-medium py-2">
                                <div class="text-center">Date</div>
                                <div class="text-center">Supplier Name</div>
                                <div class="text-center">Reff. No.</div>
                                <div class="text-center">Expense</div>
                                <div class="text-center">Lot No.</div>
                                <div class="text-center">Amount</div>
                                <div class="text-center">Remarks</div>
                            </div>
                            <div class="search_container overflow-y-auto grow my-scrollbar-2">
                                @forEach ($expenses as $expense)
                                    <div id="{{ $expense->id }}" data-json='{{ $expense }}' class="contextMenuToggle modalToggle relative group grid grid-cols-7 border-b border-[var(--h-bg-color)] items-center py-2 cursor-pointer hover:bg-[var(--h-secondary-bg-color)] transition-all fade-in ease-in-out">
                                        <span class="text-center">{{ $expense->date->format('d-M-Y, D') }}</span>
                                        <span class="text-center">{{ $expense->supplier->supplier_name }}</span>
                                        <span class="text-center">{{ $expense->reff_no }}</span>
                                        <span class="text-center capitalize">{{ $expense->expenseSetups->title }}</span>
                                        <span class="text-center">{{ $expense->lot_no ?? '-' }}</span>
                                        <span class="text-center">{{ number_format($expense->amount, 1) }}</span>
                                        <span class="text-center capitalize">{{ $expense->remarks ?? '-' }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <p id="noItemsError" style="display: none" class="text-sm text-[var(--border-error)] mt-3">No items found</p>
                    </div>
                </div>
            @else
                <div class="no-article-message w-full h-full flex flex-col items-center justify-center gap-2">
                    <h1 class="text-sm text-[var(--secondary-text)] capitalize">No Expense Found</h1>
                    <a href="{{ route('expenses.create') }}"
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
                </ul>
            </div>
        </div>
    </section>

    <script>
        let companyData = @json(app('company'));
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
                    generateModal(item, 'openModal');
                }
            });

            document.addEventListener('mousedown', (e) => {
                if (e.target.id === "print-order") {
                    generateModal(item, 'context');
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
                        generateModal(item, 'openModal');
                    }
                });
            });
        }

        function generateModal(item, context) {
            let modalDom = document.getElementById('modal')
            let data = JSON.parse(item.dataset.json);
            console.log(data);
            

            let totalAmount = 0;
            let totalQuantity = 0;
            let discount = data.discount;
            let previousBalance = data.previous_balance;
            let netAmount = data.netAmount;
            let currentBalance = data.current_balance;
            let cottonCount = data.cotton_count ? data.cotton_count : 0;

            modalDom.innerHTML = `
                <x-modal id="modalForm" closeAction="closeModal">
                    <!-- Modal Content Slot -->
                    <div class="flex items-start relative h-[15rem]">
                        <div class="flex-1 h-full overflow-y-auto my-scrollbar-2">
                            <div class="px-2">
                                <h5 id="name" class="text-2xl mb-2 text-[var(--text-color)] capitalize font-semibold leading-none">Expense: ${data.expense}</h5>
                                <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm"><strong>Supplier:</strong> <span>${data.supplier.supplier_name}</span></p>
                                <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm"><strong>Reff. No:</strong> <span>${data.reff_no}</span></p>
                                <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm"><strong>Lot No:</strong> <span>${data.lot_no}</span></p>
                                <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm"><strong>Date:</strong> <span>${formatDate(data.date)}</span></p>
                                <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm"><strong>Amount:</strong> <span>${formatNumbersWithDigits(data.amount)}</span></p>
                            </div>
                            
                            <hr class="border-gray-600 my-3"/>  

                            <div id="paymentDetails" class="px-2">
                                <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm capitalize"><strong>Remarks:</strong> <span>${data.remarks || "No Remarks"}</span></p>
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
            
            if (context == 'context') {
                document.getElementById('printOrder').click();
            } else {
                openModal();
            }
        }

        addListenerToCards();

        function openModal() {
            document.getElementById('modal').classList.remove('hidden');
            document.getElementById('modal').classList.add('flex');
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

        // Function for Search
        function filterData(search) {
            const filteredData = cardsDataArray.filter(item => {
                switch (filterType) {
                    case 'all':
                        return (
                            item.expense.toLowerCase().includes(search) ||
                            item.supplier.supplier_name.toLowerCase().includes(search) ||
                            item.reff_no.toString().includes(search)
                        );
                        break;
                        
                    case 'expense':
                        return (
                            item.expense.toLowerCase().includes(search)
                        );
                        break;
                        
                    case 'supplier':
                        return (
                            item.supplier.supplier_name.toLowerCase().includes(search)
                        );
                        break;
                        
                    case 'reff_no':
                        return (
                            item.reff_no.toString().includes(search)
                        );
                        break;
                
                    default:
                        return (
                            item.expense.toLowerCase().includes(search) ||
                            item.supplier.supplier_name.toLowerCase().includes(search) ||
                            item.reff_no.toString().includes(search)
                        );
                        break;
                }
            });

            return filteredData;
        }
    </script>
@endsection
