@extends('app')
@section('title', 'Show Articles | ' . app('company')->name)
@section('content')
    @php $authLayout = Auth::user()->layout; @endphp
    <!-- Modals -->
    {{-- article details modal --}}
    <div id="modal"
        class="mainModal hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-black bg-opacity-50 fade-in">
    </div>
    <!-- Main Content -->
    <h1 class="text-3xl font-bold mb-5 text-center text-[--primary-color] fade-in"> Show Articles </h1>

    <!-- Search Form -->
    {{-- <form id="search-form" method="GET" action="{{ route('article.index') }}" autocomplete="off"
        class="search-box w-[80%] text-sm mx-auto my-5 flex items-center gap-4">
        <!-- Search Input -->
        <div class="search-input relative flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Article Number"
                id="article_no_search"
                class="w-full px-4 py-2 rounded-lg bg-[--h-bg-color] text-[--text-color] placeholder-[--text-color] focus:outline-none focus:ring-2 focus:ring-[--primary-color] focus:ring-opacity-50">
        </div>

        <!-- Filters -->
        <div class="filter-box flex flex-1 items-center gap-4">
            <!-- Season Filter -->
            <div class="filter-select relative w-full">
                <select name="season" id="season"
                    class="w-full px-4 py-2 rounded-lg bg-[--h-bg-color] text-[--text-color] placeholder-[--text-color] appearance-none focus:outline-none focus:ring-2 focus:ring-[--primary-color] focus:ring-opacity-50">
                    <option value="all" {{ request('season') === 'all' ? 'selected' : '' }}>All Seasons</option>
                    @foreach ($seasons as $season)
                        <option value="{{ $season->id }}" {{ request('season') == $season->id ? 'selected' : '' }}>
                            {{ $season->title }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Size Filter -->
            <div class="filter-select relative w-full">
                <select name="size" id="size"
                    class="w-full px-4 py-2 rounded-lg bg-[--h-bg-color] text-[--text-color] placeholder-[--text-color] appearance-none focus:outline-none focus:ring-2 focus:ring-[--primary-color] focus:ring-opacity-50">
                    <option value="all">All Sizes</option>
                    @foreach ($sizes as $size)
                        <option value="{{ $size->id }}" {{ request('size') == $size->id ? 'selected' : '' }}>
                            {{ $size->title }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Category Filter -->
            <div class="filter-select relative w-full">
                <select name="category" id="category"
                    class="w-full px-4 py-2 rounded-lg bg-[--h-bg-color] text-[--text-color] placeholder-[--text-color] appearance-none focus:outline-none focus:ring-2 focus:ring-[--primary-color] focus:ring-opacity-50">
                    <option value="all" {{ request('category') === 'all' ? 'selected' : '' }}>All Categories
                    </option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->title }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </form> --}}

    <section class="text-center mx-auto ">
        <div
            class="show-box mx-auto w-[80%] h-[70vh] bg-[--secondary-bg-color] rounded-xl shadow overflow-y-auto @if ($authLayout == 'grid') pt-7 pr-2 @endif relative">
            @if ($authLayout == 'grid')
                <div
                    class="form-title text-center absolute top-0 left-0 w-full bg-[--primary-color] py-1 shadow-lg uppercase font-semibold text-sm">
                    <h4>Show Articles</h4>
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

            @if (count($orders) > 0)
                <div
                    class="add-new-article-btn absolute bottom-8 right-5 hover:scale-105 hover:bottom-9 transition-all group duration-300 ease-in-out">
                    <a href="{{ route('orders.create') }}"
                        class="bg-[--primary-color] text-[--text-color] px-3 py-2 rounded-full hover:bg-[--h-primary-color] transition-all duration-300 ease-in-out"><i
                            class="fas fa-plus"></i></a>
                    <span
                        class="absolute shadow-xl right-7 top-0 border border-gray-600 transform -translate-x-1/2 bg-[--secondary-bg-color] text-[--text-color] text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
                        Add
                    </span>
                </div>
            @endif

            @if (count($orders) > 0)
                <div class="details h-full">
                    <div class="container-parent h-full overflow-y-auto my-scroller">
                        @if ($authLayout == 'grid')
                            <div class="card_container p-5 pr-3 grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">
                                @foreach ($orders as $order)
                                    <div data-json='{{ $order }}'
                                        class="contextMenuToggle modalToggle card relative border border-gray-600 shadow rounded-xl min-w-[100px] h-[7rem] flex gap-4 py-4 px-5 cursor-pointer overflow-hidden fade-in">
                                        <x-card :data="[
                                            'name' => 'Order No: ' . $order->order_no,
                                            'details' => [
                                                'Customer' => $order->customer->customer_name,
                                                'Date' => $order->date,
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
                                @foreach ($orders as $article)
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
                            Order</button>
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

            let totalAmount = 0;
            let totalQuantity = 0;
            let discount = data.discount;
            let previousBalance = data.customer.previous_balance;
            let netAmount = data.netAmount;
            let currentBalance = data.customer.current_balance;

            modalDom.innerHTML = `
                <x-modal id="modalForm" classForBody="p-5 max-w-4xl h-[35rem] overflow-y-auto my-scroller-2 bg-white text-black" closeAction="closeModal" action="{{ route('update-user-status') }}">
                    <div id="preview-container" class="w-[210mm] h-[297mm] mx-auto overflow-hidden relative">
                        <div id="preview" class="preview flex flex-col h-full">
                            <div id="order" class="order flex flex-col h-full">
                                <div id="order-banner" class="order-banner w-full flex justify-between mt-8 px-5">
                                    <div class="left w-50">
                                        <div class="order-logo">
                                            <img src="{{ asset('images/${companyData.logo}') }}" alt="Track Point"
                                                class="w-[150px]" />
                                        </div>
                                    </div>
                                    <div class="right w-50 my-auto pr-3 text-sm text-gray-500">
                                        <div class="order-date">Date: ${data.date}</div>
                                        <div class="order-number">Order No.: ${data.order_no}</div>
                                        <div class="order-copy">Order Copy: Customer</div>
                                    </div>
                                </div>
                                <hr class="w-100 my-5 border-gray-600">
                                <div id="order-header" class="order-header w-full flex justify-between px-5">
                                    <div class="left w-50">
                                        <div class="order-to text-sm text-gray-500">Order to:</div>
                                        <div class="order-customer text-lg">${data.customer.customer_name}</div>
                                        <div class="order-person text-md">${data.customer.person_name}</div>
                                        <div class="order-address text-md">${data.customer.address}, ${data.customer.city}</div>
                                        <div class="order-phone text-md">${data.customer.phone_number}</div>
                                    </div>
                                    <div class="right w-50">
                                        <div class="order-from text-sm text-gray-500">Order from:</div>
                                        <div class="order-customer text-lg">${companyData.name}</div>
                                        <div class="order-person text-md">${companyData.owner_name}</div>
                                        <div class="order-address text-md">${companyData.city}, ${companyData.address}</div>
                                        <div class="order-phone text-md">${companyData.phone_number}</div>
                                    </div>
                                </div>
                                <hr class="w-100 mt-5 mb-5 border-gray-600">
                                <div id="order-body" class="order-body w-[95%] grow mx-auto">
                                    <div class="order-table w-full">
                                        <div class="table w-full border border-gray-600 rounded-lg pb-4 overflow-hidden">
                                            <div class="thead w-full">
                                                <div class="tr flex justify-between w-full px-4 py-2 bg-[--primary-color] text-white">
                                                    <div class="th text-sm font-medium w-[5%]">#</div>
                                                    <div class="th text-sm font-medium w-[10%]">Article</div>
                                                    <div class="th text-sm font-medium w-1/6">Qty/Pcs.</div>
                                                    <div class="th text-sm font-medium grow">Desc.</div>
                                                    <div class="th text-sm font-medium w-1/6">Rate</div>
                                                    <div class="th text-sm font-medium w-1/6">Amount</div>
                                                    <div class="th text-sm font-medium w-[12%]">Packed Qty.</div>
                                                </div>
                                            </div>
                                            <div id="tbody" class="tbody w-full">
                                                ${data.ordered_articles.map((orderedArticle, index) => {
                                                    const article = orderedArticle.article;
                                                    const salesRate = article.sales_rate;
                                                    const orderedQuantity = orderedArticle.ordered_quantity;
                                                    const total = parseInt(salesRate) * orderedQuantity;
                                                    const hrClass = index === 0 ? "mb-3" : "my-3";

                                                    totalAmount += total;
                                                    totalQuantity += orderedQuantity;

                                                    return `
                                                        <div>
                                                            <hr class="w-full ${hrClass} border-gray-600">
                                                            <div class="tr flex justify-between w-full px-4">
                                                                <div class="td text-sm font-semibold w-[5%]">${index + 1}.</div>
                                                                <div class="td text-sm font-semibold w-[10%]">#${article.article_no}</div>
                                                                <div class="td text-sm font-semibold w-[10%]">${orderedQuantity}</div>
                                                                <div class="td text-sm font-semibold grow">${orderedArticle.description}</div>
                                                                <div class="td text-sm font-semibold w-1/6">
                                                                    ${new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(salesRate)}
                                                                </div>
                                                                <div class="td text-sm font-semibold w-1/6">
                                                                    ${new Intl.NumberFormat('en-US', { minimumFractionDigits: 1, maximumFractionDigits: 1 }).format(total)}
                                                                </div>
                                                                <div class="td text-sm font-semibold w-[12%]">____________</div>
                                                            </div>
                                                        </div>
                                                    `;
                                                }).join('')}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="w-full my-4 border-gray-600">
                                <div class="flex flex-col space-y-2">
                                    <div id="order-total" class="tr flex justify-between w-full px-2 gap-2 text-sm">
                                        <div class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                                            <div class="text-nowrap">Total Quantity - Pcs</div>
                                            <div class="w-1/4 text-right grow">${new Intl.NumberFormat('en-US').format(totalQuantity)}</div>
                                        </div>
                                        <div class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                                            <div class="text-nowrap">Total Amount</div>
                                            <div class="w-1/4 text-right grow">${new Intl.NumberFormat('en-US', { minimumFractionDigits: 1, maximumFractionDigits: 1 }).format(totalAmount)}</div>
                                        </div>
                                        <div class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                                            <div class="text-nowrap">Discount - %</div>
                                            <div class="w-1/4 text-right grow">${discount}</div>
                                        </div>
                                    </div>
                                    <div id="order-total" class="tr flex justify-between w-full px-2 gap-2 text-sm">
                                        <div class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                                            <div class="text-nowrap">Previous Balance</div>
                                            <div class="w-1/4 text-right grow">${new Intl.NumberFormat('en-US', { minimumFractionDigits: 1, maximumFractionDigits: 1 }).format(previousBalance)}</div>
                                        </div>
                                        <div
                                            class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                                            <div class="text-nowrap">Net Amount</div>
                                            <div class="w-1/4 text-right grow">${new Intl.NumberFormat('en-US', { minimumFractionDigits: 1, maximumFractionDigits: 1 }).format(netAmount)}</div>
                                        </div>
                                        <div
                                            class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                                            <div class="text-nowrap">Current Balance</div>
                                            <div class="w-1/4 text-right grow">${new Intl.NumberFormat('en-US', { minimumFractionDigits: 1, maximumFractionDigits: 1 }).format(currentBalance)}</div>
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
    </script>
@endsection
