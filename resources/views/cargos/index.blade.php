@extends('app')
@section('title', 'Show Cargo Lists | ' . app('company')->name)
@section('content')
    <!-- Modals -->
    <div id="modal"
        class="mainModal hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-[var(--overlay-color)] fade-in">
    </div>
    
    <div class="w-[80%] mx-auto">
        <x-search-header heading="Cargo Lists" :filter_items="[
            'all' => 'All',
            'cargo_no' => 'Shipment No.',
            'customer_name' => 'Customer Name',
            'date' => 'Date',
        ]"/>
    </div>
    
    <!-- Main Content -->
    <section class="text-center mx-auto ">
        <div
            class="show-box mx-auto w-[80%] h-[70vh] bg-[var(--secondary-bg-color)] rounded-xl shadow overflow-y-auto pt-7 pr-2 relative">
            <x-form-title-bar title="Show Cargo Lists" changeLayoutBtn layout="{{ $authLayout }}" />

            @if (count($cargos) > 0)
                <div
                    class="add-new-article-btn absolute z-[999] bottom-8 right-5 hover:scale-105 hover:bottom-9 transition-all group duration-300 ease-in-out">
                    <a href="{{ route('cargos.create') }}"
                        class="bg-[var(--primary-color)] text-[var(--text-color)] px-3 py-2 rounded-full hover:bg-[var(--h-primary-color)] transition-all duration-300 ease-in-out"><i
                            class="fas fa-plus"></i></a>
                    <span
                        class="absolute shadow-xl right-7 top-0 border border-gray-600 transform -translate-x-1/2 bg-[var(--secondary-bg-color)] text-[var(--text-color)] text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
                        Add
                    </span>
                </div>
            @endif

            @if (count($cargos) > 0)
                <div class="details h-full">
                    <div class="container-parent h-full overflow-y-auto my-scrollbar-2">
                        <div class="card_container p-5 pr-3">
                            @if ($authLayout == 'grid')
                                <div class="search_container grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">
                                    @foreach ($cargos as $cargo)
                                        <div id="{{ $cargo->id }}" data-json='{{ $cargo }}'
                                            class="contextMenuToggle modalToggle card relative border border-gray-600 shadow rounded-xl min-w-[100px] flex gap-4 py-4 px-5 cursor-pointer overflow-hidden fade-in">
                                            <x-card :data="[
                                                'name' => 'Cargo No: ' . $cargo->cargo_no,
                                                'details' => [
                                                    'Date' => $cargo->date->format('d-M-Y, D'),
                                                ],
                                            ]" />
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="grid grid-cols-3 bg-[var(--h-bg-color)] rounded-lg font-medium py-2">
                                    <div class="text-center">Cargo No.</div>
                                    <div class="text-center">Date</div>
                                </div>
                                <div class="search_container overflow-y-auto grow my-scrollbar-2">
                                    @forEach ($cargos as $cargo)
                                        <div id="{{ $cargo->id }}" data-json='{{ $cargo }}' class="contextMenuToggle modalToggle relative group grid grid-cols-3 border-b border-[var(--h-bg-color)] items-center py-2 cursor-pointer hover:bg-[var(--h-secondary-bg-color)] transition-all fade-in ease-in-out">
                                            <span class="text-center">{{ $cargo->cargo_no }}</span>
                                            <span class="text-center">{{ $cargo->date }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="no-article-message w-full h-full flex flex-col items-center justify-center gap-2">
                    <h1 class="text-sm text-[var(--secondary-text)] capitalize">No List Found</h1>
                    <a href="{{ route('cargos.create') }}"
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
                            class="w-full px-4 py-2 text-left cursor-pointer hover:bg-[var(--h-bg-color)] rounded-md transition-all duration-300 ease-in-out">Show
                            Details</button>
                    </li>
                    <li>
                        <button id="print-list" type="button"
                            class="w-full px-4 py-2 text-left cursor-pointer hover:bg-[var(--h-bg-color)] rounded-md transition-all duration-300 ease-in-out">Print
                            List</button>
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
                if (e.target.id === "print-list") {
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
            let netAmount = data.netAmount;

            modalDom.innerHTML = `
                <x-modal id="modalForm" classForBody="p-5 max-w-4xl h-[35rem] overflow-y-auto my-scrollbar-2 bg-white text-black" closeAction="closeModal" action="{{ route('update-user-status') }}">
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
                                        <div class="text-right">
                                            <h1 class="text-2xl font-medium text-[var(--primary-color)] pr-2">Cargo List</h1>
                                            <div class='mt-1'>${ companyData.phone_number }</div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="w-full my-3 border-black">
                                <div id="preview-header" class="preview-header w-full flex justify-between px-5">
                                    <div class="left my-auto pr-3 text-sm text-gray-600 space-y-1.5">
                                        <div class="cargo-date leading-none">Date: ${formatDate(data.date)}</div>
                                        <div class="cargo-number leading-none">Cargo No.: ${data.cargo_no}</div>
                                    </div>
                                    <div class="center my-auto">
                                        <div class="cargo-name capitalize font-semibold text-md">Cargo Name: ${data.cargo_name}</div>
                                    </div>
                                    <div class="right my-auto pr-3 text-sm text-gray-600 space-y-1.5">
                                        <div class="preview-copy leading-none">Cargo List Copy: Cargo</div>
                                        <div class="preview-doc leading-none">Document: Cargo List</div>
                                    </div>
                                </div>
                                <hr class="w-full my-3 border-black">
                                <div id="preview-body" class="preview-body w-[95%] grow mx-auto">
                                    <div class="preview-table w-full">
                                        <div class="table w-full border border-black rounded-lg pb-2.5 overflow-hidden">
                                            <div class="thead w-full">
                                                <div class="tr flex justify-between w-full px-4 py-1.5 bg-[var(--primary-color)] text-white">
                                                    <div class="th text-sm font-medium w-[7%]">S.No</div>
                                                    <div class="th text-sm font-medium w-1/5">Date</div>
                                                    <div class="th text-sm font-medium w-1/6">Invoice No.</div>
                                                    <div class="th text-sm font-medium w-1/6">Cotton</div>
                                                    <div class="th text-sm font-medium grow">Customer</div>
                                                    <div class="th text-sm font-medium w-[12%]">City</div>
                                                </div>
                                            </div>
                                            <div id="tbody" class="tbody w-full">
                                                ${data.invoices.map((invoice, index) => {
                                                    const hrClass = index === 0 ? "mb-2.5" : "my-2.5";

                                                    return `
                                                        <div>
                                                            <hr class="w-full ${hrClass} border-black">
                                                            <div class="tr flex justify-between w-full px-4">
                                                                <div class="td text-sm font-semibold w-[7%]">${index + 1}.</div>
                                                                <div class="td text-sm font-semibold w-1/5">${formatDate(invoice.date)}</div>
                                                                <div class="td text-sm font-semibold w-1/6">${invoice.invoice_no}</div>
                                                                <div class="td text-sm font-semibold w-1/6">${invoice.cotton_count}</div>
                                                                <div class="td text-sm font-semibold grow capitalize">${invoice.customer.customer_name}</div>
                                                                <div class="td text-sm font-semibold w-[12%]">${invoice.customer.city}</div>
                                                            </div>
                                                        </div>
                                                    `;
                                                }).join('')}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="w-full my-3 border-black">
                                <div class="tfooter flex w-full text-sm px-4 justify-between mb-4 text-black">
                                    <P class="leading-none">${ companyData.name } | ${ companyData.address }</P>
                                    <p class="leading-none text-sm">&copy; 2025 Spark Pair | +92 316 5825495</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal Action Slot -->
                    <x-slot name="actions">
                        <button type="button" id="printList"
                            class="px-4 py-2 bg-[var(--secondary-bg-color)] border border-gray-600 text-nowrap text-[var(--secondary-text)] rounded-lg hover:bg-[var(--h-bg-color)] transition-all duration-300 ease-in-out cursor-pointer hover:scale-[0.95]">
                            Print List
                        </button>

                        <button onclick="closeModal()" type="button"
                            class="px-4 py-2 bg-[var(--secondary-bg-color)] border border-gray-600 text-[var(--secondary-text)] rounded-lg hover:bg-[var(--h-bg-color)] transition-all duration-300 ease-in-out cursor-pointer hover:scale-[0.95]">
                            Cancel
                        </button>
                    </x-slot>
                </x-modal>
            `;
            
            addListenerToPrintShipment();
            if (context == 'context') {
                document.getElementById('printList').click();
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
                            item.cargo_no.toString().includes(search) ||
                            item.customer.customer_name.toLowerCase().includes(search) ||
                            item.date.toLowerCase().includes(search)
                        );
                        break;
                        
                    case 'cargo_no':
                        return (
                            item.cargo_no.toLowerCase().includes(search)
                        );
                        break;
                        
                    case 'customer_name':
                        return (
                            item.customer.customer_name.toLowerCase().includes(search)
                        );
                        break;
                        
                    case 'date':
                        return (
                            item.date.toLowerCase().includes(search)
                        );
                        break;
                
                    default:
                        return (
                            item.cargo_no.toString().includes(search) ||
                            item.customer.customer_name.toLowerCase().includes(search) ||
                            item.date.toLowerCase().includes(search)
                        );
                        break;
                }
            });

            return filteredData;
        }
        
        function addListenerToPrintShipment() {
            document.getElementById('printList').addEventListener('click', (e) => {
                e.preventDefault();
                closeAllDropdowns();
                const preview = document.getElementById('preview-container'); // preview content

                // Pehle se agar koi iframe hai to usko remove karein
                let oldIframe = document.getElementById('printIframe');
                if (oldIframe) {
                    oldIframe.remove();
                }

                // Naya iframe banayein
                let printIframe = document.createElement('iframe');
                printIframe.id = "printIframe";
                printIframe.style.position = "absolute";
                printIframe.style.width = "0px";
                printIframe.style.height = "0px";
                printIframe.style.border = "none";
                printIframe.style.display = "none"; // ✅ Hide iframe

                // Iframe ko body me add karein
                document.body.appendChild(printIframe);

                let printDocument = printIframe.contentDocument || printIframe.contentWindow.document;
                printDocument.open();

                // ✅ Current page ke CSS styles bhi iframe me inject karenge
                const headContent = document.head.innerHTML;

                printDocument.write(`
                    <html>
                        <head>
                            <title>Print Cargo List</title>
                            ${headContent} <!-- Copy current styles -->
                            <style>
                                @media print {

                                    body {
                                        margin: 0;
                                        padding: 0;
                                        width: 210mm; /* A4 width */
                                        height: 297mm; /* A4 height */
                                        
                                    }

                                    .preview-container, .preview-container * {
                                        page-break-inside: avoid;
                                    }
                                }
                            </style>
                        </head>
                        <body>
                            <div class="preview-container pt-3">${preview.innerHTML}</div> <!-- Add the preview content, only innerHTML -->
                            <div id="preview-container" class="preview-container pt-3">${preview.innerHTML}</div> <!-- Add the preview content, only innerHTML -->
                        </body>
                    </html>
                `);

                printDocument.close();

                // Wait for iframe to load and print
                printIframe.onload = () => {

                    // Select the preview-copy div and update its text
                    let previewCopy = printDocument.querySelector('#preview-container .preview-copy');

                    if (previewCopy) {
                        previewCopy.textContent = "Cargo List Copy: Office"; // Change text to "preview Copy: Office"
                    }

                    setTimeout(() => {
                        printIframe.contentWindow.focus();
                        printIframe.contentWindow.print();
                        document.body.removeChild(printIframe); // Remove iframe after printing
                    }, 1000);
                }
            });
        }
    </script>
@endsection
