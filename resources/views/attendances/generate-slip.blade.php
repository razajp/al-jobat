@extends('app')
@section('title', 'Generate Slip | ' . app('company')->name)
@section('content')
    <!-- Main Content -->
    <!-- Progress Bar -->
    <div class="mb-5 max-w-4xl mx-auto">
        <x-search-header heading="Generate Slip" link linkText="Manage Salary" linkHref="{{ route('attendances.manage-salary') }}"/>
        <x-progress-bar :steps="['Generate Slip', 'Preview']" :currentStep="1" />
    </div>

    <!-- Form -->
    <form id="form" action="{{ route('attendances.generate-slip-post') }}" method="post" enctype="multipart/form-data"
        class="bg-[var(--secondary-bg-color)] text-sm rounded-xl shadow-lg p-8 border border-[var(--glass-border-color)]/20 pt-14 max-w-4xl mx-auto relative overflow-hidden">
        @csrf
        <x-form-title-bar title="Generate Slip" />

        <!-- Step 1: Generate shipment -->
        <div class="step1 space-y-4 ">
            <div class="">
                {{-- month --}}
                <x-input label="Month" name="month" id="month" type="month" required />
            </div>
        </div>

        <!-- Step 2: view shipment -->
        <div class="step2 hidden space-y-4 text-black h-[35rem] overflow-y-auto my-scrollbar-2 bg-white rounded-md">
            <div id="preview-container" class="w-[297mm] h-[210mm] mx-auto overflow-hidden relative">
                <div id="preview" class="preview flex flex-col h-full">
                    <h1 class="text-[var(--border-error)] font-medium text-center mt-5">No Preview avalaible.</h1>
                </div>
            </div>
        </div>
    </form>

    <script>
        const nameDom = document.getElementById('month');

        function generateSlipPreview() {
            $.ajax({
                url: '/attendances/generate-slip',
                type: 'POST',
                data: {
                    month: nameDom.value
                }, // Optional if you want to send any data, can be left empty
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log(response);

                    const preview = document.getElementById("preview");
                    preview.innerHTML = ""; // clear old content

                    const perPage = 4;
                    let page;

                    page = document.createElement("div");
                    page.className = "w-full grid grid-cols-4 gap-x-4 h-full px-4.5";
                    preview.appendChild(page);

                    response.forEach((emp) => {
                        emp.records.push({ date: `${emp.month}-31`, time: '-' });

                        // Employee Block
                        const empBlock = document.createElement("div");
                        empBlock.className = "employee-block h-[210mm] flex items-center p-2";

                        empBlock.innerHTML = `
                            <div class="grow">
                                <div class="mb-1 p-1 text-center border border-gray-600 rounded-lg">
                                    <h2 class="text-lg font-bold text-gray-800 tracking-wide">${emp.employee_name}</h2>
                                    <p class="text-xs text-gray-600">${emp.month}</p>
                                </div>
                                <div class="overflow-x-auto font-medium">
                                    <div class="w-full border border-gray-600 text-[8px] text-gray-700 rounded-lg overflow-hidden p-1">
                                        <div class="bg-[var(--primary-color)] text-white rounded-md">
                                            <div class="grid grid-cols-2 text-center">
                                                <div class="border-r border-white py-1 px-2">Date</div>
                                                <div class="py-1 px-2">Time</div>
                                            </div>
                                        </div>
                                        <div>
                                            ${emp.records.map((r, i) => {
                                                const dateObj = new Date(r.date);
                                                const isSunday = dateObj.getDay() === 0;
                                                const noTime = r.time === '-';

                                                let rowBg = '';
                                                if (isSunday) {
                                                    rowBg = 'bg-blue-100 text-blue-800 font-semibold'; // Always Sunday color
                                                } else if (noTime) {
                                                    rowBg = 'bg-red-200 text-red-800 font-semibold'; // Only non-Sunday missing time = red
                                                }

                                                return `
                                                    <div class="grid grid-cols-2 text-center ${rowBg} ${i === emp.records.length - 1 ? 'rounded-b-md' : ''}">
                                                        <div class="border-r border-gray-600 py-1 px-2 ${i === emp.records.length - 1 ? '' : 'border-b border-gray-600'}">
                                                            ${formatDate(r.date)}
                                                        </div>
                                                        <div class="py-1 px-2 ${i === emp.records.length - 1 ? '' : 'border-b border-gray-600'}">
                                                            ${r.time}
                                                        </div>
                                                    </div>
                                                `;
                                            }).join('')}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-[10px] text-gray-600 flex justify-between mt-1 leading-none tracking-wide px-2.5 pt-1 border-t border-gray-600"><p>Al-Jobat</p><p>SpakrPair</p></div>
                            </div>
                        `;

                        page.appendChild(empBlock);
                    });
                }
            });
        }

        function validateForNextStep() {
            if (!nameDom.value) return false;
            console.log("ran");

            generateSlipPreview();
            return true;
        }

        function onClickOnPrintBtn() {
            const preview = document.getElementById('preview-container'); // preview content

            // ✅ Clone so that original DOM safe rahe
            let clone = preview.cloneNode(true);

            // ✅ Sirf direct child <hr> (pages ke beech) remove karo
            clone.querySelectorAll(":scope > hr").forEach(hr => hr.remove());

            // Agar pehle se iframe hai to usko hatao
            let oldIframe = document.getElementById('printIframe');
            if (oldIframe) {
                oldIframe.remove();
            }

            // Naya iframe banao
            let printIframe = document.createElement('iframe');
            printIframe.id = "printIframe";
            printIframe.style.position = "absolute";
            printIframe.style.width = "0px";
            printIframe.style.height = "0px";
            printIframe.style.border = "none";
            printIframe.style.display = "none";

            document.body.appendChild(printIframe);

            let printDocument = printIframe.contentDocument || printIframe.contentWindow.document;
            printDocument.open();

            // ✅ Copy styles from current page
            const headContent = document.head.innerHTML;

            printDocument.write(`
                <html>
                    <head>
                        <title>Print Statement</title>
                        ${headContent}
                        <style>
                            @page {
                                size: A4 landscape;
                                margin: 0;
                            }

                            body {
                                margin: 0;
                                padding: 0;
                                background: #fff;
                            }
                        </style>
                    </head>
                    <body>
                        ${clone.innerHTML} <!-- ✅ only outside <hr> removed -->
                    </body>
                </html>
            `);

            printDocument.close();

            // Print jab iframe load ho jaye
            printIframe.onload = () => {
                printIframe.contentWindow.focus();
                printIframe.contentWindow.print();
            };
        }
    </script>
@endsection
