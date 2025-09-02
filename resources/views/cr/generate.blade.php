@extends('app')
@section('title', 'Generate CR | ' . app('company')->name)
@section('content')
    <!-- Main Content -->
    <!-- Progress Bar -->
    <div class="mb-5 max-w-4xl mx-auto">
        <x-search-header heading="Generate CR" link linkText="Show CR" linkHref="{{ route('cr.index') }}"/>
        <x-progress-bar :steps="['Generate CR', 'Preview']" :currentStep="1" />
    </div>

    <!-- Form -->
    <form id="form" action="{{ route('cr.store') }}" method="post" enctype="multipart/form-data"
        class="bg-[var(--secondary-bg-color)] text-sm rounded-xl shadow-lg p-8 border border-[var(--glass-border-color)]/20 pt-14 max-w-4xl mx-auto  relative overflow-hidden">
        @csrf
        <x-form-title-bar title="Generate CR" />

        <!-- Step 1: Generate cargo list -->
        <div class="step1 space-y-4 ">
            <div class="grid grid-cols-3 gap-4">
                <!-- voucher_no -->
                <x-input
                    label="Voucher No."
                    name="voucher_no"
                    id="voucher_no"
                    placeholder="Enter Voucher No."
                    required
                    onkeydown="trackVoucherState(event)"
                />

                {{-- cargo date --}}
                <x-input label="Date" name="date" id="date" type="date" required disabled/>

                <!-- supplier_name -->
                <x-input
                    label="Supplier Name"
                    id="supplier_name"
                    disabled
                    placeholder="Supplier Name"
                />
            </div>
            {{-- cargo-list-table --}}
            <div id="cargo-list-table" class="w-full text-left text-sm">
                <div class="flex justify-between items-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4 mb-4">
                    <div class="w-[10%]">S.No.</div>
                    <div class="w-1/6">Date</div>
                    <div class="w-1/6">Bill No.</div>
                    <div class="w-1/6">Cottons</div>
                    <div class="grow">Customer</div>
                    <div class="w-[10%]">City</div>
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
        let voucher = {};
        let selectedInvoicesArray = [];
        const dateDom = document.getElementById('date');
        const supplierNameDom = document.getElementById('supplier_name');

        function trackVoucherState(e) {
            if (e.key == 'Enter') {
                $.ajax({
                    url: '/get-voucher-details',
                    type: 'POST',
                    data: {
                        voucher_no: e.target.value,
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        voucher = response.data;
                        if (voucher) {
                            console.log(voucher);
                            date.disabled = false;
                            date.min = voucher.date;
                            supplierNameDom.value = voucher.supplier_name;

                            selectedInvoicesArray = [];
                        } else {
                            dateDom.value = '';
                            dateDom.disabled = true;
                            selectedInvoicesArray = [];
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Failed to update last activity", error);
                    }
                });
            }
        }
        // const modalDom = document.getElementById("modal");
        // // const selectAllCheckbox = document.getElementById("select-all-checkbox");
        // const generateListBtn = document.getElementById("generateListBtn");
        // const cargoListDOM = document.getElementById('cargo-list');
        // const finalTotalCottonsDOM = document.getElementById('finalTotalCottons');
        // generateListBtn.disabled = true;
        // let totalCottonCount = 0;

        // function trackStateOfgenerateBtn(elem) {
        //     if (elem.value != "") {
        //         generateListBtn.disabled = false;
        //     } else {
        //         generateListBtn.disabled = true;
        //     }
        // }

        // let isModalOpened = false;

        // generateListBtn.addEventListener('click', () => {
        //     generateModal();
        // })

        // function generateModal() {
        //     let cardData = [];

        //     if (data.length > 0) {
        //         cardData.push(...data.map(item => {
        //             return {
        //                 id: item.id,
        //                 name: item.invoice_no,
        //                 data: item,
        //                 checkbox: true,
        //                 checked: selectedInvoicesArray.some(selected => selected.id === item.id),
        //                 onclick: 'selectThisInvoice(this)',
        //             };
        //         }));
        //     }

        //     let modalData = {
        //         id: 'modalForm',
        //         class: 'h-[80%] w-full',
        //         cards: {name: 'Invoices', count: 4, data: cardData},
        //     }

        //     createModal(modalData);
        // }

        // function deselectInvoiceAtIndex(index) {
        //     if (index !== -1) {
        //         selectedInvoicesArray.splice(index, 1);
        //     }
        // }

        // function deselectThisInvoice(index) {
        //     totalCottonCount -=  selectedInvoicesArray[index].cotton_count;

        //     deselectInvoiceAtIndex(index);

        //     renderList();
        // }

        // function renderList() {
        //     if (selectedInvoicesArray.length > 0) {
        //         let clutter = "";
        //         selectedInvoicesArray.forEach((selectedInvoice, index) => {
        //             clutter += `
        //                 <div class="flex justify-between items-center border-t border-gray-600 py-3 px-4">
        //                     <div class="w-[10%]">${index+1}</div>
        //                     <div class="w-1/6">${formatDate(selectedInvoice.date)}</div>
        //                     <div class="w-1/6">${selectedInvoice.invoice_no}</div>
        //                     <div class="w-1/6">${selectedInvoice.cotton_count ?? '-'}</div>
        //                     <div class="grow">${selectedInvoice.customer.customer_name}</div>
        //                     <div class="w-[10%]">${selectedInvoice.customer.city.title}</div>
        //                     <div class="w-[10%] text-center">
        //                         <button onclick="deselectThisInvoice(${index})" type="button" class="text-[var(--danger-color)] cursor-pointer text-xs px-2 py-1 rounded-lg hover:text-[var(--h-danger-color)] transition-all duration-300 ease-in-out">
        //                             <i class="fas fa-trash"></i>
        //                         </button>
        //                     </div>
        //                 </div>
        //             `;
        //         });

        //         cargoListDOM.innerHTML = clutter;
        //     } else {
        //         cargoListDOM.innerHTML =
        //             `<div class="text-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4">No Invoices Yet</div>`;
        //     }
        //     finalTotalCottonsDOM.textContent = totalCottonCount;
        //     updateInputinvoicesArray();
        // }
        // renderList();

        // function updateInputinvoicesArray() {
        //     let inputinvoices = document.getElementById('invoices');
        //     let finalArticlesArray = selectedInvoicesArray.map(invoice => {
        //         return {
        //             id: invoice.id,
        //             description: invoice.description,
        //             shipment_quantity: invoice.shipmentQuantity
        //         }
        //     });
        //     inputinvoices.value = JSON.stringify(finalArticlesArray);
        // }

        // let companyData = @json(app('company'));
        // const previewDom = document.getElementById('preview');

        // function generateCargoListPreview() {
        //     let cargoNo = (parseInt(lastCargo.cargo_no) + 1).toString().padStart(4, '0');
        //     const cargoNameInpDom = document.getElementById("cargo_name");
        //     const dateInpDom = document.getElementById("date");

        //     if (selectedInvoicesArray.length > 0) {
        //         previewDom.innerHTML = `
        //             <div id="preview-document" class="preview-document flex flex-col h-full">
        //                 <div id="preview-banner" class="preview-banner w-full flex justify-between items-center mt-8 pl-5 pr-8">
        //                     <div class="left">
        //                         <div class="company-logo">
        //                             <img src="{{ asset('images/${companyData.logo}') }}" alt="Track Point"
        //                                 class="w-[12rem]" />
        //                         </div>
        //                     </div>
        //                     <div class="right">
        //                         <div>
        //                             <h1 class="text-2xl font-medium text-[var(--primary-color)] pr-2">Cargo List</h1>
        //                             <div class='mt-1'>${ companyData.phone_number }</div>
        //                         </div>
        //                     </div>
        //                 </div>
        //                 <hr class="w-full my-3 border-gray-600">
        //                 <div id="preview-header" class="preview-header w-full flex justify-between px-5">
        //                     <div class="left my-auto pr-3 text-sm text-gray-600 space-y-1.5">
        //                         <div class="cargo-date leading-none">Date: ${dateInpDom.value}</div>
        //                         <div class="cargo-number leading-none">Cargo No.: ${cargoNo}</div>
        //                         <input type="hidden" name="cargo_no" value="${cargoNo}" />
        //                     </div>
        //                     <div class="center my-auto">
        //                         <div class="cargo-name capitalize font-semibold text-md">Cargo Name: ${cargoNameInpDom.value}</div>
        //                     </div>
        //                     <div class="right my-auto pr-3 text-sm text-gray-600 space-y-1.5">
        //                         <div class="preview-copy leading-none">Cargo List Copy: Cargo</div>
        //                         <div class="preview-doc leading-none">Document: Cargo List</div>
        //                     </div>
        //                 </div>
        //                 <hr class="w-full my-3 border-gray-600">
        //                 <div id="preview-body" class="preview-body w-[95%] grow mx-auto">
        //                     <div class="preview-table w-full">
        //                         <div class="table w-full border border-gray-600 rounded-lg pb-2.5 overflow-hidden">
        //                             <div class="thead w-full">
        //                                 <div class="tr flex justify-between w-full px-4 py-1.5 bg-[var(--primary-color)] text-white">
        //                                     <div class="th text-sm font-medium w-[7%]">S.No</div>
        //                                     <div class="th text-sm font-medium w-1/6">Date</div>
        //                                     <div class="th text-sm font-medium w-1/6">Invoice No.</div>
        //                                     <div class="th text-sm font-medium w-1/6">Cotton</div>
        //                                     <div class="th text-sm font-medium grow">Customer</div>
        //                                     <div class="th text-sm font-medium w-1/6">City</div>
        //                                 </div>
        //                             </div>
        //                             <div id="tbody" class="tbody w-full">
        //                                 ${selectedInvoicesArray.map((invoice, index) => {
        //                                     const hrClass = index === 0 ? "mb-2.5" : "my-2.5";
        //                                     return `
        //                                         <div>
        //                                             <hr class="w-full ${hrClass} border-gray-600">
        //                                             <div class="tr flex justify-between w-full px-4">
        //                                                 <div class="td text-sm font-semibold w-[7%]">${index + 1}.</div>
        //                                                 <div class="td text-sm font-semibold w-1/6">${formatDate(invoice.date)}</div>
        //                                                 <div class="td text-sm font-semibold w-1/6">${invoice.invoice_no}</div>
        //                                                 <div class="td text-sm font-semibold w-1/6">${invoice.cotton_count}</div>
        //                                                 <div class="td text-sm font-semibold grow">${invoice.customer.customer_name}</div>
        //                                                 <div class="td text-sm font-semibold w-1/6">${invoice.customer.city.title}</div>
        //                                             </div>
        //                                         </div>
        //                                     `;
        //                                 }).join('')}
        //                             </div>
        //                         </div>
        //                     </div>
        //                 </div>
        //                 <hr class="w-full my-3 border-gray-600">
        //                 <div class="tfooter flex w-full text-sm px-4 justify-between mb-4 text-gray-600">
        //                     <P class="leading-none">Powered by SparkPair</P>
        //                     <p class="leading-none text-sm">&copy; 2025 Spark Pair | +92 316 5825495</p>
        //                 </div>
        //             </div>
        //         `;
        //     } else {
        //         previewDom.innerHTML = `
        //             <h1 class="text-[var(--border-error)] font-medium text-center mt-5">No Preview avalaible.</h1>
        //         `;
        //     }
        // }

        // function selectThisInvoice(invoiceElem) {
        //     let checkbox = invoiceElem.querySelector("input[type='checkbox']")
        //     checkbox.checked = !checkbox.checked;

        //     toggleInvoice(invoiceElem, checkbox);
        // }

        // function toggleInvoice(invoiceElem, checkbox) {
        //     if (checkbox.checked) {
        //         selectInvoice(invoiceElem);
        //     } else {
        //         deselectInvoice(invoiceElem);
        //     }
        // }

        // function selectInvoice(invoiceElem) {
        //     const invoiceData = JSON.parse(invoiceElem.dataset.json).data;

        //     const index = selectedInvoicesArray.findIndex(invoice => invoice.id === invoiceData.id);
        //     if (index == -1) {
        //         selectedInvoicesArray.push(invoiceData);
        //         totalCottonCount += invoiceData.cotton_count;
        //     }
        //     renderList()
        // }

        // function deselectInvoice(invoiceElem) {
        //     const invoiceData = JSON.parse(invoiceElem.dataset.json).data;

        //     const index = selectedInvoicesArray.findIndex(invoice => invoice.id === invoiceData.id);
        //     if (index > -1) {
        //         selectedInvoicesArray.splice(index, 1);
        //         totalCottonCount -= invoiceData.cotton_count;

        //         // selectAllCheckbox.checked = false;
        //     }
        //     renderList()
        // }

        function validateForNextStep() {
            // generateCargoListPreview()
            return true;
        }

        // function addListenerToPrintAndSaveBtn() {
        //     document.getElementById('printAndSaveBtn').addEventListener('click', (e) => {
        //         e.preventDefault();
        //         closeAllDropdowns();
        //         const preview = document.getElementById('preview-container'); // preview content

        //         // Pehle se agar koi iframe hai to usko remove karein
        //         let oldIframe = document.getElementById('printIframe');
        //         if (oldIframe) {
        //             oldIframe.remove();
        //         }

        //         // Naya iframe banayein
        //         let printIframe = document.createElement('iframe');
        //         printIframe.id = "printIframe";
        //         printIframe.style.position = "absolute";
        //         printIframe.style.width = "0px";
        //         printIframe.style.height = "0px";
        //         printIframe.style.border = "none";
        //         printIframe.style.display = "none"; // ✅ Hide iframe

        //         // Iframe ko body me add karein
        //         document.body.appendChild(printIframe);

        //         let printDocument = printIframe.contentDocument || printIframe.contentWindow.document;
        //         printDocument.open();

        //         // ✅ Current page ke CSS styles bhi iframe me inject karenge
        //         const headContent = document.head.innerHTML;

        //         printDocument.write(`
        //             <html>
        //                 <head>
        //                     <title>Print Invoice</title>
        //                     ${headContent} <!-- Copy current styles -->
        //                     <style>
        //                         @media print {

        //                             body {
        //                                 margin: 0;
        //                                 padding: 0;
        //                                 width: 210mm; /* A4 width */
        //                                 height: 297mm; /* A4 height */

        //                             }

        //                             .preview-container, .preview-container * {
        //                                 page-break-inside: avoid;
        //                             }
        //                         }
        //                     </style>
        //                 </head>
        //                 <body>
        //                     <div class="preview-container pt-3">${preview.innerHTML}</div> <!-- Add the preview content, only innerHTML -->
        //                     <div id="preview-container" class="preview-container pt-3">${preview.innerHTML}</div> <!-- Add the preview content, only innerHTML -->
        //                 </body>
        //             </html>
        //         `);

        //         printDocument.close();

        //         // Wait for iframe to load and print
        //         printIframe.onload = () => {
        //             let orderCopy = printDocument.querySelector('#preview-container .invoice-copy');
        //             if (orderCopy) {
        //                 orderCopy.textContent = "Invoice Copy: Office";
        //             }

        //             // Listen for after print in the iframe's window
        //             printIframe.contentWindow.onafterprint = () => {
        //                 document.getElementById('form').submit();
        //             };

        //             setTimeout(() => {
        //                 printIframe.contentWindow.focus();
        //                 printIframe.contentWindow.print();
        //             }, 1000);
        //         };
        //     });
        // }

        // document.addEventListener("DOMContentLoaded", ()=>{
        //     addListenerToPrintAndSaveBtn();
        // });
    </script>
@endsection
