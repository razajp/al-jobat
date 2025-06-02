@extends('app')
@section('title', 'Show Vouchers | ' . app('company')->name)
@section('content')
    <!-- Modals -->
    <div id="modal"
        class="mainModal hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-[var(--overlay-color)] fade-in">
    </div>
    
    <div class="w-[80%] mx-auto">
        <x-search-header heading="Vouchers" :filter_items="[
            'all' => 'All',
            'supplier_name' => 'Supplier Name',
            'type' => 'Type',
            'method' => 'Method',
            'date' => 'Date',
        ]"/>
    </div>
    
    <!-- Main Content -->
    <section class="text-center mx-auto ">
        <div
            class="show-box mx-auto w-[80%] h-[70vh] bg-[var(--secondary-bg-color)] rounded-xl shadow overflow-y-auto pt-7 pr-2 relative">
            <x-form-title-bar title="Show Vouchers" changeLayoutBtn layout="{{ $authLayout }}" />

            @if (count($vouchers) > 0)
                <div class="absolute bottom-3 right-3 flex items-center gap-2 w-fll z-50">
                    <x-section-navigation-button link="{{ route('vouchers.create') }}" title="Add New Payment" icon="fa-plus" />
                </div>
                
                <div class="details h-full">
                    <div class="container-parent h-full overflow-y-auto my-scrollbar-2">
                        <div class="card_container p-5 pr-3">
                            @if ($authLayout == 'grid')
                                <div class="search_container grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                                    @foreach ($vouchers as $voucher)
                                        <div id="{{ $voucher->id }}" data-json='{{ $voucher }}'
                                            class="contextMenuToggle modalToggle card relative border border-gray-600 shadow rounded-xl min-w-[100px] flex gap-4 py-4 px-5 cursor-pointer overflow-hidden fade-in">
                                            <x-card :data="[
                                                'name' => 'Voucher No.: ' . $voucher->voucher_no,
                                                'details' => [
                                                    'Supplier' => str_replace('_', ' ',$voucher->supplier->supplier_name),
                                                    'Date' => date('d-M-Y D', strtotime($voucher->date)),
                                                    'Amount' => number_format($voucher->total_payment, 1),
                                                ],
                                            ]" />
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="grid grid-cols-4 bg-[var(--h-bg-color)] rounded-lg font-medium py-2">
                                    <div class="text-center">Supplier</div>
                                    <div class="text-center">Type</div>
                                    <div class="text-center">Date</div>
                                    <div class="text-center">Amount</div>
                                </div>
                                <div class="search_container overflow-y-auto grow my-scrollbar-2">
                                    @forEach ($vouchers as $voucher)
                                        <div id="{{ $voucher->id }}" data-json='{{ $voucher }}' class="contextMenuToggle modalToggle relative group grid text- grid-cols-4 border-b border-[var(--h-bg-color)] items-center py-2 cursor-pointer hover:bg-[var(--h-secondary-bg-color)] transition-all fade-in ease-in-out">
                                            <span class="text-center">{{ $voucher->supplier->supplier_name }}</span>
                                            <span class="text-center">{{ str_replace('_', ' ',$voucher->type) }}</span>
                                            <span class="text-center">{{ date('d-M-Y D', strtotime($voucher->date)) }}</span>
                                            <span class="text-center">{{ number_format($voucher->amount, 1) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="no-article-message w-full h-full flex flex-col items-center justify-center gap-2">
                    <h1 class="text-sm text-[var(--secondary-text)] capitalize">No Vouchers Found</h1>
                    <a href="{{ route('vouchers.create') }}"
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
        
        let companyData = @json(app('company'));
        function generateModal(item) {
            let modalDom = document.getElementById('modal')
            let data = JSON.parse(item.dataset.json);

            modalDom.innerHTML = `
                <x-modal id="modalForm" classForBody="p-5 max-w-4xl h-[35rem] overflow-y-auto my-scrollbar-2 bg-white text-black" closeAction="closeModal">
                    <div id="preview-container" class="w-[210mm] h-[297mm] mx-auto overflow-hidden relative">
                        <div id="preview" class="preview flex flex-col h-full">
                            <div id="preview-document" class="preview-document flex flex-col h-full">
                                <div id="preview-banner" class="preview-banner w-full flex justify-between items-center mt-8 pl-5 pr-8">
                                    <div class="left">
                                        <div class="company-logo">
                                            <img src="{{ asset('images/${companyData.logo}') }}" alt="Track Point"
                                                class="w-[12rem]" />
                                        </div>
                                    </div>
                                    <div class="right">
                                        <div>
                                            <h1 class="text-2xl font-medium text-[var(--primary-color)] pr-2">Payment Voucher</h1>
                                            <div class='mt-1'>${ companyData.phone_number }</div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="w-full my-3 border-gray-600">
                                <div id="preview-header" class="preview-header w-full flex justify-between px-5">
                                    <div class="left my-auto pr-3 text-sm text-gray-600 space-y-1.5">
                                        <div class="voucher-date leading-none">Date: ${formatDate(data.date)}</div>
                                        <div class="voucher-number leading-none">Voucher No.: ${data.voucher_no}</div>
                                    </div>
                                    <div class="center my-auto">
                                        <div class="supplier-name capitalize font-semibold text-md">Supplier Name: ${data.supplier.supplier_name}</div>
                                    </div>
                                    <div class="right my-auto pr-3 text-sm text-gray-600 space-y-1.5">
                                        <div class="preview-copy leading-none">Voucher Copy: Supplier</div>
                                        <div class="preview-doc leading-none">Document: Payment Voucher</div>
                                    </div>
                                </div>
                                <hr class="w-full my-3 border-gray-600">
                                <div id="preview-body" class="preview-body w-[95%] grow mx-auto">
                                    <div class="preview-table w-full">
                                        <div class="table w-full border border-gray-600 rounded-lg pb-2.5 overflow-hidden">
                                            <div class="thead w-full">
                                                <div class="tr flex justify-between w-full px-4 py-1.5 bg-[var(--primary-color)] text-white">
                                                    <div class="th text-sm font-medium w-[7%]">S.No</div>
                                                    <div class="th text-sm font-medium w-1/6">Method</div>
                                                    <div class="th text-sm font-medium w-1/6">C./S. NO.</div>
                                                    <div class="th text-sm font-medium w-1/6">C./S. Date</div>
                                                    <div class="th text-sm font-medium grow">-</div>
                                                    <div class="th text-sm font-medium w-1/6">Amount</div>
                                                </div>
                                            </div>
                                            <div id="tbody" class="tbody w-full">
                                                ${data.supplier_payments.map((payment, index) => {
                                                    const hrClass = index === 0 ? "mb-2.5" : "my-2.5";
                                                    return `
                                                            <div>
                                                                <hr class="w-full ${hrClass} border-gray-600">
                                                                <div class="tr flex justify-between w-full px-4">
                                                                    <div class="td text-sm font-semibold w-[7%]">${index + 1}.</div>
                                                                    <div class="td text-sm font-semibold w-1/6">${payment.method ?? '-'}</div>
                                                                    <div class="td text-sm font-semibold w-1/6">${payment.cheque_no ?? payment.slip_no ?? '-'}</div>
                                                                    <div class="td text-sm font-semibold w-1/6">${payment.cheque_date ?? payment.slip_date ?? '-'}</div>
                                                                    <div class="td text-sm font-semibold grow">${'-'}</div>
                                                                    <div class="td text-sm font-semibold w-1/6">${payment.amount ?? '-'}</div>
                                                                </div>
                                                            </div>
                                                        `;
                                                }).join('')}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="w-full my-3 border-gray-600">
                                <div class="flex flex-col space-y-2">
                                    <div id="total" class="tr flex justify-between w-full px-2 gap-2 text-sm">
                                        <div class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                                            <div class="text-nowrap">Previous Balance - Rs</div>
                                            <div class="w-1/4 text-right grow">${formatNumbersWithDigits(data.previous_balance, 1, 1)}</div>
                                        </div>
                                        <div class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                                            <div class="text-nowrap">Total Payment - Rs</div>
                                            <div class="w-1/4 text-right grow">${formatNumbersWithDigits(data.total_payment, 1, 1)}</div>
                                        </div>
                                        <div class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                                            <div class="text-nowrap">Current Balance - Rs</div>
                                            <div class="w-1/4 text-right grow">${formatNumbersWithDigits(data.previous_balance - data.total_payment, 1, 1)}</div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="w-full my-3 border-gray-600">
                                <div class="tfooter flex w-full text-sm px-4 justify-between mb-4 text-gray-600">
                                    <P class="leading-none">${ companyData.name } | ${ companyData.address }</P>
                                    <p class="leading-none text-sm">&copy; 2025 Spark Pair | +92 316 5825495</p>
                                </div>
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

        // Function for Search
        function filterData(search) {
            const filteredData = cardsDataArray.filter(item => {
                switch (filterType) {
                    case 'all':
                        return (
                            item.supplier.supplier_name.toLowerCase().includes(search) ||
                            item.type.toLowerCase().includes(search) ||
                            item.method.toLowerCase().includes(search) ||
                            item.date.toLowerCase().includes(search)
                        );
                        break;
                        
                    case 'supplier_name':
                        return (
                            item.supplier.supplier_name.toLowerCase().includes(search)
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
                            item.supplier.supplier_name.toLowerCase().includes(search) ||
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
