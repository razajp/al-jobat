function createModal(data) {
    const statusColor = {
        active: ['[var(--bg-success)]', '[var(--h-bg-success)]', '[var(--border-success)]'],
        transparent: ['transparent', 'transparent', 'transparent'],
        no_Image: ['[var(--bg-warning)]', '[var(--h-bg-warning)]', '[var(--border-warning)]'],
        inactive: ['[var(--bg-error)]', '[var(--h-bg-error)]', '[var(--border-error)]'],
    };

    const contextMenu = document.getElementById('context-menu');
    if (contextMenu) {
        contextMenu.classList.add('fade-out');
        contextMenu.addEventListener('animationend', () => {
            contextMenu.remove()
        }, { once: true })
    };

    const modalWrapper = document.createElement('div');
    modalWrapper.id = `${data.id}-wrapper`;
    modalWrapper.className = `fixed inset-0 z-50 text-sm flex items-center justify-center bg-[var(--overlay-color)] fade-in`;

    let clutter = `
        <form id="${data.id}" method="${data.method ?? 'POST'}" action="${data.action}" enctype="multipart/form-data" class="w-full h-full flex flex-col space-y-4 relative items-center justify-center scale-in ${data.class}">
            <input type="hidden" name="_token" value="${document.querySelector('meta[name=\'csrf-token\']')?.content}">
            <div class="${data.class} ${data.preview ? 'bg-white text-black max-w-4xl h-[35rem] py-0' : 'bg-[var(--secondary-bg-color)]'} ${data.cards ? 'h-[40rem] max-w-6xl' : 'max-w-2xl'} rounded-2xl shadow-lg w-full p-6 flex relative">
                <div id="modal-close" onclick="closeModal('${data.id}')"
                    class="absolute top-0 -right-4 translate-x-full bg-[var(--secondary-bg-color)] rounded-2xl shadow-lg w-auto p-3 text-sm transition-all duration-300 ease-in-out hover:scale-[0.95] cursor-pointer">
                    <button type="button"
                        class="z-10 text-gray-400 hover:text-gray-600 hover:scale-[0.95] transition-all duration-300 ease-in-out cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                            class="w-6 h-6" style="display: inline">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="flex ${data.flex_col ? 'flex-col' : ''} w-full">
                    <div class="w-full h-full ${!data.table?.scrollable ? 'overflow-y-auto my-scrollbar-2' : ''}">
    `;

    if (data.user?.status || data.status) {
        const [bgColor, hoverBgColor, textColor] = statusColor[data.user?.status ?? data.status] || statusColor.inactive;
        if (data.image) {
            clutter += `
                <div id="active_inactive_dot_modal"
                    class="absolute top-3 left-3 w-[0.7rem] h-[0.7rem] bg-${textColor} rounded-full">
                </div>
            `;
        } else {
            clutter += `
                <div id="active_inactive_dot_modal"
                    class="absolute top-3 right-3 w-[0.7rem] h-[0.7rem] bg-${textColor} rounded-full">
                </div>
            `;
        }
    }
    
    clutter += `
        <div class="flex ${data.flex_col ? 'flex-col' : ''} items-start relative ${(data.class || '').includes('h-') ? 'h-full' : 'h-[15rem]'}">
    `;
    
    if (data.image) {
        clutter += `
                <div class="${!data.profile ? 'rounded-lg' : 'rounded-[41.5%]'} ${data.image && data.image == '/images/no_image_icon.png' ? 'scale-75' : ''} h-full aspect-square overflow-hidden">
                    <img id="imageInModal" src="${data.image}" alt=""
                        class="w-full h-full object-cover aspect-square">
                </div>
        `;
    }
    
    let detailsHTML = '';
    if (data.details && typeof data.details === 'object') {
        detailsHTML = Object.entries(data.details).map(([label, value]) => {
            // If it's an 'hr' entry (you can use any key like 'hr' or '--hr--')
            if (label === 'hr') {
                return `<hr class="w-full my-3 border-gray-600">`;
            }

            return `
                <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm capitalize">
                    <strong>${label}:</strong> <span style="opacity: 0.9">${value}</span>
                </p>
            `;
        }).join('');
    }

    if (data.name) {
        clutter += `
            <div class="flex-1 flex flex-col ${data.image ? 'ml-8' : ''} h-full w-full ${!data.table?.scrollable ? 'overflow-y-auto my-scrollbar-2' : ''}">
                <h5 id="name" class="text-2xl my-1 text-[var(--text-color)] capitalize font-semibold">${data.name}</h5>
                ${detailsHTML}
        `;
    }

    if (data.fields) {
        clutter += `
            <hr class="w-full my-3 border-gray-600">
            <div class="grid grid-cols-${data.fieldsGridCount} w-full gap-3 p-1">
        `;
        data.fields.forEach(field => {
            if (field.category == 'input') {
                if (field.type != 'hidden') {
                    let buttonHTML = '';
                    
                    if (field.btnId) {
                        buttonHTML = `
                            <button onclick="${field.onclick ?? ''}" id="${field.btnId ?? ''}" type="button" class="bg-[var(--primary-color)] px-4 rounded-lg hover:bg-[var(--h-primary-color)] transition-all duration-300 ease-in-out cursor-pointer text-lg font-bold disabled:opacity-50 disabled:cursor-not-allowed" disabled>+</button>
                        `;
                    }
                    
                    clutter += `
                        <div class="${field.grow ? 'grow' : ''} ${field.full ? 'col-span-full' : ''}">
                            <div class="form-group relative ${field.hidden ? 'hidden' : ''}">
                                <label for="${field.name ?? ''}" class="block font-medium text-[var(--secondary-text)] mb-2 ${!field.label ? 'hidden' : ''}">${field.label}</label>

                                <div class="relative flex gap-3">
                                    <input onkeydown="${field.enterToSubmitListener ? 'enterToSubmit(event)' : ''}" id="${field.id ?? ''}" type="${field.type ?? 'text'}" name="${field.name ?? ''}" value="${field.value ?? ''}" min="${field.min}" max="${field.max}" placeholder="${field.placeholder ?? ''}" ${field.required ? 'required' : ''} ${field.disabled ? 'disabled' : ''} ${field.readonly ? 'readonly' : ''} oninput="${field.oninput ?? ''}" onchange="${field.onchange ?? ''}" class="w-full rounded-lg bg-[var(--h-bg-color)] border-gray-600 text-[var(--text-color)] px-3 ${field.type == 'date' ? 'py-[7px]' : 'py-2'} border focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300 ease-in-out disabled:bg-transparent placeholder:capitalize">
                                    ${buttonHTML}
                                </div>
                            </div>

                            <div id="${field.name}-error" class="absolute -bottom-5 left-1 text-[var(--border-error)] text-xs mt-1 hidden transition-all duration-300 ease-in-out"></div>
                        </div>
                    `;
                    
                    if (field.focus) {
                        setTimeout(() => {
                            const input = document.getElementById(`${field.id}`);
                            if (input) input.focus();
                        }, 0);
                    }
                } else {
                    clutter += `
                        <input id="${field.id ?? ''}" type="hidden" name="${field.name ?? ''}" value="${field.value ?? ''}">
                    `;
                }
            } else if (field.category == 'select') {
                let buttonHTML = '';
                let optionsHTML = '<option value="">-- No options available --</option>';
                
                if (field.btnId) {
                    buttonHTML = `
                        <button onclick="${field.onclick ?? ''}" id="${field.btnId ?? ''}" type="button" class="bg-[var(--primary-color)] px-4 rounded-lg hover:bg-[var(--h-primary-color)] transition-all duration-300 ease-in-out cursor-pointer text-lg font-bold disabled:opacity-50 disabled:cursor-not-allowed" disabled>+</button>
                    `;
                }

                if (field.options && field.options.length > 0) {
                    optionsHTML = `<option value="">-- Select ${field.label} --</option>`;
                    
                    const rawOptions = field.options[0];
                    const optionsArray = Object.entries(rawOptions).map(([key, obj]) => {
                        return {
                            id: key,
                            text: obj.text,
                            data_option: obj.data_option || '{}'
                        };
                    });

                    optionsArray.forEach(option => {
                        console.log(option);
                        
                        optionsHTML += `
                            <option value="${option.id}" data-option='${JSON.stringify(option.data_option)}'>${option.text}</option>
                        `;
                    });
                }

                clutter += `
                    <div class="grow form-group">
                        <label for="${field.name ?? ''}" class="block font-medium text-[var(--secondary-text)] mb-2">${field.label} *</label>
                        
                        <div class="selectParent relative flex gap-3">
                            <select id="${field.id ?? ''}" name="${field.name ?? ''}" onchange="${field.onchange}" value="${field.value || ''}" class="w-full rounded-lg bg-[var(--h-bg-color)] border-gray-600 text-[var(--text-color)] px-3 py-2 border appearance-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300 ease-in-out disabled:bg-transparent" ${field.required ? 'required' : ''} ${field.disabled ? 'disabled' : ''} ${field.readonly ? 'readonly' : ''}>
                                ${optionsHTML}
                            </select>
                            ${buttonHTML}
                        </div>
                    </div>
                `;
            } else if (field.category == 'hr') {
                clutter += `
                    <div class="col-span-full">
                        <hr class="w-full border-gray-600">
                    </div>
                `;
            } else if (field.category == 'explicitHtml') {
                clutter += `
                    <div class="">
                        ${field.html}
                    </div>
                `;
            }
        });

        clutter += `
            </div>
        `;
    }

    if (data.imagePicker) {
        clutter += `
            <div>
                <hr class="w-full my-3 border-gray-600">

                <div class="grid grid-cols-1 md:grid-cols-1">
                    <label for="${data.imagePicker.name}"
                        class="border-dashed border-2 border-gray-300 rounded-lg p-6 flex flex-col items-center justify-center cursor-pointer hover:border-primary transition-all duration-300 ease-in-out relative">
                        <input id="${data.imagePicker.id}" type="file" name="${data.imagePicker.name}" accept="image/*"
                            class="image_upload opacity-0 absolute inset-0 cursor-pointer"
                            onchange="previewImage(event)" />
                        <div id="image_preview_${data.imagePicker.id}" class="flex flex-col items-center max-w-[50%]">
                            <img src="${data.imagePicker.placeholder}" alt="Upload Icon"
                                class="placeholder_icon w-auto h-full mb-2 rounded-md" id="placeholder_icon_${data.imagePicker.id}" />
                            <p id="upload_text_${data.imagePicker.id}" class="upload_text text-md text-gray-500">${data.imagePicker.uploadText}</p>
                        </div>
                    </label>
                </div>
            </div>
        `;
    }

    if (data.cards) {
        let cardsHTML = '';
        if (data.cards.data.length > 0) {
            data.cards.data.forEach(item => {
                cardsHTML += createCard(item)
            });
        } else {
            cardsHTML= `
                <div class="col-span-full text-center text-[var(--border-error)] text-md mt-4">No ${data.cards.name} yet</div>
            `;
        }

        clutter += `
            <div class="flex-1 flex flex-col ${data.image ? 'ml-8' : ''} h-full w-full overflow-y-auto my-scrollbar-2">
                <h5 id="name" class="text-2xl text-[var(--text-color)] capitalize font-semibold">${data.cards.name}</h5>
                <hr class="w-full my-3 border-gray-600">
                <div class="grid grid-cols-${data.cards.count} w-full gap-3 text-sm">
                    ${cardsHTML}
                </div>
            </div>
        `;
    }

    if (data.table) {
        let headerHTML = '';
        let bodyHTML = '';

        data.table.headers.forEach(header => {
            headerHTML += `<div class="${header.class}">${header.label}</div>`;
        });

        if (data.table.body?.length > 0) {
            data.table.body.forEach(data => {
                const rowHTML = data.map(item => {
                    let checkboxHTML = '';
                    let inputHTML = '';

                    if (item.input) {
                        inputHTML = `
                            <input class="${item.input.class || ''} w-[70%] border border-gray-600 bg-[var(--h-bg-color)] py-0.5 px-2 rounded-md text-xs focus:outline-none opacity-0 pointer-events-none" type="${item.input.type || 'text'}" name="${item.input.name || ''}" value="${item.input.value || ''}" min="${item.input.min || ''}" oninput="${item.input.oninput || ''}" onclick="${item.input.onclick || ''}" />
                        `;
                    }

                    if (item.checkbox) {
                        checkboxHTML = `
                            <input ${item.checked ? 'checked' : ''} type="checkbox" name="selected_customers[]"
                                class="row-checkbox mr-2 shrink-0 w-3.5 h-3.5 appearance-none border border-gray-400 rounded-sm checked:bg-[var(--primary-color)] checked:border-transparent focus:outline-none transition duration-150 cursor-pointer" />
                        `;
                    }

                    if (item.checkbox || item.input) {
                        return `
                            <div class="${item.class}">
                                ${checkboxHTML}
                                ${inputHTML}
                            </div>
                        `;
                    } else {
                        return `<div class="${item.class}">${item.data}</div>`;
                    }
               }).join('');
                bodyHTML += `
                    <div id='${data[0].jsonData?.id}' ${data[0].jsonData ? `data-json='${JSON.stringify(data[0].jsonData)}'` : ''} data class="flex justify-between items-center border-t border-gray-600 py-2 px-4 ${data[0].checkbox ? 'cursor-pointer row-toggle select-none customer-row hover:bg-[var(--h-secondary-bg-color)] transition-all fade-in ease-in-out' : ''}" ${data[0].checkbox ? 'onclick="console.log(this)"' : ''}>
                        ${rowHTML}
                    </div>
                `;
            });
        } else {
            bodyHTML += `
                <div class="flex justify-between items-center border-t border-gray-600 py-2 px-4">
                    <div class="grow text-center text-[var(--border-error)]">No ${data.table.name} yet.</div>
                </div>
            `;
        }

        clutter += `
            <hr class="w-full my-3 border-gray-600">
            <div class="w-full ${data.table.scrollable ? 'h-[80.5%] overflow-hidden' : 'h-auto'} text-left text-sm">
                <div class="flex justify-between items-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4 mb-3">
                    ${headerHTML}
                </div>
                <div id="table-body" class="overflow-y-auto my-scrollbar-2 h-full">
                    ${bodyHTML}
                </div>
            </div>
        `;
    }

    if (data.calcBottom && data.calcBottom.length > 0) {
        let calcBottomClass = '';
        let fieldsHTML = '';
        const childCount = data.calcBottom.length;

        if (childCount === 1 || childCount === 3) {
            calcBottomClass = 'flex';
        } else if (childCount === 2 || childCount === 4) {
            calcBottomClass = 'grid grid-cols-2';
        } else if (childCount === 6) {
            calcBottomClass = 'grid grid-cols-3';
        }

        data.calcBottom.forEach(field => {
            fieldsHTML += `
                <div class="final flex justify-between items-center bg-[var(--h-bg-color)] border border-gray-600 rounded-lg py-2 px-4 w-full ${field.disabled ? 'cursor-not-allowed' : ''}">
                    <label for="${field.name}" class="text-nowrap grow">${field.label}</label>
                    <input type="text" required name="${field.name}" id="${field.name}" max="${field.max}" value="${field.value}" ${field.disabled ? 'disabled' : ''} class="text-right bg-transparent outline-none border-none w-[50%]" />
                </div>
            `;
        });

        clutter += `
            <div class="w-full">
                <hr class="w-full my-3 border-gray-600">
                <div id="calc-bottom" class="${calcBottomClass} w-full gap-3 text-sm">
                    ${fieldsHTML}
                </div>
            </div>
        `;
    }

    if (data.chips) {
        clutter += `
            <hr class="w-full my-3 border-gray-600">
            <div id="chipsContainer" class="w-full flex flex-wrap gap-2 overflow-y-auto my-scrollbar-2 text-[var(--text-color)]">
        `;

        let removeBtn = `
            <button class="delete cursor-pointer ${data.chips.length <= 1 ? 'hidden' : ''} transition-all 0.3s ease-in-out" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                class="size-3 stroke-gray-400">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        `;

        data.chips.forEach(chip => {
           clutter += `
                <div data-id="${chip.id}" class="chip border border-gray-600 text-xs rounded-xl py-2 px-4 inline-flex items-center gap-2 transition-all 0.3s ease-in-out">
                    <div class="text tracking-wide">${chip.title}</div>
                    ${data.editableChips ? removeBtn : ''}
                </div>
           `; 
        });

        clutter += `
            </div>
        `;
    }

    if (data.name) {
        clutter += '</div>';
    }

    if (data.preview) {
        let previewData = data.preview.data;
        let cottonCount = previewData.cotton_count || 0;
        let totalAmount = 0;
        let totalQuantity = 0;
        let discount = previewData.discount || previewData.shipment?.discount;
        let previousBalance = previewData.previous_balance || 0;
        let netAmount = previewData.netAmount || previewData.shipment?.netAmount;
        let currentBalance = previewData.current_balance;

        let invoiceTableHeader = "";
        let invoiceTableBody = "";
        let invoiceBottom = "";

        if (data.preview.type == "voucher") {
            invoiceTableHeader = `
                <div class="th text-sm font-medium w-[7%]">S.No</div>
                <div class="th text-sm font-medium w-[11%]">Method</div>
                <div class="th text-sm font-medium w-1/5">Customer</div>
                <div class="th text-sm font-medium w-1/4">Account</div>
                <div class="th text-sm font-medium w-[17%]">Date</div>
                <div class="th text-sm font-medium w-[11%]">Reff. No.</div>
                <div class="th text-sm font-medium w-[10%]">Amount</div>
            `;

            invoiceTableBody = `
                ${previewData.supplier_payments.map((payment, index) => {
                console.log('hello', payment);

                const hrClass = index === 0 ? "mb-2.5" : "my-2.5";
                return `
                <div>
                    <hr class="w-full ${hrClass} border-gray-600">
                    <div class="tr flex justify-between w-full px-4">
                        <div class="td text-sm font-semibold w-[7%]">${index + 1}.</div>
                        <div class="td text-sm font-semibold w-[11%] capitalize">${payment.method ?? '-'}</div>
                        <div class="td text-sm font-semibold w-1/5">${payment.program?.customer.customer_name ?? '-'}</div>
                        <div class="td text-sm font-semibold w-1/4">${(payment.bank_account?.account_title?.split('|')[0] ?? '-') + ' | ' +
                            (payment.bank_account?.bank.short_title ?? '-')}</div>
                        <div class="td text-sm font-semibold w-[17%]">${formatDate(payment.date) ?? '-'}</div>
                        <div class="td text-sm font-semibold w-[11%]">${payment.cheque?.cheque_no ?? payment.cheque_no ?? payment.reff_no ?? payment.slip?.slip_no ??
                            payment.transaction_id ?? '-'}</div>
                        <div class="td text-sm font-semibold w-[10%]">${formatNumbersWithDigits(payment.amount, 1, 1) ?? '-'}
                        </div>
                    </div>
                </div>
                `;
                }).join('')}
            `;

            invoiceBottom = `
                <div class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                    <div class="text-nowrap">Previous Balance - Rs</div>
                    <div class="w-1/4 text-right grow">${formatNumbersWithDigits(previewData.previous_balance, 1, 1)}</div>
                </div>
                <div class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                    <div class="text-nowrap">Total Payment - Rs</div>
                    <div class="w-1/4 text-right grow">${formatNumbersWithDigits(previewData.total_payment, 1, 1)}</div>
                </div>
                <div class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full">
                    <div class="text-nowrap">Current Balance - Rs</div>
                    <div class="w-1/4 text-right grow">${formatNumbersWithDigits(previewData.previous_balance - previewData.total_payment, 1, 1)}</div>
                </div>
            `;
        } else if (data.preview.type == "cargo_list") {
            invoiceTableHeader = `
                <div class="th text-sm font-medium w-[7%]">S.No</div>
                <div class="th text-sm font-medium w-1/5">Date</div>
                <div class="th text-sm font-medium w-1/6">Invoice No.</div>
                <div class="th text-sm font-medium w-1/6">Cotton</div>
                <div class="th text-sm font-medium grow">Customer</div>
                <div class="th text-sm font-medium w-[12%]">City</div>
            `;

            invoiceTableBody = `
                ${previewData.invoices.map((invoice, index) => {
                    const hrClass = index === 0 ? "mb-2.5" : "my-2.5";

                    return `
                        <div>
                            <hr class="w-full ${hrClass} border-black">
                            <div class="tr flex justify-between w-full px-4">
                                <div class="td text-sm font-semibold w-[7%]">${index + 1}.</div>
                                <div class="td text-sm font-semibold w-1/5">${formatDate(invoice.date)}</div>
                                <div class="td text-sm font-semibold w-1/6">${invoice.invoice_no}</div>
                                <div class="td text-sm font-semibold w-1/6">${invoice.cotton_count}</div>
                                <div class="td text-sm font-semibold grow capitalize">${invoice.customer.customer_name}</div>
                                <div class="td text-sm font-semibold w-[12%]">${invoice.customer.city.title}</div>
                            </div>
                        </div>
                    `;
                }).join('')}
            `;
        } else {
            invoiceTableHeader = `
                <div class="th text-sm font-medium ">S.No</div>
                <div class="th text-sm font-medium ">Article</div>
                <div class="th text-sm font-medium col-span-2">Description</div>
                <div class="th text-sm font-medium ">Pcs.</div>
                <div class="th text-sm font-medium ">Packets</div>
                ${data.preview.type == 'invoice' ? '<div class="th text-sm font-medium ">Unit</div>' : ''}
                <div class="th text-sm font-medium ">Rate/Pc.</div>
                <div class="th text-sm font-medium ">Amount</div>
                ${data.preview.type == 'order' ? '<div class="th text-sm font-medium ">Dispatch</div>' : ''}
            `;

            invoiceTableBody = `
                ${previewData.articles.map((orderedArticle, index) => {
                    const article = orderedArticle.article;
                    const salesRate = article.sales_rate;
                    const orderedQuantity = orderedArticle.ordered_quantity;
                    const invoiceQuantity = orderedArticle.invoice_quantity;
                    const shipmentQuantity = orderedArticle.shipment_quantity;
                    const total = parseInt(salesRate) * (orderedQuantity || invoiceQuantity || shipmentQuantity);
                    const hrClass = index === 0 ? "mb-2.5" : "my-2.5";

                    totalAmount += total;
                    totalQuantity += orderedQuantity || invoiceQuantity || shipmentQuantity;

                    return `
                        <div>
                            <hr class="w-full ${hrClass} border-black">
                            <div class="tr grid grid-cols-${data.preview.type == 'shipment' ? '8' : '9'} justify-between w-full px-4">
                                <div class="td text-sm font-semibold ">${index + 1}.</div>
                                <div class="td text-sm font-semibold ">${article.article_no}</div>
                                <div class="td text-sm font-semibold col-span-2">${orderedArticle.description}</div>
                                <div class="td text-sm font-semibold ">${orderedQuantity || invoiceQuantity || shipmentQuantity}</div>
                                <div class="td text-sm font-semibold ">${article?.pcs_per_packet ? Math.floor((orderedQuantity || invoiceQuantity || shipmentQuantity) / article.pcs_per_packet) : 0}</div>
                                ${data.preview.type == 'invoice' ? '<div class="td text-sm font-semibold "> ' + article?.pcs_per_packet + ' </div>' : ''}
                                <div class="td text-sm font-semibold ">${formatNumbersWithDigits(salesRate, 1, 1)}</div>
                                <div class="td text-sm font-semibold ">${formatNumbersWithDigits(total, 1, 1)}</div>
                                ${data.preview.type == 'order' ? '<div class="td text-sm font-semibold "></div>' : ''}
                            </div>
                        </div>
                    `;
                }).join('')}
            `;

            invoiceBottom = `
                <div class="total flex justify-between items-center border border-black rounded-lg py-1.5 px-4 w-full">
                    <div class="text-nowrap">Total Quantity - Pcs</div>
                    <div class="w-1/4 text-right grow">${formatNumbersDigitLess(totalQuantity)}</div>
                </div>
                <div class="total flex justify-between items-center border border-black rounded-lg py-1.5 px-4 w-full">
                    <div class="text-nowrap">Total Amount</div>
                    <div class="w-1/4 text-right grow">${formatNumbersWithDigits(totalAmount, 1, 1)}</div>
                </div>
                <div class="total flex justify-between items-center border border-black rounded-lg py-1.5 px-4 w-full">
                    <div class="text-nowrap">Discount - %</div>
                    <div class="w-1/4 text-right grow">${discount}</div>
                </div>
                ${data.preview.type == 'order' ? `
                    <div class="total flex justify-between items-center border border-black rounded-lg py-1.5 px-4 w-full">
                        <div class="text-nowrap">Previous Balance</div>
                        <div class="w-1/4 text-right grow">${formatNumbersWithDigits(previousBalance, 1, 1)}</div>
                    </div>
                ` : ''}
                <div
                    class="total flex justify-between items-center border border-black rounded-lg py-1.5 px-4 w-full">
                    <div class="text-nowrap">Net Amount</div>
                    <div class="w-1/4 text-right grow">${formatNumbersWithDigits(netAmount, 1, 1)}</div>
                </div>
                ${data.preview.type == 'order' ? `
                    <div
                        class="total flex justify-between items-center border border-black rounded-lg py-1.5 px-4 w-full">
                        <div class="text-nowrap">Current Balance</div>
                        <div class="w-1/4 text-right grow">${formatNumbersWithDigits(currentBalance, 1,1)}</div>
                    </div>
                ` : ''}
            `;
        }
        
        clutter += `
            <div id="preview-container" class="w-[210mm] h-[297mm] mx-auto relative overflow-y-auto my-scrollbar-2">
                <div id="preview" class="preview flex flex-col h-full py-6">
                    <div class="flex flex-col h-full">
                        <div id="banner" class="banner w-full flex justify-between items-center mt-8 px-5">
                            <div class="left">
                                <div class="logo">
                                    <img src="images/${companyData.logo}" alt="Track Point"
                                        class="w-[12rem]" />
                                    <div class='mt-1'>${ companyData.phone_number }</div>
                                </div>
                            </div>
                            <div class="right">
                                <div class="logo text-right">
                                    <h1 class="text-2xl font-medium text-[var(--h-primary-color)]">${data.preview.document}</h1>
                                    <div class="mt-1 text-right ${cottonCount == 0 ? 'hidden' : ''}">Cotton: ${cottonCount}</div>
                                    ${previewData.shipment_no ? '<div class="mt-1 text-right">Shipment No.: ' + previewData.shipment_no + ' </div>' : ''}
                                    ${previewData.order_no ? '<div class="mt-1 text-right">Order No.: ' + previewData.order_no + ' </div>' : ''}
                                </div>
                            </div>
                        </div>
                        <hr class="w-full my-3 border-black">
                        <div id="header" class="header w-full flex justify-between px-5">
                            <div class="left w-50 space-y-1">
                                ${data.preview.type == "order" || data.preview.type == "invoice" ? `
                                    <div class="customer text-lg leading-none capitalize">M/s: ${previewData.customer.customer_name}</div>
                                    <div class="person text-md text-lg leading-none">${previewData.customer.urdu_title}</div>
                                    <div class="address text-md leading-none">${previewData.customer.address}, ${previewData.customer.city.title}</div>
                                    <div class="phone text-md leading-none">${previewData.customer.phone_number}</div>
                                ` : `
                                    <div class="date leading-none">Date: ${formatDate(previewData.date)}</div>
                                    <div class="number leading-none capitalize">${data.preview.type.replace('_', ' ')} No.: ${data.preview.type == 'shipment' ? previewData.shipment_no : data.preview.type == 'voucher' ? previewData.voucher_no : data.preview.type == 'cargo_list' ? previewData.cargo_no : ''}</div>
                                `}
                            </div>
                            ${data.preview.type == 'voucher' || data.preview.type == 'cargo_list' ? `
                                <div class="center my-auto ">
                                    <div class="supplier-name capitalize font-semibold text-md">Supplier Name: ${previewData.supplier?.supplier_name || previewData.cargo_name}</div>
                                </div>
                            ` : ''}
                            <div class="right w-50 my-auto text-right text-sm text-black space-y-1.5">
                                ${data.preview.type == "order" || data.preview.type == "invoice" ? `
                                    <div class="date leading-none">Date: ${formatDate(previewData.date)}</div>
                                    <div class="number leading-none capitalize">${data.preview.type} No.: ${data.preview.type == 'order' ? previewData.order_no : data.preview.type == 'invoice' ? previewData.invoice_no : ''}</div>
                                ` : '' }
                                <div class="preview-copy leading-none capitalize">${data.preview.type.replace('_', ' ')} Copy: ${data.preview.type == 'shipment' ? 'Staff' : data.preview.type == 'voucher' ? 'Supplier' : data.preview.type == 'cargo_list' ? 'Cargo' : 'Customer'}</div>
                                <div class="copy leading-none">Document: ${data.preview.document}</div>
                            </div>
                        </div>
                        <hr class="w-full my-3 border-black">
                        <div class="body w-full px-5 grow mx-auto">
                            <div class="table w-full">
                                <div class="table w-full border border-black rounded-lg pb-2.5 overflow-hidden">
                                    <div class="thead w-full">
                                        <div class="tr ${data.preview.type == 'voucher' || data.preview.type == 'cargo_list' ? 'flex justify-between' : 'grid'} ${data.preview.type == 'shipment' ? 'grid-cols-8' : 'grid-cols-9'} w-full px-4 py-1.5 bg-[var(--primary-color)] text-white">
                                            ${invoiceTableHeader}
                                        </div>
                                    </div>
                                    <div id="tbody" class="tbody w-full">
                                        ${invoiceTableBody}
                                    </div>
                                </div>
                            </div>
                        </div>
                        ${invoiceBottom != '' ? `<hr class="w-full my-3 border-black">` : ''}
                        <div class="grid ${data.preview.type == 'order' || data.preview.type == 'voucher' ? 'grid-cols-3' : 'grid-cols-2'} gap-2 px-5">
                            ${invoiceBottom}
                        </div>
                        <hr class="w-full my-3 border-black">
                        <div class="tfooter flex w-full text-sm px-5 justify-between mb-4 text-black">
                            <P class="leading-none">${ companyData.name } | ${ companyData.address }</P>
                            <p class="leading-none text-sm">&copy; 2025 Spark Pair | +92 316 5825495</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
        
    clutter += `
                    </div>
                </div>
            </div>
        </div>

        <div id="modal-action"
            class="bg-[var(--secondary-bg-color)] rounded-2xl shadow-lg max-w-3xl w-auto p-3 relative text-sm">
            <div class="flex gap-3">
                <button onclick="closeModal('${data.id}')" type="button"
                    class="px-4 py-2 bg-[var(--secondary-bg-color)] border border-gray-600 text-[var(--secondary-text)] rounded-lg hover:bg-[var(--h-bg-color)] transition-all duration-300 ease-in-out cursor-pointer hover:scale-[0.95]">
                    Cancel
                </button>
    `;
    
    if (data.bottomActions) {
        data.bottomActions.forEach(action => {
            if (action.id.includes('edit')) {
                clutter += `
                    <a id="${action.id}-in-modal" href="${window.location.pathname}/${action.dataId}/edit"
                        class="px-4 py-2 bg-[var(--secondary-bg-color)] border border-gray-600 text-[var(--secondary-text)] rounded-lg hover:bg-[var(--h-bg-color)] transition-all duration-300 ease-in-out cursor-pointer hover:scale-[0.95]">
                        ${action.text}
                    </a>
                `;
            } else {
                clutter += `
                    <button id="${action.id}-in-modal" type="${action.type ?? 'button'}" onclick='${action.onclick}'
                        class="px-4 py-2 bg-${action.id.includes('add') ? '[var(--bg-success)]' : '[var(--secondary-bg-color)]'} border hover:border-${action.id.includes('add') ? '[var(--border-success)] border-[var(--bg-success)]' : 'gray-600 border-gray-600'} text-${action.id.includes('add') ? '[var(--border-success)]' : '[var(--secondary-text)]'} rounded-lg hover:bg-${action.id.includes('add') ? '[var(--h-bg-success)]' : '[var(--h-bg-color)]'} transition-all duration-300 ease-in-out cursor-pointer hover:scale-[0.95]">
                        ${action.text}
                    </button>
                `;
            }
        });
    }
    
    if ((data.details && data.details['Balance'] == 0.0) || data.forceStatusBtn) {
        console.log(data.status);
        if (data.user?.status || data.status) {
            let status = data.user?.status ?? data.status;
            const [bgColor, hoverBgColor, textColor] = statusColor[status == 'active' ? status = 'in_active' : status = 'active'] || statusColor.inactive;
            clutter += `
                <div id="ac_in_modal">
                    <input type="hidden" id="user_id" name="user_id" value="${data.user?.id ?? data.uId}">
                    <input type="hidden" id="user_status" name="status" value="${data.user?.status ?? data.status}">
                    <button id="ac_in_btn" type="submit"
                        class="px-4 py-2 bg-${bgColor} border border-${bgColor} text-${textColor} font-semibold rounded-lg hover:bg-${hoverBgColor} transition-all duration-300 ease-in-out cursor-pointer hover:scale-[0.95] capitalize">
                        ${status.replace('_', ' ')}
                    </button>
                </div>
            `;
        }
    }

    clutter += `
                </div>
            </div>
        </form>
    `;
    modalWrapper.innerHTML = clutter;

    closeOnClickOutside = (e) => {
        const clickedId = e.target.id;
        if (clickedId === `${data.id}-wrapper` || clickedId === `${data.id}`) {
            const modal = document.getElementById(`${data.id}`);
            const modalWrapper = document.getElementById(`${data.id}-wrapper`);

            modal.classList.add('scale-out');
            modal.addEventListener('animationend', () => {
                modalWrapper.classList.add('fade-out');
                modalWrapper.addEventListener('animationend', () => {
                    modalWrapper.remove();
                }, { once: true });
            }, { once: true });
            document.removeEventListener('mousedown', closeOnClickOutside);
            document.removeEventListener('keydown', escToClose);
            document.removeEventListener('keydown', enterToSubmit);
        }
    };
    document.addEventListener('mousedown', closeOnClickOutside);

    // ✅ Escape Key to Close
    escToClose = (e) => {
        if (e.key === 'Escape') {
            const form = modalWrapper.querySelector('form');
            form.classList.add('scale-out');
            form.addEventListener('animationend', () => {
                modalWrapper.classList.add('fade-out');
                modalWrapper.addEventListener('animationend', () => {
                    modalWrapper.remove();
                }, { once: true });
            }, { once: true });

            // Optionally: remove these listeners after first use
            document.removeEventListener('mousedown', closeOnClickOutside);
            document.removeEventListener('keydown', escToClose);
            document.removeEventListener('keydown', enterToSubmit);
        }
    };

    // ✅ enter Key to subbmit
    enterToSubmit = (e) => {
        if (e.key === 'Enter') {
            const form = modalWrapper.querySelector('form');
            const btn = form.querySelector('#modal-action button[id*="add"], #modal-action button[id*="update"]');
            if (btn) {
                btn.click();
            }
        }
    };

    document.addEventListener('keydown', escToClose);
    if (data.defaultListener !== false) {
        document.addEventListener('keydown', enterToSubmit);
    }
    document.body.appendChild(modalWrapper);
}