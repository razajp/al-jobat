@extends('app')
@section('title', 'Generate Invoice | ' . app('company')->name)
@section('content')
    <div id="invoice-container" class="hidden"></div>

    <script>
        let invoices = @json($invoices);
        let companyData = @json(app('company'));
        console.log(invoices);

        let invoiceContainer = document.getElementById("invoice-container");

        document.addEventListener("DOMContentLoaded", ()=>{
            invoices.forEach((invoice)=>{
                let previewDom = document.createElement("div");
                previewDom.classList = "invoice";

                customerData = invoice.customer;
                let totalQuantity = 0;
                let totalAmount = 0;
                let cottonCount = invoice.cotton_count;
                if (invoice.shipment.fetchedArticles.length > 0) {  
                    previewDom.innerHTML = `
                        <div class="preview-container w-[210mm] h-[297mm] mx-auto overflow-hidden relative">
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
                                                <div class="mt-1 text-right ${cottonCount == 0 ? '' : ''} pr-2">Cotton: ${cottonCount}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="w-full my-3 border-black">
                                    <div id="invoice-header" class="invoice-header w-full flex justify-between px-5">
                                        <div class="left w-50 space-y-1">
                                            <div class="invoice-customer text-lg leading-none">M/s: ${customerData.customer_name}</div>
                                            <div class="invoice-person text-md text-lg leading-none">${customerData.urdu_title}</div>
                                            <div class="invoice-address text-md leading-none">${customerData.address}, ${customerData.city.title}</div>
                                            <div class="invoice-phone text-md leading-none">${customerData.phone_number}</div>
                                        </div>
                                        <div class="right my-auto pr-3 text-sm text-black space-y-1.5">
                                            <div class="invoice-date leading-none">Date: ${invoice.date}</div>
                                            <div class="invoice-number leading-none">Invoice No.: ${invoice.invoice_no}</div>
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
                                                    ${invoice.shipment.fetchedArticles.map((article, index) => {
                                                        const hrClass = index === 0 ? "mb-2.5" : "my-2.5";
                                                        let quantity = article.shipment_quantity * cottonCount;
                                                        totalQuantity += quantity;
                                                        totalAmount += parseInt(article.article.sales_rate) * quantity;
                                                        return `
                                                            <div>
                                                                <hr class="w-full ${hrClass} border-black">
                                                                <div class="tr flex justify-between w-full px-4">
                                                                    <div class="td text-sm font-semibold w-[7%]">${index + 1}.</div>
                                                                    <div class="td text-sm font-semibold w-[10%]">${article.article.article_no}</div>
                                                                    <div class="td text-sm font-semibold w-[10%]">${quantity / article.article.pcs_per_packet}</div>
                                                                    <div class="td text-sm font-semibold w-[10%]">${quantity}</div>
                                                                    <div class="td text-sm font-semibold grow">${article.description}</div>
                                                                    <div class="td text-sm font-semibold w-[10%]">${formatNumbersDigitLess(article.article.pcs_per_packet)}</div>
                                                                    <div class="td text-sm font-semibold w-[11%]">${formatNumbersWithDigits(article.article.sales_rate, 2, 2)}</div>
                                                                    <div class="td text-sm font-semibold w-[11%]">${formatNumbersWithDigits(parseInt(article.article.sales_rate) * quantity, 1, 1)}</div>
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
                                                <div class="w-1/2 text-right grow">${formatNumbersDigitLess(invoice.shipment.discount)}</div>
                                            </div>
                                            <div
                                                class="total flex justify-between items-center border border-black rounded-lg py-1.5 px-4 w-full">
                                                <div class="text-nowrap">Net Amount</div>
                                                <div class="w-1/2 text-right grow">${formatNumbersWithDigits(invoice.netAmount, 1, 1)}</div>
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
                    `;
                } else {
                    previewDom.innerHTML = `
                        <h1 class="text-[var(--border-error)] font-medium text-center mt-5">No Preview avalaible.</h1>
                    `;
                }

                invoiceContainer.prependChild(previewDom);
                addListenerToPrintInvoice()
            });
        })

        function addListenerToPrintInvoice() {
            closeAllDropdowns();
            const previews = document.querySelectorAll('.preview-container'); // preview content

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

            let printBody = "";
            previews.forEach((preview)=>{
                printBody += `
                    <div class="preview-container pt-3">${preview.innerHTML}</div> <!-- Add the preview content, only innerHTML -->
                    <div class="forOffice preview-container pt-3">${preview.innerHTML}</div> <!-- Add the preview content, only innerHTML -->
                `;
            });

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
                        ${printBody}
                    </body>
                </html>
            `);

            printDocument.close();

            // Wait for iframe to load and print
            printIframe.onload = () => {
                let forOffices = printDocument.querySelectorAll('.forOffice .invoice-copy');
                forOffices.forEach((forOffice)=>{
                    if (forOffice) {
                        forOffice.textContent = "Invoice Copy: Office";
                    }
                })
                // Listen for after print in the iframe's window
                printIframe.contentWindow.onafterprint = () => {
                    window.location.href = '/invoices/create';
                };

                setTimeout(() => {
                    printIframe.contentWindow.focus();
                    printIframe.contentWindow.print();
                }, 1000);
            };
        }
    </script>
@endsection