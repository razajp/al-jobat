@extends('app')
@section('title', 'Add Bilty | ' . app('company')->name)
@section('content')
    <!-- Modal -->
    <div id="modal"
        class="hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-[var(--overlay-color))] fade-in">
        <x-modal id="ModalForm" classForBody="p-5 pt-4 max-w-6xl h-[45rem]" closeAction="closeModal">
            <!-- Modal Content Slot -->
            <div class="flex items-start relative h-full">
                <div class="flex-1 h-full overflow-y-auto my-scrollbar-2 flex flex-col pt-2 pr-1">
                    <x-search-header heading="Invoices" toFrom_label="Invoice No:" toFrom toFrom_type="text" :filter_items="[
                        'all' => 'Invoice No.',
                    ]"/>

                    @if (count($invoices) > 0)
                        <div class='overflow-y-auto my-scrollbar-2 pt-2 grow'>
                            <div class="search_container grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">
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

                <div id="select-all-checkbox-parent" class="absolute z-[999] bottom-1.5 right-0 hover:scale-105 transition-all duration-300 ease-in-out cursor-pointer">
                    <div class="bg-[var(--secondary-bg-color)] border border-gray-600 text-[var(--text-color)] px-4 py-2 rounded-xl flex gap-3 items-center justify-between">
                        <span>Select All</span>
                        <input type="checkbox" id="select-all-checkbox" class="row-checkbox shrink-0 w-3.5 h-3.5 appearance-none border border-gray-400 rounded-sm checked:bg-[var(--primary-color)] checked:border-transparent focus:outline-none transition duration-150 cursor-pointer pointer-events-none">
                    </div>
                </div>
            </div>
            <!-- Modal Action Slot -->
            <x-slot name="actions">
                <button onclick="closeModal()" type="button"
                    class="px-4 py-2 bg-[var(--secondary-bg-color)] border border-gray-600 text-[var(--secondary-text)] rounded-lg hover:bg-[var(--h-bg-color)] transition-all duration-300 ease-in-out cursor-pointer">
                    Close
                </button>
            </x-slot>
        </x-modal>
    </div>

    <!-- Main Content -->
    <!-- Progress Bar -->
    <div class="mb-5 max-w-4xl mx-auto">
        <x-search-header heading="Add Bilty" link linkText="Show Bilties" linkHref="{{ route('cargos.index') }}"/>
    </div>

    <!-- Form -->
    <form id="form" action="{{ route('bilties.store') }}" method="post" enctype="multipart/form-data"
        class="bg-[var(--secondary-bg-color)] text-sm rounded-xl shadow-lg p-8 border border-[var(--h-bg-color)] pt-12 max-w-4xl mx-auto  relative overflow-hidden">
        @csrf
        <div
            class="form-title text-center absolute top-0 left-0 w-full bg-[var(--primary-color)] py-1 capitalize tracking-wide font-medium text-sm">
            <h4>Add Bilty</h4>
        </div>

        <div class="space-y-4 ">
            <div class="flex items-end gap-4">
                {{-- cargo date --}}
                <div class="grow">
                    <x-input label="Date" name="date" id="date" type="date" onchange="trackStateOfgenerateBtn(this)"
                        validateMax max='{{ now()->toDateString() }}' validateMin
                        min="{{ now()->subDays(4)->toDateString() }}" required />
                </div>

                <button id="generateListBtn" type="button"
                    class="bg-[var(--primary-color)] px-4 py-2 rounded-lg hover:bg-[var(--h-primary-color)] transition-all duration-300 ease-in-out text-nowrap cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">Select Invoices</button>
            </div>
            {{-- cargo-list-table --}}
            <div id="cargo-list-table" class="w-full text-left text-sm">
                <div class="flex justify-between items-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4 mb-4">
                    <div class="w-[7%]">S.No.</div>
                    <div class="w-1/6">Date</div>
                    <div class="w-1/6">Bill No.</div>
                    <div class="w-[10%]">Cottons</div>
                    <div class="grow">Customer</div>
                    <div class="w-[10%]">City</div>
                    <div class="w-1/6">Bilty No.</div>
                    <div class="w-[10%] text-center">Action</div>
                </div>
                <div id="cargo-list" class="h-[20rem] overflow-y-auto my-scrollbar-2">
                    <div class="text-center bg-[var(--h-bg-color)] rounded-lg py-3 px-4">No Rates Added</div>
                </div>
            </div>

            <input type="hidden" name="invoices_array" id="invoices" value="">
            <div class="w-full grid grid-cols-1 text-sm mt-5 text-nowrap">
                <div class="total-qty flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                    <div class="grow">Total Cottons</div>
                    <div id="finalTotalCottons">0</div>
                </div>
            </div>
        </div>

        <div class="w-full flex justify-end mt-4">
            <button type="submit"
                class="px-6 py-1 bg-[var(--bg-success)] border border-[var(--bg-success)] text-[var(--text-success)] font-medium text-nowrap rounded-lg hover:bg-[var(--h-bg-success)] transition-all 0.3s ease-in-out cursor-pointer">
                <i class='fas fa-save mr-1'></i> Save
            </button>
        </div>
    </form>

    <script>
        let selectedInvoicesArray = [];

        const modalDom = document.getElementById("modal");
        const selectAllCheckbox = document.getElementById("select-all-checkbox");
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
                            <div class="w-[7%]">${index+1}</div>
                            <div class="w-1/6">${selectedInvoice.date}</div>
                            <div class="w-1/6">${selectedInvoice.invoice_no}</div>
                            <div class="w-[10%]">${selectedInvoice.cotton_count ?? '-'}</div>
                            <div class="grow">${selectedInvoice.customer.customer_name}</div>
                            <div class="w-[10%]">${selectedInvoice.customer.city}</div>
                            <div class="w-1/6">
                                <input oninput="setBiltyNo(${selectedInvoice.id}, this.value)" class="bilty_no w-[80%] border border-gray-600 bg-[var(--h-bg-color)] py-0.5 px-2 rounded-md text-xs focus:outline-none" type="number"/>
                            </div>
                            <div class="w-[10%] text-center">
                                <button onclick="deselectThisInvoice(${index})" type="button" class="text-[var(--danger-color)] cursor-pointer text-xs px-2 py-1 rounded-lg hover:text-[var(--h-danger-color)] transition-all duration-300 ease-in-out">
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
            updateInputInvoicesArray();
        }
        renderList();

        function updateInputInvoicesArray() {
            let inputinvoices = document.getElementById('invoices');
            let finalInovicesArray = selectedInvoicesArray.map(invoice => {
                return {
                    id: invoice.id,
                    biltyNo: invoice.biltyNo
                }
            });
            inputinvoices.value = JSON.stringify(finalInovicesArray);

            console.log(inputinvoices);
        }

        let companyData = @json(app('company'));
        const previewDom = document.getElementById('preview');

        function generateCargoListPreview() {
            const cargoNameInpDom = document.getElementById("cargo_name");
            const dateInpDom = document.getElementById("date");

            if (selectedInvoicesArray.length > 0) {
                previewDom.innerHTML = `
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
                                    <h1 class="text-2xl font-medium text-[var(--primary-color)] pr-2">Cargo List</h1>
                                    <div class='mt-1'>${ companyData.phone_number }</div>
                                </div>
                            </div>
                        </div>
                        <hr class="w-full my-3 border-gray-600">
                        <div id="preview-header" class="preview-header w-full flex justify-between px-5">
                            <div class="left my-auto pr-3 text-sm text-gray-600 space-y-1.5">
                                <div class="cargo-date leading-none">Date: ${dateInpDom.value}</div>
                                <div class="cargo-number leading-none">Cargo No.: ${cargoNo}</div>
                                <input type="hidden" name="cargo_no" value="${cargoNo}" />
                            </div>
                            <div class="center my-auto">
                                <div class="cargo-name capitalize font-semibold text-md">Cargo Name: ${cargoNameInpDom.value}</div>
                            </div>
                            <div class="right my-auto pr-3 text-sm text-gray-600 space-y-1.5">
                                <div class="preview-copy leading-none">Cargo List Copy: Cargo</div>
                                <div class="preview-doc leading-none">Document: Cargo List</div>
                            </div>
                        </div>
                        <hr class="w-full my-3 border-gray-600">
                        <div id="preview-body" class="preview-body w-[95%] grow mx-auto">
                            <div class="preview-table w-full">
                                <div class="table w-full border border-gray-600 rounded-lg pb-2.5 overflow-hidden">
                                    <div class="thead w-full">
                                        <div class="tr flex justify-between w-full px-4 py-1.5 bg-[var(--primary-color)] text-white">
                                            <div class="th text-sm font-medium w-[7%]">S.No</div>
                                            <div class="th text-sm font-medium w-1/6">Date</div>
                                            <div class="th text-sm font-medium w-1/6">Invoice No.</div>
                                            <div class="th text-sm font-medium w-1/6">Cotton</div>
                                            <div class="th text-sm font-medium grow">Customer</div>
                                            <div class="th text-sm font-medium w-1/6">City</div>
                                        </div>
                                    </div>
                                    <div id="tbody" class="tbody w-full">
                                        ${selectedInvoicesArray.map((invoice, index) => {
                                            const hrClass = index === 0 ? "mb-2.5" : "my-2.5";
                                            return `
                                                <div>
                                                    <hr class="w-full ${hrClass} border-gray-600">
                                                    <div class="tr flex justify-between w-full px-4">
                                                        <div class="td text-sm font-semibold w-[7%]">${index + 1}.</div>
                                                        <div class="td text-sm font-semibold w-1/6">${invoice.date}</div>
                                                        <div class="td text-sm font-semibold w-1/6">${invoice.invoice_no}</div>
                                                        <div class="td text-sm font-semibold w-1/6">${invoice.cotton_count}</div>
                                                        <div class="td text-sm font-semibold grow">${invoice.customer.customer_name}</div>
                                                        <div class="td text-sm font-semibold w-1/6">${invoice.customer.city}</div>
                                                    </div>
                                                </div>
                                            `;
                                        }).join('')}
                                    </div>
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
                onClickInvoice(card);
            });
        });

        function onClickInvoice(invoiceElem) {
            let checkbox = invoiceElem.querySelector("input[type='checkbox']")
            checkbox.checked = !checkbox.checked;

            toggleInvoice(invoiceElem, checkbox);
        }

        function toggleInvoice(invoiceElem, checkbox) {
            if (checkbox.checked) {
                selectInvoice(invoiceElem);
            } else {
                deselectInvoice(invoiceElem);
            }
        }

        function selectInvoice(invoiceElem) {
            const invoiceData = JSON.parse(invoiceElem.dataset.json);

            const index = selectedInvoicesArray.findIndex(invoice => invoice.id === invoiceData.id);
            if (index == -1) {
                selectedInvoicesArray.push(invoiceData);
                totalCottonCount += invoiceData.cotton_count;
            }
        }

        function deselectInvoice(invoiceElem) {
            const invoiceData = JSON.parse(invoiceElem.dataset.json);

            const index = selectedInvoicesArray.findIndex(invoice => invoice.id === invoiceData.id);
            if (index > -1) {
                selectedInvoicesArray.splice(index, 1);
                totalCottonCount -= invoiceData.cotton_count;

                selectAllCheckbox.checked = false;
            }
        }

        function deselectAllInvoices() {
            document.querySelectorAll(".invoice-card input[type='checkbox']").forEach(checkbox => {
                checkbox.checked = false;
            });
            
            selectedInvoicesArray = [];
            totalCottonCount = 0;
            selectAllCheckbox.checked = false;
        }

        function validateForNextStep() {
            generateCargoListPreview()
            return true;
        }

        const searchInput = document.getElementById("search_box");
        const fromInput = document.getElementById("from");
        const toInput = document.getElementById("to");
        const cards = document.querySelectorAll(".invoice-card");
        const cardsContainer = document.querySelector(".search_container");

        function getInvoiceNumber(str) {
            // Converts '2025-0001' => 20250001 (as number)
            return parseInt(str.replace("-", ""));
        }

        function filterCards() {
            clearSerialOrSearch("search");
            
            const fromVal = getInvoiceNumber(fromInput.value);
            const toVal = getInvoiceNumber(toInput.value);

            cards.forEach(card => {
                const data = JSON.parse(card.getAttribute("data-json"));
                const invoiceNum = getInvoiceNumber(data.invoice_no);

                // Determine if the card should be shown
                const show = (
                    (!fromVal || invoiceNum >= fromVal) &&
                    (!toVal || invoiceNum <= toVal)
                );

                card.style.display = show ? "flex" : "none";
            });
        }

        fromInput.addEventListener("input", filterCards);
        toInput.addEventListener("input", filterCards);

        function filterData(search) {
            clearSerialOrSearch("serial");

            const filteredData = cardsDataArray.filter(item => {
                switch (filterType) {
                    case 'all':
                        return (
                            item.invoice_no.toString().includes(search)
                        );
                        break;
                
                    default:
                        return (
                            item.invoice_no.toString().includes(search)
                        );
                        break;
                }
            });

            return filteredData;
        }

        function clearSerialOrSearch(serialOrSearch) {
            if (serialOrSearch == "serial") {
                toInput.value = "";
                fromInput.value = "";

                cards.forEach(card=>{
                    card.style.display = "flex";
                });
            } else {
                searchInput.value = "";
                cardsContainer.innerHTML = "";
                
                cards.forEach(card=>{
                    cardsContainer.appendChild(card);
                });
            }
        }

        const selectAllCheckboxParent = document.getElementById('select-all-checkbox-parent');
        selectAllCheckboxParent.addEventListener('click', ()=>{
            selectAllCheckbox.checked = !selectAllCheckbox.checked;

            selectAllScript();
        });
        
        function selectAllScript() {
            let invoiceCards = document.querySelectorAll(".invoice-card");
            invoiceCards.forEach(card => {
                if (card.style.display != "none") {
                    const checkbox = card.querySelector("input[type='checkbox']");
                    checkbox.checked = selectAllCheckbox.checked;
                    
                    toggleInvoice(card, checkbox);
                }
            });
        }

        function setBiltyNo(invoiceId, biltyNo) {
            const invoice = selectedInvoicesArray.find(invoice => invoice.id === invoiceId);
            if (invoice) {
                invoice.biltyNo = biltyNo;
            }

            updateInputInvoicesArray();
        }
    </script>
@endsection
