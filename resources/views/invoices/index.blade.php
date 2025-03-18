@extends('app')
@section('title', 'Show Articles | ' . app('company')->name)
@section('content')
    <!-- Modals -->
    <div id="modal"
        class="mainModal hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-black bg-opacity-50 fade-in">
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
            class="show-box mx-auto w-[80%] h-[70vh] bg-[--secondary-bg-color] rounded-xl shadow overflow-y-auto pt-7 pr-2 relative">
            <div
                class="form-title text-center absolute top-0 left-0 w-full bg-[--primary-color] py-1 shadow-lg uppercase font-semibold text-sm">
                <h4>Show Invoices</h4>
            </div>

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

            @if (count($invoices) > 0)
                <div
                    class="add-new-article-btn absolute bottom-8 right-5 hover:scale-105 hover:bottom-9 transition-all group duration-300 ease-in-out">
                    <a href="{{ route('invoices.create') }}"
                        class="bg-[--primary-color] text-[--text-color] px-3 py-2 rounded-full hover:bg-[--h-primary-color] transition-all duration-300 ease-in-out"><i
                            class="fas fa-plus"></i></a>
                    <span
                        class="absolute shadow-xl right-7 top-0 border border-gray-600 transform -translate-x-1/2 bg-[--secondary-bg-color] text-[--text-color] text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
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
                                                    'Order No.' => $invoice->order_no,
                                                    'Customer Name' => $invoice->order->customer->customer_name,
                                                    'Date' => $invoice->date,
                                                ],
                                            ]" />
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="grid grid-cols-4 bg-[--h-bg-color] rounded-lg font-medium py-2">
                                    <div class="text-center">Invoice No.</div>
                                    <div class="text-center">Order No.</div>
                                    <div class="text-center">Customer</div>
                                    <div class="text-center">Date</div>
                                </div>
                                <div class="search_container overflow-y-auto grow my-scrollbar-2">
                                    @forEach ($invoices as $invoice)
                                        <div id="{{ $invoice->id }}" data-json='{{ $invoice }}' class="contextMenuToggle modalToggle relative group grid text- grid-cols-4 border-b border-[--h-bg-color] items-center py-2 cursor-pointer hover:bg-[--h-secondary-bg-color] transition-all fade-in ease-in-out">
                                            <span class="text-center">{{ $invoice->invoice_no }}</span>
                                            <span class="text-center">{{ $invoice->order_no }}</span>
                                            <span class="text-center">{{ $invoice->order->customer->customer_name }}</span>
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
                    <h1 class="text-sm text-[--secondary-text] capitalize">No Order Found</h1>
                    <a href="{{ route('orders.create') }}"
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

        $('#article_no_search').on('input', function(e) {
            e.preventDefault();

            $(this).blur();

            submitForm();

            setTimeout(() => {
                $(this).focus();
            }, 100);
        });

        $('#search-form').on('change', 'select', function(e) {
            if (e.type === 'keydown' && e.key !== 'Enter')
                return;
            e.preventDefault();
            submitForm();
        });

        function submitForm() {
            let formData = $('#search-form').serialize();

            $.ajax({
                url: $('#search-form').attr('action'),
                method: 'GET',
                data: formData,
                success: function(response) {
                    const articles = $(response).find('.details').html();

                    if (articles === undefined || articles.trim() === "") {
                        $('.details').html(
                            '<div class="text-center text-[--border-error] pt-5 col-span-4">Article Not Found</div>'
                        );
                    } else {
                        $('.details').html(articles);
                        addListenerToCards();
                        addContextMenuListenerToCards();
                    };
                },
                error: function() {
                    alert('Error submitting form');
                }
            });
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

            let customerData = data.order.customer;

            let totalAmount = 0;
            let totalQuantity = 0;
            let discount = data.order.discount;
            let netAmount = data.netAmount;

            modalDom.innerHTML = `
             <x-modal id="modalForm" classForBody="p-5 max-w-4xl h-[35rem] overflow-y-auto my-scrollbar-2 bg-white text-black" closeAction="closeModal" action="{{ route('update-user-status') }}">
                    <div id="preview-container" class="w-[210mm] h-[297mm] mx-auto overflow-hidden relative">
                        <div id="preview" class="preview flex flex-col h-full">
                                <div id="invoice" class="invoice flex flex-col h-full">
                                    <div id="invoice-banner" class="invoice-banner w-full flex justify-between mt-8 px-5">
                                        <div class="left w-50">
                                            <div class="invoice-logo">
                                                <img src="{{ asset('images/${companyData.logo}') }}" alt="Track Point"
                                                    class="w-[150px]" />
                                            </div>
                                        </div>
                                        <div class="right w-50 my-auto pr-3 text-sm text-gray-500">
                                            <div class="invoice-date">Date: ${data.date}</div>
                                            <div class="invoice-number">Invoice No.: ${data.invoice_no}</div>
                                            <div class="invoice-copy">Invoice Copy: Customer</div>
                                        </div>
                                    </div>
                                    <hr class="w-100 my-5 border-gray-600">
                                    <div id="invoice-header" class="invoice-header w-full flex justify-between px-5">
                                        <div class="left w-50">
                                            <div class="invoice-to text-sm text-gray-500">Invoice to:</div>
                                            <div class="invoice-customer text-lg">${customerData.customer_name}</div>
                                            <div class="invoice-person text-md">${customerData.person_name}</div>
                                            <div class="invoice-address text-md">${customerData.address}, ${customerData.city}</div>
                                            <div class="invoice-phone text-md">${customerData.phone_number}</div>
                                        </div>
                                        <div class="right w-50">
                                            <div class="invoice-from text-sm text-gray-500">Invoice from:</div>
                                            <div class="invoice-customer text-lg">${companyData.name}</div>
                                            <div class="invoice-person text-md">${companyData.owner_name}</div>
                                            <div class="invoice-address text-md">${companyData.city}, ${companyData.address}</div>
                                            <div class="invoice-phone text-md">${companyData.phone_number}</div>
                                        </div>
                                    </div>
                                    <hr class="w-100 mt-5 mb-5 border-gray-600">
                                    <div id="invoice-body" class="invoice-body w-[95%] grow mx-auto">
                                        <div class="invoice-table w-full">
                                            <div class="table w-full border border-gray-600 rounded-lg pb-4 overflow-hidden">
                                                <div class="thead w-full">
                                                    <div class="tr flex justify-between w-full px-4 py-2 bg-[--primary-color] text-white">
                                                        <div class="th text-sm font-medium w-[5%]">#</div>
                                                        <div class="th text-sm font-medium w-[11%]">Article</div>
                                                        <div class="th text-sm font-medium w-[11%]">Packets</div>
                                                        <div class="th text-sm font-medium w-[10%]">Pcs</div>
                                                        <div class="th text-sm font-medium grow">Desc.</div>
                                                        <div class="th text-sm font-medium w-[8%]">Pcs/Pkt</div>
                                                        <div class="th text-sm font-medium w-[12%]">Rate/Pc</div>
                                                        <div class="th text-sm font-medium w-[15%]">Amount</div>
                                                    </div>
                                                </div>
                                                <div id="tbody" class="tbody w-full">
                                                    ${data.articles.map((articles, index) => {
                                                        totalAmount += parseInt(articles.article.sales_rate) * articles.invoice_quantity;
                                                        totalQuantity += articles.invoice_quantity;

                                                        if (index == 0) {
                                                            return `
                                                                    <div>
                                                                        <hr class="w-full mb-3 border-gray-600">
                                                                        <div class="tr flex justify-between w-full px-4">
                                                                            <div class="td text-sm font-semibold w-[5%]">${index + 1}.</div>
                                                                            <div class="td text-sm font-semibold w-[11%]">#${articles.article.article_no}</div>
                                                                            <div class="td text-sm font-semibold w-[11%]">${articles.invoice_quantity / articles.article.pcs_per_packet}</div>
                                                                            <div class="td text-sm font-semibold w-[10%]">${articles.invoice_quantity}</div>
                                                                            <div class="td text-sm font-semibold grow">${articles.description}</div>
                                                                            <div class="td text-sm font-semibold w-[8%]">${formatNumbersDigitLess(articles.article.pcs_per_packet)}</div>
                                                                            <div class="td text-sm font-semibold w-[12%]">${formatNumbersWithDigits(articles.article.sales_rate, 2, 2)}</div>
                                                                            <div class="td text-sm font-semibold w-[15%]">${formatNumbersWithDigits(parseInt(articles.article.sales_rate) * articles.invoice_quantity, 1, 1)}</div>
                                                                        </div>
                                                                    </div>
                                                                `;
                                                        } else {
                                                            return `
                                                                    <div>
                                                                        <hr class="w-full my-3 border-gray-600">
                                                                        <div class="tr flex justify-between w-full px-4">
                                                                            <div class="td text-sm font-semibold w-[5%]">${index + 1}.</div>
                                                                            <div class="td text-sm font-semibold w-[11%]">#${articles.article.article_no}</div>
                                                                            <div class="td text-sm font-semibold w-[11%]">${articles.invoice_quantity / articles.article.pcs_per_packet}</div>
                                                                            <div class="td text-sm font-semibold w-[10%]">${articles.invoice_quantity}</div>
                                                                            <div class="td text-sm font-semibold grow">${articles.description}</div>
                                                                            <div class="td text-sm font-semibold w-[8%]">${formatNumbersDigitLess(articles.article.pcs_per_packet)}</div>
                                                                            <div class="td text-sm font-semibold w-[12%]">${formatNumbersWithDigits(articles.article.sales_rate, 2, 2)}</div>
                                                                            <div class="td text-sm font-semibold w-[15%]">${formatNumbersWithDigits(parseInt(articles.article.sales_rate) * articles.invoice_quantity, 1, 1)}</div>
                                                                        </div>
                                                                    </div>
                                                                `;
                                                        }
                                                    }).join('')}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="w-full my-4 border-gray-600">
                                    <div class="flex flex-col space-y-2">
                                        <div id="invoice-total" class="tr grid grid-cols-2 w-full px-2 gap-2 text-sm">
                                            <div class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                                                <div class="text-nowrap">Total Quantity - Pcs</div>
                                                <div class="w-1/4 text-right grow">${formatNumbersDigitLess(totalQuantity)}</div>
                                            </div>
                                            <div class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                                                <div class="text-nowrap">Gross Amount</div>
                                                <div class="w-1/4 text-right grow">${formatNumbersWithDigits(totalAmount, 1, 1)}</div>
                                            </div>
                                            <div class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                                                <div class="text-nowrap">Discount - %</div>
                                                <div class="w-1/4 text-right grow">${formatNumbersDigitLess(discount)}</div>
                                            </div>
                                            <div
                                                class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                                                <div class="text-nowrap">Net Amount</div>
                                                <div class="w-1/4 text-right grow">${formatNumbersWithDigits(netAmount, 1, 1)}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="w-full my-4 border-gray-600">
                                    <div class="tfooter flex w-full text-sm px-4 justify-between mb-4">
                                        <P>${ companyData.name }</P>
                                        <p>&copy; Spark Pair 2025 | sparkpair.com</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Modal Action Slot -->
                        <x-slot name="actions">
                            <button type="button"
                                class="px-4 py-2 bg-[--secondary-bg-color] border border-gray-600 text-nowrap text-[--secondary-text] rounded-lg hover:bg-[--h-bg-color] transition-all 0.3s ease-in-out">
                                Print Order
                            </button>

                            <button onclick="closeModal()" type="button"
                                class="px-4 py-2 bg-[--secondary-bg-color] border border-gray-600 text-[--secondary-text] rounded-lg hover:bg-[--h-bg-color] transition-all 0.3s ease-in-out">
                                Cancel
                            </button>
                        </x-slot>
                    </x-modal>
                `;

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
    </script>
@endsection
