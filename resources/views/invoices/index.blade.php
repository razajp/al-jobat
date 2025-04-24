@extends('app')
@section('title', 'Show Articles | ' . app('company')->name)
@section('content')
    <!-- Modals -->
    <div id="modal"
        class="mainModal hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-[var(--overlay-color)] fade-in">
    </div>
    
    <div class="w-[80%] mx-auto">
        <x-search-header heading="Invoices" :filter_items="[
            'all' => 'All',
            'invoice_no' => 'Invoice No.',
            'order_no' => 'Order No.',
            'customer_name' => 'Customer Name',
            'date' => 'Date',
        ]"/>
    </div>

    <!-- Main Content -->
    <section class="text-center mx-auto ">
        <div
            class="show-box mx-auto w-[80%] h-[70vh] bg-[var(--secondary-bg-color)] rounded-xl shadow overflow-y-auto pt-7 pr-2 relative">
            <div
                class="form-title text-center absolute top-0 left-0 w-full bg-[var(--primary-color)] py-1 shadow-lg uppercase font-semibold text-sm">
                <h4>Show Invoices</h4>

                <div class="buttons absolute top-0 right-4 text-sm h-full flex items-center">
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

            @if (count($invoices) > 0)
                <div
                    class="add-new-article-btn absolute bottom-8 right-5 hover:scale-105 hover:bottom-9 transition-all group duration-300 ease-in-out">
                    <a href="{{ route('invoices.create') }}"
                        class="bg-[var(--primary-color)] text-[var(--text-color)] px-3 py-2 rounded-full hover:bg-[var(--h-primary-color)] transition-all duration-300 ease-in-out"><i
                            class="fas fa-plus"></i></a>
                    <span
                        class="absolute shadow-xl right-7 top-0 border border-gray-600 transform -translate-x-1/2 bg-[var(--secondary-bg-color)] text-[var(--text-color)] text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
                        Add
                    </span>
                </div>
            @endif

            @if (count($invoices) > 0)
                <div class="details h-full">
                    <div class="container-parent h-full overflow-y-auto my-scrollbar">
                        <div class="card_container p-5 pr-3">
                            @if ($authLayout == 'grid')
                                <div class="search_container grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                                    @foreach ($invoices as $invoice)
                                        <div id="{{ $invoice->id }}" data-json='{{ $invoice }}'
                                            class="contextMenuToggle modalToggle card relative border border-gray-600 shadow rounded-xl min-w-[100px] flex gap-4 py-4 px-5 cursor-pointer overflow-hidden fade-in">
                                            <x-card :data="[
                                                'name' => 'Invoice No: ' . $invoice->invoice_no,
                                                'details' => [
                                                    $invoice->order_no ? 'Order No.' : 'Shipment No.' => $invoice->order_no ?? $invoice->shipment_no,
                                                    'Customer Name' => $invoice->customer->customer_name,
                                                    'Date' => $invoice->date,
                                                ],
                                            ]" />
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="grid grid-cols-4 bg-[var(--h-bg-color)] rounded-lg font-medium py-2">
                                    <div class="text-center">Invoice No.</div>
                                    <div class="text-center">Order No.</div>
                                    <div class="text-center">Customer</div>
                                    <div class="text-center">Date</div>
                                </div>
                                <div class="search_container overflow-y-auto grow my-scrollbar-2">
                                    @forEach ($invoices as $invoice)
                                        <div id="{{ $invoice->id }}" data-json='{{ $invoice }}' class="contextMenuToggle modalToggle relative group grid text- grid-cols-4 border-b border-[var(--h-bg-color)] items-center py-2 cursor-pointer hover:bg-[var(--h-secondary-bg-color)] transition-all fade-in ease-in-out">
                                            <span class="text-center">{{ $invoice->invoice_no }}</span>
                                            <span class="text-center">{{ $invoice->order_no }}</span>
                                            <span class="text-center">{{ $invoice->customer->customer_name }}</span>
                                            <span class="text-center">{{ $invoice->date }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="no-article-message w-full h-full flex flex-col items-center justify-center gap-2">
                    <h1 class="text-sm text-[var(--secondary-text)] capitalize">No Invoice Found</h1>
                    <a href="{{ route('invoices.create') }}"
                        class="text-sm bg-[var(--primary-color)] text-[var(--text-color)] px-4 py-2 rounded-md hover:bg-[var(--h-primary-color)] hover:scale-105 hover:mb-2 transition-all 0.3s ease-in-out font-semibold">Add
                        New</a>
                </div>
            @endif
        </div>

        <div class="context-menu absolute top-0 left-0 text-sm z-50" style="display: none;">
            <div
                class="border border-gray-600 w-48 bg-[var(--secondary-bg-color)] text-[var(--text-color)] shadow-md rounded-xl transform transition-all 0.3s ease-in-out z-50">
                <ul class="p-2">
                    <li>
                        <button id="show-details" type="button"
                            class="w-full px-4 py-2 text-left hover:bg-[var(--h-bg-color)] rounded-md transition-all 0.3s ease-in-out">Show
                            Details</button>
                    </li>
                    <li>
                        <button id="print-invoice" type="button"
                            class="w-full px-4 py-2 text-left hover:bg-[var(--h-bg-color)] rounded-md transition-all 0.3s ease-in-out">Print
                            Invoice</button>
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

            document.addEventListener('click', (e) => {
                if (e.target.id === "show-details") {
                    generateModal(item);
                }
            });

            document.addEventListener('click', (e) => {
                if (e.target.id === "print-invoice") {
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
                document.addEventListener('click', removeContextMenu);
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

            let customerData = data.customer;

            let totalAmount = 0;
            let totalQuantity = 0;
            let discount = data.order?.discount ?? data.shipment?.discount;
            let netAmount = data.netAmount;
            let cottonCount = data.cotton_count ? 'Cotton: '+String(data.cotton_count).padStart(2, '0') : 0;

            modalDom.innerHTML = `
                <x-modal id="modalForm" classForBody="p-5 max-w-4xl h-[35rem] overflow-y-auto my-scrollbar-2 bg-white text-black" closeAction="closeModal">
                    <div id="preview-container" class="w-[210mm] h-[297mm] mx-auto overflow-hidden relative">
                        <div id="preview" class="preview flex flex-col h-full">
                            <div id="invoice" class="invoice flex flex-col h-full">
                                <div id="invoice-banner" class="invoice-banner w-full flex justify-between items-center mt-8 pl-5 pr-8">
                                    <div class="left">
                                        <div class="invoice-logo">
                                            <img src="{{ asset('images/${companyData.logo}') }}" alt="Track Point"
                                                class="w-[12rem]" />
                                            <div class='mt-1'>${ companyData.phone_number }</div>
                                        </div>
                                    </div>
                                    <div class="left">
                                        <div class="invoice-logo">
                                            <h1 class="text-2xl font-medium text-[var(--h-primary-color)] pr-2">Sales Invoice</h1>
                                            <div class="mt-1 text-right ${cottonCount == 0 ? 'hidden' : ''} pr-2">${cottonCount}</div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="w-full my-3 border-black">
                                <div id="invoice-header" class="invoice-header w-full flex justify-between px-5">
                                    <div class="left w-50 space-y-1">
                                        <div class="invoice-customer text-lg leading-none">M/s: ${customerData.customer_name}</div>
                                        <div class="invoice-person text-md text-lg leading-none">${customerData.urdu_title}</div>
                                        <div class="invoice-address text-md leading-none">${customerData.address}, ${customerData.city}</div>
                                        <div class="invoice-phone text-md leading-none">${customerData.phone_number}</div>
                                    </div>
                                    <div class="right my-auto pr-3 text-sm text-black space-y-1.5">
                                        <div class="invoice-date leading-none">Date: ${data.date}</div>
                                        <div class="invoice-number leading-none">Invoice No.: ${data.invoice_no}</div>
                                        <div class="invoice-copy leading-none">Invoice Copy: Customer</div>
                                        <div class="invoice-copy leading-none">Document: Sales Invoice</div>
                                    </div>
                                </div>
                                <hr class="w-full my-3 border-black">
                                <div id="invoice-body" class="invoice-body w-[95%] grow mx-auto">
                                    <div class="invoice-table w-full">
                                        <div class="table w-full border border-black rounded-lg pb-2.5 overflow-hidden">
                                            <div class="thead w-full">
                                                <div class="tr flex justify-between w-full px-4 py-1.5 bg-[var(--primary-color)] text-white">
                                                    <div class="th text-sm font-medium w-[7%]">S.No</div>
                                                    <div class="th text-sm font-medium w-[10%]">Article</div>
                                                    <div class="th text-sm font-medium w-[10%]">Packets</div>
                                                    <div class="th text-sm font-medium w-[10%]">Pcs.</div>
                                                    <div class="th text-sm font-medium grow">Description</div>
                                                    <div class="th text-sm font-medium w-[10%]">Pcs/Pkt.</div>
                                                    <div class="th text-sm font-medium w-[11%]">Rate/Pc.</div>
                                                    <div class="th text-sm font-medium w-[11%]">Amount</div>
                                                </div>
                                            </div>
                                            <div id="tbody" class="tbody w-full">
                                                ${data.articles.map((articles, index) => {
                                                    totalAmount += parseInt(articles.article.sales_rate) * articles.invoice_quantity;
                                                    totalQuantity += articles.invoice_quantity;
                                                    const hrClass = index === 0 ? "mb-2.5" : "my-2.5";

                                                    return `
                                                        <div>
                                                            <hr class="w-full ${hrClass} border-black">
                                                            <div class="tr flex justify-between w-full px-4">
                                                                <div class="td text-sm font-semibold w-[7%]">${index + 1}.</div>
                                                                <div class="td text-sm font-semibold w-[10%]">#${articles.article.article_no}</div>
                                                                <div class="td text-sm font-semibold w-[10%]">${articles.invoice_quantity / articles.article.pcs_per_packet}</div>
                                                                <div class="td text-sm font-semibold w-[10%]">${articles.invoice_quantity}</div>
                                                                <div class="td text-sm font-semibold grow">${articles.description}</div>
                                                                <div class="td text-sm font-semibold w-[10%]">${formatNumbersDigitLess(articles.article.pcs_per_packet)}</div>
                                                                <div class="td text-sm font-semibold w-[11%]">${formatNumbersWithDigits(articles.article.sales_rate, 2, 2)}</div>
                                                                <div class="td text-sm font-semibold w-[11%]">${formatNumbersWithDigits(parseInt(articles.article.sales_rate) * articles.invoice_quantity, 1, 1)}</div>
                                                            </div>
                                                        </div>
                                                    `;
                                                }).join('')}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="w-full my-3 border-black">
                                <div class="flex flex-col space-y-2">
                                    <div id="invoice-total" class="tr flex justify-between w-full px-2 gap-2 text-sm">
                                        <div class="total flex justify-between items-center border border-black rounded-lg py-1.5 px-4 w-full">
                                            <div class="text-nowrap">Total Quantity - Pcs</div>
                                            <div class="w-1/2 text-right grow">${formatNumbersDigitLess(totalQuantity)}</div>
                                        </div>
                                        <div class="total flex justify-between items-center border border-black rounded-lg py-1.5 px-4 w-full">
                                            <div class="text-nowrap">Gross Amount</div>
                                            <div class="w-1/2 text-right grow">${formatNumbersWithDigits(totalAmount, 1, 1)}</div>
                                        </div>
                                    </div>
                                    <div id="invoice-total" class="tr flex justify-between w-full px-2 gap-2 text-sm">
                                        <div class="total flex justify-between items-center border border-black rounded-lg py-1.5 px-4 w-full">
                                            <div class="text-nowrap">Discount</div>
                                            <div class="w-1/2 text-right grow">${formatNumbersDigitLess(discount)}</div>
                                        </div>
                                        <div
                                            class="total flex justify-between items-center border border-black rounded-lg py-1.5 px-4 w-full">
                                            <div class="text-nowrap">Net Amount</div>
                                            <div class="w-1/2 text-right grow">${formatNumbersWithDigits(netAmount, 1, 1)}</div>
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
                        <button type="button" id="printInvoice"
                            class="px-4 py-2 bg-[var(--secondary-bg-color)] border border-gray-600 text-nowrap text-[var(--secondary-text)] rounded-lg hover:bg-[var(--h-bg-color)] transition-all 0.3s ease-in-out">
                            Print Invoice
                        </button>

                        <button onclick="closeModal()" type="button"
                            class="px-4 py-2 bg-[var(--secondary-bg-color)] border border-gray-600 text-[var(--secondary-text)] rounded-lg hover:bg-[var(--h-bg-color)] transition-all 0.3s ease-in-out">
                            Cancel
                        </button>
                    </x-slot>
                </x-modal>
                `;

            addListenerToPrintInvoice()
            if (context == 'context') {
                document.getElementById('printInvoice').click();
            } else {
                openModal();
            }
        }

        addListenerToCards();

        function openModal() {
            isModalOpened = true;
            closeAllDropdowns();
            closeContextMenu();
            document.getElementById('modal').classList.remove('hidden');
            document.getElementById('modal').classList.add('flex');
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
                            item.invoice_no.toString().includes(search) ||
                            item.order_no.toString().includes(search) ||
                            item.order.customer.customer_name.toLowerCase().includes(search) ||
                            item.date.toLowerCase().includes(search)
                        );
                        break;
                        
                    case 'invoice_no':
                        return (
                            item.invoice_no.toString().includes(search)
                        );
                        break;
                        
                    case 'order_no':
                        return (
                            item.order_no.toString().includes(search)
                        );
                        break;
                        
                    case 'customer_name':
                        return (
                            item.order.customer.customer_name.toLowerCase().includes(search)
                        );
                        break;
                        
                    case 'date':
                        return (
                            item.date.toLowerCase().includes(search)
                        );
                        break;
                
                    default:
                        return (
                            item.invoice_no.toString().includes(search) ||
                            item.order_no.toString().includes(search) ||
                            item.order.customer.customer_name.toLowerCase().includes(search) ||
                            item.date.toLowerCase().includes(search)
                        );
                        break;
                }
            });

            return filteredData;
        }

        function addListenerToPrintInvoice() {
            document.getElementById('printInvoice').addEventListener('click', (e) => {
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
                            <title>Print Invoice</title>
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
                    let orderCopy = printDocument.querySelector('#preview-container .invoice-copy');

                    if (orderCopy) {
                        orderCopy.textContent = "Invoice Copy: Office"; // Change text to "invoice Copy: Office"
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
