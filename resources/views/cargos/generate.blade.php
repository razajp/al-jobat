@extends('app')
@section('title', 'Generate Cargo List | ' . app('company')->name)
@section('content')
    <!-- Modal -->
    <div id="modal"
        class="hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-[var(--overlay-color))] fade-in">
        <x-modal id="ModalForm" classForBody="p-5 pt-4 max-w-6xl h-[45rem]" closeAction="closeModal">
            <!-- Modal Content Slot -->
            <div class="flex items-start relative h-full">
                <div class="flex-1 h-full overflow-y-auto my-scrollbar-2 flex flex-col pt-2">
                    <x-search-header heading="Invoices" toFrom_label="Invoice No:" toFrom toFrom_type="number"/>

                    @if (count($invoices) > 0)
                        <div class='overflow-y-auto my-scrollbar-2 pt-2 grow'>
                            <div class="card_container grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">
                                @foreach ($invoices as $invoice)
                                    <div id="{{ $invoice->id }}" data-json='{{ $invoice }}'
                                        class="invoice-card card relative border flex items-center justify-between border-gray-600 shadow rounded-xl min-w-[100px] py-3 px-4 cursor-pointer overflow-hidden fade-in">
                                        <div class="text-start {{ isset($data['image']) ? "pt-1" : "" }}">
                                            <h5 class="text-lg text-[var(--text-color)] capitalize font-semibold leading-none">
                                                Invoice No: {{ $invoice->invoice_no }}
                                            </h5>
                                        </div>
                                        <input type="checkbox" name="selected_customers[]"
                                            class="row-checkbox shrink-0 w-3.5 h-3.5 appearance-none border border-gray-400 rounded-sm checked:bg-[var(--primary-color)] checked:border-transparent focus:outline-none transition duration-150 pointer-events-none cursor-pointer"/>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="text-[var(--border-error)] text-center h-full">Not Found</div>
                    @endif
                </div>
            </div>
            <!-- Modal Action Slot -->
            <x-slot name="actions">
                <button onclick="closeModal()" type="button"
                    class="px-4 py-2 bg-[var(--secondary-bg-color)] border border-gray-600 text-[var(--secondary-text)] rounded-lg hover:bg-[var(--h-bg-color)] transition-all 0.3s ease-in-out">
                    Close
                </button>
            </x-slot>
        </x-modal>
    </div>

    <!-- Main Content -->
    <h1 class="text-3xl font-bold mb-6 text-center text-[var(--primary-color)] fade-in"> Generate Cargo List </h1>

    <!-- Progress Bar -->
    <div class="mb-5 max-w-4xl mx-auto">
        <x-progress-bar :steps="['Generate Cargo List', 'Preview']" :currentStep="1" />
    </div>

    <!-- Form -->
    <form id="form" action="{{ route('cargos.store') }}" method="post" enctype="multipart/form-data"
        class="bg-[var(--secondary-bg-color)] text-sm rounded-xl shadow-lg p-8 border border-[var(--h-bg-color)] pt-12 max-w-4xl mx-auto  relative overflow-hidden">
        @csrf
        <div
            class="form-title text-center absolute top-0 left-0 w-full bg-[var(--primary-color)] py-1 capitalize tracking-wide font-medium text-sm">
            <h4>Generate Cargo List</h4>
        </div>

        <!-- Step 1: Generate shipment -->
        <div class="step1 space-y-4 ">
            <div class="flex items-end gap-4">
                {{-- shipment date --}}
                <div class="grow">
                    <x-input label="Date" name="date" id="date" type="date" onchange="trackStateOfgenerateBtn(this)"
                        validateMax max='{{ now()->toDateString() }}' validateMin
                        min="{{ now()->subDays(4)->toDateString() }}" required />
                </div>
                    
                <div class="grow">
                    <!-- customer_name -->
                    <x-input 
                        label="Cargo Name"
                        name="cargo_name" 
                        id="cargo_name" 
                        placeholder="Enter cargo name" 
                        required 
                    />
                </div>

                <button id="generateListBtn" type="button"
                    class="bg-[var(--primary-color)] px-4 py-2 rounded-lg hover:bg-[var(--h-primary-color)] transition-all 0.3s ease-in-out text-nowrap disabled:opacity-50 disabled:cursor-not-allowed">Select Invoices</button>
            </div>
            {{-- rate showing --}}
            <div id="shipment-table" class="w-full text-left text-sm">
                <div class="flex justify-between items-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4 mb-4">
                    <div class="w-[10%]">S.No.</div>
                    <div class="w-1/6">Date</div>
                    <div class="w-1/6">Bill No.</div>
                    <div class="w-1/5">Cottons</div>
                    <div class="grow">Customer</div>
                    <div class="w-[10%] text-center">Action</div>
                </div>
                <div id="cargo-list" class="h-[20rem] overflow-y-auto my-scrollbar-2">
                    <div class="text-center bg-[var(--h-bg-color)] rounded-lg py-3 px-4">No Rates Added</div>
                </div>
            </div>

            <input type="hidden" name="invoices" id="invoices" value="">
            <div class="w-full grid grid-cols-1 text-sm mt-5 text-nowrap">
                <div class="total-qty flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                    <div class="grow">Total Cottons</div>
                    <div id="finalTotalCottons">0</div>
                </div>
            </div>
        </div>

        <!-- Step 2: view shipment -->
        <div class="step2 hidden space-y-4 text-black h-[35rem] overflow-y-auto my-scrollbar-2 bg-white rounded-md">
            <div id="preview-container" class="w-[210mm] h-[297mm] mx-auto overflow-hidden relative">
                <div id="preview" class="preview flex flex-col h-full">
                    <h1 class="text-[var(--border-error)] font-medium text-center mt-5">No Preview avalaible.</h1>
                </div>
            </div>
        </div>
    </form>

    <script>
        let selectedInvoicesArray = [];

        const lastShipment = @json($last_cargo);
        const modalDom = document.getElementById("modal");
        const generateListBtn = document.getElementById("generateListBtn");
        const cargoListDOM = document.getElementById('cargo-list');
        const finalTotalCottonsDOM = document.getElementById('finalTotalCottons');
        generateListBtn.disabled = true;
        let totalCottonCount = 0;

        function trackStateOfgenerateBtn(elem) {
            if (elem.value != "") {
                generateListBtn.disabled = false;
            } else {
                generateListBtn.disabled = true;
            }
        }

        let isModalOpened = false;

        generateListBtn.addEventListener('click', () => {
            generateModal();
        })

        function generateModal() {
            openModal();
        }

        function openModal() {
            isModalOpened = true;
            closeAllDropdowns();
            document.getElementById('modal').classList.remove('hidden');
        }

        function closeModal() {
            renderList();

            isModalOpened = false;
            let modal = document.getElementById('modal');
            modal.classList.add('fade-out');

            modal.addEventListener('animationend', () => {
                modal.classList.add('hidden');
                modal.classList.remove('fade-out');
            }, {
                once: true
            });

            finalTotalCottonsDOM.textContent = totalCottonCount;
        }

        document.addEventListener('mousedown', (e) => {
            const {
                id
            } = e.target;
            if (id === 'ModalForm') {
                closeModal();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && isModalOpened) {
                closeModal();
            }
        })

        function deselectInvoiceAtIndex(index) {
            if (index !== -1) {
                selectedInvoicesArray.splice(index, 1);
            }
        }

        function deselectThisInvoice(index) {
            document.getElementById(selectedInvoicesArray[index].id).querySelector("input[type=checkbox]").checked = false;
            
            totalCottonCount -= selectedInvoicesArray[index].cotton_count;
            
            deselectInvoiceAtIndex(index);

            renderList();
            
            finalTotalCottonsDOM.textContent = totalCottonCount;
        }

        function renderList() {
            if (selectedInvoicesArray.length > 0) {
                let clutter = "";
                selectedInvoicesArray.forEach((selectedInvoice, index) => {
                    clutter += `
                        <div class="flex justify-between items-center border-t border-gray-600 py-3 px-4">
                            <div class="w-[10%]">${index+1}</div>
                            <div class="w-1/6">${selectedInvoice.date}</div>
                            <div class="w-1/6">${selectedInvoice.invoice_no}</div>
                            <div class="w-1/6">${selectedInvoice.cotton_count ?? '-'}</div>
                            <div class="grow">${selectedInvoice.customer.customer_name}</div>
                            <div class="w-[10%] text-center">
                                <button onclick="deselectThisInvoice(${index})" type="button" class="text-[var(--danger-color)] text-xs px-2 py-1 rounded-lg hover:text-[var(--h-danger-color)] transition-all duration-300 ease-in-out">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    `;
                });

                cargoListDOM.innerHTML = clutter;
            } else {
                cargoListDOM.innerHTML =
                    `<div class="text-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4">No Invoices Yet</div>`;
            }
            updateInputinvoicesArray();
        }
        renderList();

        function updateInputinvoicesArray() {
            let inputinvoices = document.getElementById('invoices');
            let finalArticlesArray = selectedInvoicesArray.map(invoice => {
                return {
                    id: invoice.id,
                    description: invoice.description,
                    shipment_quantity: invoice.shipmentQuantity
                }
            });
            inputinvoices.value = JSON.stringify(finalArticlesArray);
        }

        let companyData = @json(app('company'));
        const previewDom = document.getElementById('preview');

        function generateShipmentNo() {
            let lastShipmentNo = lastShipment.shipment_no
            const nextShipmentNo = String(parseInt(lastShipmentNo) + 1).padStart(4, '0');
            return nextShipmentNo;
        }

        function getShipmentDate() {
            const dateDom = document.getElementById('date').value;
            const date = new Date(dateDom);

            // Extract day, month, and year
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are 0-based
            const year = date.getFullYear();
            const dayOfWeek = date.getDay(); // 0 = Sunday, 1 = Monday, ..., 6 = Saturday

            // Array of weekday names
            const weekDays = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];

            // Return the formatted date
            return `${day}-${month}-${year}, ${weekDays[dayOfWeek]}`;
        }

        function generateShipment() {
            shipmentNo = generateShipmentNo();  
            shipmentDate = getShipmentDate();

            if (selectedInvoicesArray.length > 0) {
                previewDom.innerHTML = `
                    <div id="shipment" class="shipment flex flex-col h-full">
                        <div id="shipment-banner" class="shipment-banner w-full flex justify-between items-center mt-8 pl-5 pr-8">
                            <div class="left">
                                <div class="shipment-logo">
                                    <img src="{{ asset('images/${companyData.logo}') }}" alt="Track Point"
                                        class="w-[12rem]" />
                                </div>
                            </div>
                            <div class="right">
                                <div>
                                    <h1 class="text-2xl font-medium text-[var(--primary-color)] pr-2">Shipment</h1>
                                    <div class='mt-1'>${ companyData.phone_number }</div>
                                </div>
                            </div>
                        </div>
                        <hr class="w-full my-3 border-gray-600">
                        <div id="shipment-header" class="shipment-header w-full flex justify-between px-5">
                            <div class="left w-50 my-auto pr-3 text-sm text-gray-600 space-y-1.5">
                                <div class="shipment-date leading-none">Date: ${shipmentDate}</div>
                                <div class="shipment-number leading-none">Shipment No.: ${shipmentNo}</div>
                                <input type="hidden" name="shipment_no" value="${shipmentNo}" />
                            </div>
                            <div class="right w-50 my-auto pr-3 text-sm text-gray-600 space-y-1.5">
                                <div class="shipment-copy leading-none">Shipment Copy: Office</div>
                                <div class="shipment-copy leading-none">Document: Shipment</div>
                            </div>
                        </div>
                        <hr class="w-full my-3 border-gray-600">
                        <div id="shipment-body" class="shipment-body w-[95%] grow mx-auto">
                            <div class="shipment-table w-full">
                                <div class="table w-full border border-gray-600 rounded-lg pb-2.5 overflow-hidden">
                                    <div class="thead w-full">
                                        <div class="tr flex justify-between w-full px-4 py-1.5 bg-[var(--primary-color)] text-white">
                                            <div class="th text-sm font-medium w-[7%]">S.No</div>
                                            <div class="th text-sm font-medium w-[10%]">Article</div>
                                            <div class="th text-sm font-medium grow">Description</div>
                                            <div class="th text-sm font-medium w-[10%]">Pcs.</div>
                                            <div class="th text-sm font-medium w-[10%]">Packets</div>
                                            <div class="th text-sm font-medium w-[10%]">Rate</div>
                                            <div class="th text-sm font-medium w-[10%]">Amount</div>
                                        </div>
                                    </div>
                                    <div id="tbody" class="tbody w-full">
                                        ${selectedInvoicesArray.map((article, index) => {
                                            const hrClass = index === 0 ? "mb-2.5" : "my-2.5";
                                            return `
                                                <div>
                                                    <hr class="w-full ${hrClass} border-gray-600">
                                                    <div class="tr flex justify-between w-full px-4">
                                                        <div class="td text-sm font-semibold w-[7%]">${index + 1}.</div>
                                                        <div class="td text-sm font-semibold w-[10%]">#${article.article_no}</div>
                                                        <div class="td text-sm font-semibold grow">${article.description}</div>
                                                        <div class="td text-sm font-semibold w-[10%]">${article.shipmentQuantity}</div>
                                                        <div class="td text-sm font-semibold w-[10%]">${article.pcs_per_packet ? Math.floor(article.shipmentQuantity / article.pcs_per_packet) : 0}</div>
                                                        <div class="td text-sm font-semibold w-[10%]">
                                                            ${new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(article.sales_rate)}
                                                        </div>
                                                        <div class="td text-sm font-semibold w-[10%]">
                                                            ${new Intl.NumberFormat('en-US', { minimumFractionDigits: 1, maximumFractionDigits: 1 }).format(parseInt(article.sales_rate) * article.shipmentQuantity)}
                                                        </div>
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
                            <div id="shipment-total" class="tr flex justify-between w-full px-2 gap-2 text-sm">
                                <div class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                                    <div class="text-nowrap">Total Quantity - Pcs</div>
                                    <div class="w-1/4 text-right grow">${totalQuantityDOM.textContent}</div>
                                </div>
                                <div class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                                    <div class="text-nowrap">Total Amount</div>
                                    <div class="w-1/4 text-right grow">${totalAmountDOM.textContent}</div>
                                </div>
                            </div>
                            <div id="shipment-total" class="tr flex justify-between w-full px-2 gap-2 text-sm">
                                <div class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                                    <div class="text-nowrap">Discount - %</div>
                                    <div class="w-1/4 text-right grow">${discountDOM.value}</div>
                                </div>
                                <div
                                    class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                                    <div class="text-nowrap">Net Amount</div>
                                    <div class="w-1/4 text-right grow">${finalNetAmount.value}</div>
                                </div>
                            </div>
                        </div>
                        <hr class="w-full my-3 border-gray-600">
                        <div class="tfooter flex w-full text-sm px-4 justify-between mb-4 text-gray-600">
                            <P class="leading-none">${ companyData.name } | ${ companyData.address }</P>
                            <p class="leading-none text-sm">&copy; 2025 Spark Pair | +92 316 5825495</p>
                        </div>
                    </div>
                `;
            } else {
                previewDom.innerHTML = `
                    <h1 class="text-[var(--border-error)] font-medium text-center mt-5">No Preview avalaible.</h1>
                `;
            }
        }

        document.querySelectorAll(".invoice-card").forEach((card)=>{
            card.addEventListener("click", ()=>{
                selectInvoice(card);
            });
        });

        function selectInvoice(invoiceElem) {
            let checkbox = invoiceElem.querySelector("input[type='checkbox']")
            checkbox.checked = !checkbox.checked;

            let invoiceData = JSON.parse(invoiceElem.dataset.json);

            if (checkbox.checked) {
                selectedInvoicesArray.push(invoiceData);
                totalCottonCount += invoiceData.cotton_count;
            } else {
                const index = selectedInvoicesArray.findIndex(invoice => invoice.id === invoiceData.id);
                if (index > -1) {
                    selectedInvoicesArray.splice(index, 1);
                    totalCottonCount -= invoiceData.cotton_count;
                }
            }
        }

        function validateForNextStep() {
            generateShipment()
            return true;
        }
    </script>
@endsection
