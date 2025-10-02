@extends('app')
@section('title', 'Show Vouchers | ' . app('company')->name)
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
            "Voucher No" => [
                "id" => "voucher_no",
                "type" => "text",
                "placeholder" => "Enter voucher number",
                "oninput" => "runDynamicFilter()",
                "dataFilterPath" => "voucher_no",
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

    <div class="w-[80%] mx-auto">
        <x-search-header heading="Vouchers" :search_fields=$searchFields/>
    </div>

    <!-- Main Content -->
    <section class="text-center mx-auto ">
        <div
            class="show-box mx-auto w-[80%] h-[70vh] bg-[var(--secondary-bg-color)] border border-[var(--glass-border-color)]/20 rounded-xl shadow overflow-y-auto pt-8.5 relative">
            <x-form-title-bar title="Show Vouchers" changeLayoutBtn layout="{{ $authLayout }}" resetSortBtn />

            @if (count($vouchers) > 0)
                <div class="absolute bottom-3 right-3 flex items-center gap-2 w-fll z-50">
                    <x-section-navigation-button link="{{ route('vouchers.create') }}" title="Add New Payment" icon="fa-plus" />
                </div>

                <div class="details h-full z-40">
                    <div class="container-parent h-full overflow-y-auto my-scrollbar-2">
                        <div class="card_container px-3 h-full flex flex-col">
                            <div id="table-head" class="grid grid-cols-4 bg-[var(--h-bg-color)] rounded-lg font-medium py-2 hidden mt-4 mx-2">
                                <div class="text-center cursor-pointer" onclick="sortByThis(this)">Supplier</div>
                                <div class="text-center cursor-pointer" onclick="sortByThis(this)">Voucher No</div>
                                <div class="text-center cursor-pointer" onclick="sortByThis(this)">Date</div>
                                <div class="text-center cursor-pointer" onclick="sortByThis(this)">Amount</div>
                            </div>
                            <p id="noItemsError" style="display: none" class="text-sm text-[var(--border-error)] mt-3">No items found</p>
                            <div>
                                <div class="search_container grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 overflow-y-auto grow my-scrollbar-2">
                                    {{-- class="search_container overflow-y-auto grow my-scrollbar-2"> --}}
                                </div>
                            </div>
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
                        <button id="print" type="button"
                            class="w-full px-4 py-2 text-left hover:bg-[var(--h-bg-color)] rounded-md transition-all duration-300 ease-in-out cursor-pointer">Print
                            Voucher</button>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    <script>
        let companyData = @json(app('company'));
        let authLayout = '{{ $authLayout }}';

        function createRow(data) {
            return `
                <div id="${data.id}" oncontextmenu='${data.oncontextmenu || ""}' onclick='${data.onclick || ""}'
                    class="item row relative group grid text- grid-cols-4 border-b border-[var(--h-bg-color)] items-center py-2 cursor-pointer hover:bg-[var(--h-secondary-bg-color)] transition-all fade-in ease-in-out"
                    data-json='${JSON.stringify(data)}'>

                    <span class="text-center">${data.details["Supplier"]}</span>
                    <span class="text-center">${data.name}</span>
                    <span class="text-center">${data.details['Date']}</span>
                    <span class="text-center">${data.details['Amount']}</span>
                </div>
            `;
        }

        const fetchedData = @json($vouchers);
        let allDataArray = fetchedData.map(item => {
            return {
                id: item.id,
                name: item.voucher_no,
                details: {
                    'Supplier': item.supplier ? item.supplier.supplier_name : 'Al Jobat',
                    'Date': formatDate(item.date),
                    'Amount': formatNumbersWithDigits(item.total_payment, 1, 1),
                },
                data: item,
                oncontextmenu: "generateContextMenu(event)",
                onclick: "generateModal(this)",
                visible: true,
            };
        });

        function printVoucher(elem) {
            closeAllDropdowns();

            if (elem.parentElement.tagName.toLowerCase() === 'li') {
                elem.parentElement.parentElement.querySelector('#show-details').click();
                document.getElementById('modalForm').parentElement.classList.add('hidden');
            }

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
                        <title>Print Voucher</title>
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
                let shipmentCopy = printDocument.querySelector('#preview-container .preview-copy');
                if (shipmentCopy) {
                    shipmentCopy.textContent = "Shipment Copy: Office";
                }

                // Listen for after print in the iframe's window
                printIframe.contentWindow.onafterprint = () => {
                    console.log("Print dialog closed");
                };

                setTimeout(() => {
                    printIframe.contentWindow.focus();
                    printIframe.contentWindow.print();
                }, 1000);

                document.getElementById('modalForm').parentElement.remove();
            };
        }

        function generateContextMenu(e) {
            e.preventDefault();
            let item = e.target.closest('.item');
            let data = JSON.parse(item.dataset.json);

            let contextMenuData = {
                item: item,
                data: data,
                x: e.pageX,
                y: e.pageY,
                actions: [
                    {id: 'print', text: 'Print Voucher', onclick: 'printVoucher(this)'}
                ]
            };

            createContextMenu(contextMenuData);
        }

        function generateModal(item) {
            let data = JSON.parse(item.dataset.json);

            let modalData = {
                id: 'modalForm',
                preview: {type: 'voucher', data: data.data, document: 'Voucher'},
                bottomActions: [
                    {id: 'print', text: 'Print Voucher', onclick: 'printVoucher(this)'}
                ],
            }

            createModal(modalData);
        }
    </script>
@endsection
