@extends('app')
@section('title', 'Generate Invoice | ' . app('company')->name)
@section('content')
    <!-- Main Content -->
    <h1 class="text-3xl font-bold mb-6 text-center text-[--primary-color] fade-in"> Generate Invoice </h1>

    <!-- Progress Bar -->
    <div class="mb-5 max-w-4xl mx-auto">
        <x-progress-bar :steps="['Generate Invoice', 'Preview']" :currentStep="1" />
    </div>

    <!-- Form -->
    <form id="form" action="{{ route('invoices.store') }}" method="post" enctype="multipart/form-data"
        class="bg-[--secondary-bg-color] text-sm rounded-xl shadow-lg p-8 border border-[--h-bg-color] pt-12 max-w-4xl mx-auto  relative overflow-hidden">
        @csrf
        <div
            class="form-title text-center absolute top-0 left-0 w-full bg-[--primary-color] py-1 capitalize tracking-wide font-medium text-sm">
            <h4>Generate New Invoice</h4>
        </div>

        <!-- Step 1: Generate Invoice -->
        <div class="step1 space-y-4 ">
            <div class="flex justify-between gap-4">
                <input type="hidden" name="date" value='{{ now()->toDateString() }}'>
                {{-- order_no --}}
                <div class="grow">
                    <x-input label="Order Number" name="order_no" id="order_no" placeholder="Enter order number" required withButton btnId="generateInvoiceBtn" btnText="Generate Invoice" value="2025-"/>
                </div>
            </div>
            {{-- rate showing --}}
            <div id="article-table" class="w-full text-left text-sm">
                <div class="flex justify-between items-center bg-[--h-bg-color] rounded-lg py-2 px-4 mb-4">
                    <div class="w-[5%]">#</div>
                    <div class="w-[11%]">Article</div>
                    <div class="w-[11%]">Packets</div>
                    <div class="w-[10%]">Pcs</div>
                    <div class="grow">Decs.</div>
                    <div class="w-[8%]">Pcs/Pkt.</div>
                    <div class="w-[12%] text-right">Rate/Pc</div>
                    <div class="w-[15%] text-right">Amount</div>
                </div>
                <div id="article-list" class="h-[20rem] overflow-y-auto my-scrollbar-2">
                    <div class="text-center bg-[--h-bg-color] rounded-lg py-3 px-4">No Rates Added</div>
                </div>
            </div>

            <div class="flex w-full grid grid-cols-1 md:grid-cols-2 gap-3 text-sm mt-5 text-nowrap">
                <div class="total-qty flex justify-between items-center bg-[--h-bg-color] rounded-lg py-2 px-4 w-full">
                    <div class="grow">Total Quantity - Pcs</div>
                    <div id="totalQuantityInForm">0</div>
                </div>
                <div class="final flex justify-between items-center bg-[--h-bg-color] rounded-lg py-2 px-4 w-full">
                    <div class="grow">Gross Amount - Rs.</div>
                    <div id="totalAmountInForm">0.0</div>
                </div>
                <div class="final flex justify-between items-center bg-[--h-bg-color] rounded-lg py-2 px-4 w-full">
                    <div class="grow">Discount - %</div>
                    <div id="dicountInForm">0</div>
                </div>
                <div class="final flex justify-between items-center bg-[--h-bg-color] rounded-lg py-2 px-4 w-full">
                    <div class="grow">Net Amount - Rs.</div>
                    <input type="text" name="netAmount" id="netAmountInForm" value="0.0" readonly
                        class="text-right bg-transparent outline-none w-1/2 border-none" />
                </div>
            </div>
            <input type="hidden" name="articles_in_invoice" id="articles_in_invoice" value="">
        </div>

        <!-- Step 2: view order -->
        <div class="step2 hidden space-y-4 text-black h-[35rem] overflow-y-auto my-scrollbar-2 bg-white rounded-md">
            <div id="preview-container" class="w-[210mm] h-[297mm] mx-auto overflow-hidden relative">
                <div id="preview" class="preview flex flex-col h-full">
                    <h1 class="text-[--border-error] font-medium text-center mt-5">No Preview avalaible.</h1>
                    {{-- <div id="order" class="order flex flex-col h-full">
                        <div id="order-banner" class="order-banner w-full flex justify-between mt-8 px-5">
                            <div class="left w-50">
                                <div class="order-logo">
                                    <img src="{{ asset('images/company-logo.png') }}" alt="Track Point"
                                        class="w-[150px]" />
                                </div>
                            </div>
                            <div class="right w-50 my-auto pr-3 text-sm text-gray-500">
                                <div class="order-date">Date: 02-03-2025</div>
                                <div class="order-number">Order No.: 2025-0000</div>
                                <div class="order-copy">Order Copy: Customer</div>
                            </div>
                        </div>
                        <hr class="w-100 my-5 border-gray-600">
                        <div id="order-header" class="order-header w-full flex justify-between px-5">
                            <div class="left w-50">
                                <div class="order-to text-sm text-gray-500">Order to:</div>
                                <div class="order-customer text-lg">Karachi Garments</div>
                                <div class="order-person text-md">Hassan</div>
                                <div class="order-address text-md">Karachi</div>
                                <div class="order-phone text-md">0316-5825495</div>
                            </div>
                            <div class="right w-50">
                                <div class="order-from text-sm text-gray-500">Order from:</div>
                                <div class="order-customer text-lg">M/s Track Point</div>
                                <div class="order-person text-md">Mr. Hasan</div>
                                <div class="order-address text-md">Meetha Dar, Karachi</div>
                                <div class="order-phone text-md">0312-5214864</div>
                            </div>
                        </div>
                        <hr class="w-100 mt-5 mb-5 border-gray-600">
                        <div id="order-body" class="order-body w-[95%] grow mx-auto">
                            <div class="order-table w-full">
                                <div class="table w-full border border-gray-600 rounded-lg pb-4 overflow-hidden">
                                    <div class="thead w-full">
                                        <div class="tr flex justify-between w-full px-4 py-2 bg-[--primary-color] text-white">
                                            <div class="th text-sm font-medium w-[5%]"></div>
                                            <div class="th text-sm font-medium w-[10%]">#</div>
                                            <div class="th text-sm font-medium w-1/6">Qty/Pcs.</div>
                                            <div class="th text-sm font-medium grow">Desc.</div>
                                            <div class="th text-sm font-medium w-1/6">Rate</div>
                                            <div class="th text-sm font-medium w-1/6">Amount</div>
                                            <div class="th text-sm font-medium w-[12%]">Packed Qty.</div>
                                        </div>
                                    </div>
                                    <div id="tbody" class="tbody w-full">
                                        <div>
                                            <hr class="w-full mb-3 border-gray-600">
                                            <div class="tr flex justify-between w-full px-4">
                                                <div class="td text-sm font-semibold w-[5%] flex items-center"><input type="checkbox" class="mr-2"></div>
                                                <div class="td text-sm font-semibold w-[10%]">1</div>
                                                <div class="td text-sm font-semibold w-1/6">300</div>
                                                <div class="td text-sm font-semibold grow">Hello</div>
                                                <div class="td text-sm font-semibold w-1/6">250.00</div>
                                                <div class="td text-sm font-semibold w-1/6">1,200.0</div>
                                                <div class="td text-sm font-semibold w-[12%]">____________</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr class="w-full my-4 border-gray-600">
                        <div class="flex flex-col space-y-2">
                            <div id="order-total" class="tr flex justify-between w-full px-2 gap-2 text-sm">
                                <div class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                                    <div class="text-nowrap">Total Quantity - Pcs</div>
                                    <div class="w-1/4 text-right grow">1,200</div>
                                </div>
                                <div class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                                    <div class="text-nowrap">Total Amount</div>
                                    <div class="w-1/4 text-right grow">12,000.0</div>
                                </div>
                                <div class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                                    <div class="text-nowrap">Discount - %</div>
                                    <div class="w-1/4 text-right grow">0</div>
                                </div>
                            </div>
                            <div id="order-total" class="tr flex justify-between w-full px-2 gap-2 text-sm">
                                <div class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                                    <div class="text-nowrap">Previous Balance</div>
                                    <div class="w-1/4 text-right grow">150,000.0</div>
                                </div>
                                <div
                                    class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                                    <div class="text-nowrap">Net Amount</div>
                                    <div class="w-1/4 text-right grow">12,000.0</div>
                                </div>
                                <div
                                    class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                                    <div class="text-nowrap">Current Balance</div>
                                    <div class="w-1/4 text-right grow">14,000.0</div>
                                </div>
                            </div>
                        </div>
                        <hr class="w-full my-4 border-gray-600">
                        <div class="tfooter flex w-full text-sm px-4 justify-between mb-4">
                            <P>Company Name</P>
                            <p>&copy; Track Point | sparkpair.com | Spark Pair 2025.</p>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
    </form>

    <script>
        let orderedArticles = [];
        let articlesInInvoice = [];
        let totalQuantityPcs = 0;
        let totalAmount = 0;
        let netAmount = 0;
        let discount = 0;

        const lastInvoice = @json($last_Invoice);
        let customerData;
        const articleModalDom = document.getElementById("articleModal");
        const quantityModalDom = document.getElementById("quantityModal");
        const orderNoDom = document.getElementById("order_no");
        const generateInvoiceBtn = document.getElementById("generateInvoiceBtn");
        generateInvoiceBtn.disabled = true;
        
        // Calc Bottom
        let totalQuantityInFormDom = document.getElementById('totalQuantityInForm');
        let totalAmountInFormDom = document.getElementById('totalAmountInForm');
        let dicountInFormDom = document.getElementById('dicountInForm');
        let netAmountInFormDom = document.getElementById('netAmountInForm');

        let totalQuantityDOM;
        let totalAmountDOM;

        let isModalOpened = false;
        let isQuantityModalOpened = false;

        orderNoDom.addEventListener('input', (e) => {
            let value = e.target.value;

            value = value.replace(/\D/g, '');

            if (value.length > 4) {
                value = value.slice(0, 4) + '-' + value.slice(4);
            }

            if (value.includes('-')) {
                let parts = value.split('-');
                parts[1] = parts[1].slice(0, 4);
                value = parts.join('-');
            }

            e.target.value = value;

            trackStateOfOrderNo(e.target.value);
        });

        orderNoDom.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                generateInvoiceBtn.click();
            }
        });

        generateInvoiceBtn.addEventListener('click', function () {
            getOrderDetails();
        });

        function getOrderDetails() {
            $.ajax({
                url: "/get-order-details",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    order_no: orderNoDom.value
                },
                success: function (response) {
                    console.log(response);
                    
                    orderedArticles = response.ordered_articles;
                    discount = response.discount;
                    customerData = response.customer;
                    
                    renderList();
                    renderCalcBottom();
                }
            });
        }

        function trackStateOfOrderNo(value) {
            if (value != "") {
                generateInvoiceBtn.disabled = false;
            } else {
                generateInvoiceBtn.disabled = true;
            }
        }

        const articleListDOM = document.getElementById('article-list');

        function renderList() {
            if (orderedArticles.length > 0) {
                totalAmount = 0;
                totalQuantityPcs = 0;

                let clutter = "";
                orderedArticles.forEach((selectedArticle, index) => {
                    
                    if (selectedArticle.total_physical_stock_packets > 0) {
                        let orderedQuantity = selectedArticle.ordered_quantity;
                        let totalPhysicalStockPackets = selectedArticle.total_physical_stock_packets;
                        let totalPhysicalStockPcs = selectedArticle.total_physical_stock_packets * selectedArticle.article.pcs_per_packet;
                        let orderedPhysicalQuantity = Math.floor(totalPhysicalStockPackets);
                        
                        totalQuantityPcs += orderedPhysicalQuantity * selectedArticle.article.pcs_per_packet;

                        // console.log(orderedPhysicalQuantity * selectedArticle.article.pcs_per_packet);

                        let articleAmount = (selectedArticle.article.sales_rate * selectedArticle.article.pcs_per_packet) * orderedPhysicalQuantity;
                        
                        clutter += `
                            <div class="flex justify-between items-center border-t border-gray-600 py-3 px-4">
                                <div class="w-[5%]">${index + 1}.</div>
                                <div class="w-[11%]">#${selectedArticle.article.article_no}</div>
                                <div class="w-[11%]">
                                    <input type="number" class="w-full bg-transparent focus:outline-none" value="${orderedPhysicalQuantity}" max="${orderedPhysicalQuantity}" onclick='this.select()' oninput="packetEdited(this)" />
                                </div>
                                <div class="w-[10%]">${formatNumbersDigitLess(orderedPhysicalQuantity * selectedArticle.article.pcs_per_packet)}</div>
                                <div class="grow">${selectedArticle.description}</div>
                                <div class="w-[8%]">${selectedArticle.article.pcs_per_packet}</div>
                                <div class="w-[12%] text-right">${formatNumbersWithDigits(selectedArticle.article.sales_rate, 1, 1)}</div>
                                <div class="w-[15%] text-right">${formatNumbersWithDigits(articleAmount, 1, 1)}</div>
                            </div>
                        `;

                        totalAmount += articleAmount;

                        selectedArticle.packets = orderedPhysicalQuantity
                        selectedArticle.ordered_quantity = orderedPhysicalQuantity * selectedArticle.article.pcs_per_packet
                    }
                });

                articleListDOM.innerHTML = clutter;
            } else {
                articleListDOM.innerHTML =
                    `<div class="text-center bg-[--h-bg-color] rounded-lg py-2 px-4">No Orders Yet</div>`;
            }
        }
        renderList();

        function updateInputArticlesInInvoice() {
            const articlesInInvoiceInpDom = document.getElementById("articles_in_invoice");
            let finalArticlesArray = orderedArticles.map(article => {
                return {
                    id: article.article.id,
                    description: article.description,
                    invoice_quantity: article.ordered_quantity,
                }
            })
            articlesInInvoiceInpDom.value = JSON.stringify(finalArticlesArray);
            console.log(finalArticlesArray);
        }

        function renderCalcBottom() {
            netAmount = totalAmount - (totalAmount * (discount / 100));
            totalQuantityInFormDom.textContent = formatNumbersDigitLess(totalQuantityPcs);
            totalAmountInFormDom.textContent = formatNumbersWithDigits(totalAmount, 1, 1);
            dicountInFormDom.textContent = discount;
            netAmountInFormDom.value = formatNumbersWithDigits(netAmount, 1, 1);
        }

        function packetEdited(elem) {
            let max = parseInt(elem.max);
            
            if (elem.value > max) {
                elem.value = max;
            } else if (elem.value < 1) {
                elem.value = 1;
            }

            elem.value = elem.value.replace(/\./g, '');

            calculateAndApplyChangesOnOrderArticle(elem);
        }

        function calculateAndApplyChangesOnOrderArticle(elem) {
            let childrenDom = elem.parentElement.parentElement.children;

            let packetsValue = parseInt(elem.value);

            let articleNoInRowDom = childrenDom[1];
            let pcsInRowDom = childrenDom[3];
            totalQuantityPcs -= parseInt(pcsInRowDom.textContent.replace(/[,]/g, ''));
            let pcsPerPktInRowDom = childrenDom[5];
            let ratePerPcInRowDom = childrenDom[6];

            let amountInRowDom = childrenDom[childrenDom.length - 1];
            totalAmount -= parseInt(amountInRowDom.textContent.replace(/[,]/g, ''));

            let pcsCalculated = packetsValue * parseInt(pcsPerPktInRowDom.textContent);
            totalQuantityPcs += pcsCalculated;

            pcsInRowDom.textContent = formatNumbersDigitLess(pcsCalculated) || 0;

            let amountCalculated = parseInt(pcsInRowDom.textContent.replace(/[,]/g, '')) * parseInt(ratePerPcInRowDom.textContent.replace(/[,]/g, ''));
            totalAmount += amountCalculated;
            
            amountInRowDom.textContent = formatNumbersWithDigits(amountCalculated, 1, 1) || 0.0;

            let currentArticle = orderedArticles.find(article => article.article.article_no == parseInt(articleNoInRowDom.textContent.replace(/#/g, '')))
            
            if (currentArticle) {
                currentArticle.packets = packetsValue
                currentArticle.ordered_quantity = pcsCalculated
            }
            
            
            renderCalcBottom();
        }

        let companyData = @json(app('company'));
        let invoiceNo;
        let invoiceDate;
        const previewDom = document.getElementById('preview');

        function generateInvoiceNo() {
            let lastInvoiceNo = lastInvoice.invoice_no.replace("2025-", "")
            const todayYear = new Date().getFullYear();
            const nextInvoiceNo = String(parseInt(lastInvoiceNo, 10) + 1).padStart(4, '0');
            return todayYear + '-' + nextInvoiceNo;
        }

        function getInvoiceDate() {
            const date = new Date();

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

        function generateInvoice() {
            invoiceNo = generateInvoiceNo();
            invoiceDate = getInvoiceDate();
            
            if (orderedArticles.length > 0) {
                previewDom.innerHTML = `
                    <div id="invoice" class="invoice flex flex-col h-full">
                        <div id="invoice-banner" class="invoice-banner w-full flex justify-between mt-8 px-5">
                            <div class="left w-50">
                                <div class="invoice-logo">
                                    <img src="{{ asset('images/${companyData.logo}') }}" alt="Track Point"
                                        class="w-[150px]" />
                                </div>
                            </div>
                            <div class="right w-50 my-auto pr-3 text-sm text-gray-500">
                                <div class="invoice-date">Date: ${invoiceDate}</div>
                                <div class="invoice-number">Invoice No.: ${invoiceNo}</div>
                                <input type="hidden" name="invoice_no" value="${invoiceNo}">
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
                                        ${orderedArticles.map((articles, index) => {
                                            if (index == 0) {
                                                return `
                                                        <div>
                                                            <hr class="w-full mb-3 border-gray-600">
                                                            <div class="tr flex justify-between w-full px-4">
                                                                <div class="td text-sm font-semibold w-[5%]">${index + 1}.</div>
                                                                <div class="td text-sm font-semibold w-[11%]">#${articles.article.article_no}</div>
                                                                <div class="td text-sm font-semibold w-[11%]">${articles.packets}</div>
                                                                <div class="td text-sm font-semibold w-[10%]">${articles.ordered_quantity}</div>
                                                                <div class="td text-sm font-semibold grow">${articles.description}</div>
                                                                <div class="td text-sm font-semibold w-[8%]">${formatNumbersDigitLess(articles.article.pcs_per_packet)}</div>
                                                                <div class="td text-sm font-semibold w-[12%]">${formatNumbersWithDigits(articles.article.sales_rate, 2, 2)}</div>
                                                                <div class="td text-sm font-semibold w-[15%]">${formatNumbersWithDigits(parseInt(articles.article.sales_rate) * articles.ordered_quantity, 1, 1)}</div>
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
                                                                <div class="td text-sm font-semibold w-[11%]">${articles.packets}</div>
                                                                <div class="td text-sm font-semibold w-[10%]">${articles.ordered_quantity}</div>
                                                                <div class="td text-sm font-semibold grow">${articles.description}</div>
                                                                <div class="td text-sm font-semibold w-[8%]">${formatNumbersDigitLess(articles.article.pcs_per_packet)}</div>
                                                                <div class="td text-sm font-semibold w-[12%]">${formatNumbersWithDigits(articles.article.sales_rate, 2, 2)}</div>
                                                                <div class="td text-sm font-semibold w-[15%]">${formatNumbersWithDigits(parseInt(articles.article.sales_rate) * articles.ordered_quantity, 1, 1)}</div>
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
                                    <div class="w-1/4 text-right grow">${formatNumbersDigitLess(totalQuantityPcs)}</div>
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
                `;
            } else {
                previewDom.innerHTML = `
                    <h1 class="text-[--border-error] font-medium text-center mt-5">No Preview avalaible.</h1>
                `;
            }
        }

        function validateForNextStep() {
            generateInvoice()
            updateInputArticlesInInvoice();
            return true;
        }
    </script>
@endsection
