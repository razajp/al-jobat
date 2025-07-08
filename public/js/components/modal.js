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
            <div class="${data.class} ${data.preview ? 'bg-white text-black max-w-4xl h-[35rem]' : 'bg-[var(--secondary-bg-color)]'} ${data.cards ? 'h-[40rem] max-w-6xl' : 'max-w-2xl'} rounded-2xl shadow-lg w-full p-6 flex relative">
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
                
                <div class="flex flex-col w-full">
                    <div class="w-full h-full overflow-y-auto my-scrollbar-2">
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
        <div class="flex items-start relative ${(data.class || '').includes('h-') ? 'h-full' : 'h-[15rem]'}">
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
            <div class="flex-1 flex flex-col ${data.image ? 'ml-8' : ''} h-full overflow-y-auto my-scrollbar-2">
                <h5 id="name" class="text-2xl my-1 text-[var(--text-color)] capitalize font-semibold">${data.name}</h5>
                ${detailsHTML}
        `;
    }

    if (data.fields) {
        clutter += `
            <hr class="w-full my-3 border-gray-600">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 p-1">
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
                                    <input id="${field.id ?? ''}" type="${field.type ?? 'text'}" name="${field.name ?? ''}" value="${field.value ?? ''}" placeholder="${field.placeholder ?? ''}" ${field.required ? 'required' : ''} ${field.disabled ? 'disabled' : ''} ${field.readonly ? 'readonly' : ''} oninput="${field.oninput ?? ''}" onchange="${field.onchange ?? ''}" class="w-full rounded-lg bg-[var(--h-bg-color)] border-gray-600 text-[var(--text-color)] px-3 ${field.type == 'date' ? 'py-[7px]' : 'py-2'} border focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300 ease-in-out disabled:bg-transparent placeholder:capitalize">
                                    ${buttonHTML}
                                </div>
                            </div>
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
                            text: obj.text
                        };
                    });

                    optionsArray.forEach(option => {
                        optionsHTML += `
                            <option value="${option.id}">${option.text}</option>
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
            }
        });

        clutter += `
            </div>
        `;
    }

    if (data.imagePicker) {
        clutter += `
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
            <div class="flex-1 flex flex-col ${data.image ? 'ml-8' : ''} h-full overflow-y-auto my-scrollbar-2">
                <h5 id="name" class="text-2xl my-1 text-[var(--text-color)] capitalize font-semibold">${data.cards.name}</h5>
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
            data.table.body.forEach((data, index) => {
                bodyHTML += `
                    <div class="flex justify-between items-center border-t border-gray-600 py-2 px-4">
                        <div class="w-1/5">${index + 1}</div>
                        <div class="grow ml-5">${data.title}</div>
                        <div class="w-1/4">${formatNumbersWithDigits(data.rate, 2, 2)}</div>
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
            <div class="w-full text-left grow text-sm">
                <div class="flex justify-between items-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4 mb-3">
                    ${headerHTML}
                </div>
                <div id="table-body" class="overflow-y-auto my-scrollbar-2">
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
            calcBottomClass = 'grid', 'grid-cols-2';
        } else if (childCount === 6) {
            calcBottomClass = 'grid', 'grid-cols-3';
        }

        data.calcBottom.forEach(field => {
            fieldsHTML += `
                <div class="final flex justify-between items-center bg-[var(--h-bg-color)] border border-gray-600 rounded-lg py-2 px-4 w-full ${field.disabled ? 'cursor-not-allowed' : ''}">
                    <label for="${field.name}" class="text-nowrap grow">${field.label}</label>
                    <input type="text" required name="${field.name}" id="${field.name}" value="${field.value}" ${field.disabled ? 'disabled' : ''} class="text-right bg-transparent outline-none border-none w-[50%]" />
                </div>
            `;
        });

        clutter += `
            <hr class="w-full my-3 border-gray-600">
            <div id="calc-bottom" class="${calcBottomClass} w-full gap-3 text-sm">
                ${fieldsHTML}
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

        console.log(previewData);
        
        clutter += `
            <div id="preview-container" class="w-[210mm] h-[297mm] mx-auto relative overflow-y-auto my-scrollbar-2">
                <div id="preview" class="preview flex flex-col h-full">
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
                                <div class="customer text-lg leading-none capitalize">M/s: ${previewData.customer.customer_name}</div>
                                <div class="person text-md text-lg leading-none">${previewData.customer.urdu_title}</div>
                                <div class="address text-md leading-none">${previewData.customer.address}, ${previewData.customer.city.title}</div>
                                <div class="phone text-md leading-none">${previewData.customer.phone_number}</div>
                            </div>
                            <div class="right w-50 my-auto text-right text-sm text-black space-y-1.5">
                                <div class="date leading-none">Date: ${formatDate(previewData.date)}</div>
                                <div class="number leading-none capitalize">${data.preview.type} No.: ${data.preview.type == 'order' ? previewData.order_no : data.preview.type == 'invoice' ? previewData.invoice_no : ''}</div>
                                <div class="preview-copy leading-none capitalize">${data.preview.type} Copy: Customer</div>
                                <div class="copy leading-none">Document: ${data.preview.document}</div>
                            </div>
                        </div>
                        <hr class="w-full my-3 border-black">
                        <div class="body w-[95%] grow mx-auto">
                            <div class="table w-full">
                                <div class="table w-full border border-black rounded-lg pb-2.5 overflow-hidden">
                                    <div class="thead w-full">
                                        <div class="tr grid grid-cols-9 justify-between w-full px-4 py-1.5 bg-[var(--primary-color)] text-white">
                                            <div class="th text-sm font-medium ">S.No</div>
                                            <div class="th text-sm font-medium ">Article</div>
                                            <div class="th text-sm font-medium col-span-2">Description</div>
                                            <div class="th text-sm font-medium ">Pcs.</div>
                                            <div class="th text-sm font-medium ">Packets</div>
                                            ${data.preview.type == 'invoice' ? '<div class="th text-sm font-medium ">Unit</div>' : ''}
                                            <div class="th text-sm font-medium ">Rate/Pc.</div>
                                            <div class="th text-sm font-medium ">Amount</div>
                                            ${data.preview.type == 'order' ? '<div class="th text-sm font-medium ">Dispatch</div>' : ''}
                                        </div>
                                    </div>
                                    <div id="tbody" class="tbody w-full">
                                        ${previewData.articles.map((orderedArticle, index) => {
                                            const article = orderedArticle.article;
                                            const salesRate = article.sales_rate;
                                            const orderedQuantity = orderedArticle.ordered_quantity;
                                            const invoiceQuantity = orderedArticle.invoice_quantity;
                                            const total = parseInt(salesRate) * (orderedQuantity || invoiceQuantity);
                                            const hrClass = index === 0 ? "mb-2.5" : "my-2.5";

                                            totalAmount += total;
                                            totalQuantity += orderedQuantity || invoiceQuantity;

                                            return `
                                                <div>
                                                    <hr class="w-full ${hrClass} border-black">
                                                    <div class="tr grid grid-cols-9 justify-between w-full px-4">
                                                        <div class="td text-sm font-semibold ">${index + 1}.</div>
                                                        <div class="td text-sm font-semibold ">${article.article_no}</div>
                                                        <div class="td text-sm font-semibold col-span-2">${orderedArticle.description}</div>
                                                        <div class="td text-sm font-semibold ">${orderedQuantity || invoiceQuantity}</div>
                                                        <div class="td text-sm font-semibold ">${article?.pcs_per_packet ? Math.floor((orderedQuantity || invoiceQuantity) / article.pcs_per_packet) : 0}</div>
                                                        ${data.preview.type == 'invoice' ? '<div class="td text-sm font-semibold "> ' + article?.pcs_per_packet + ' </div>' : ''}
                                                        <div class="td text-sm font-semibold ">${formatNumbersWithDigits(salesRate, 1, 1)}</div>
                                                        <div class="td text-sm font-semibold ">${formatNumbersWithDigits(total, 1, 1)}</div>
                                                        ${data.preview.type == 'order' ? '<div class="td text-sm font-semibold "></div>' : ''}
                                                    </div>
                                                </div>
                                            `;
                                        }).join('')}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr class="w-full my-3 border-black">
                        <div class="grid ${data.preview.type == 'order' ? 'grid-cols-3' : 'grid-cols-2'} gap-2">
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


    if (data.details && data.details['Balance'] && data.details['Balance'] == 0.0) {
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

    const closeOnClickOutside = (e) => {
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
        }
    };
    document.addEventListener('mousedown', closeOnClickOutside);

    // ✅ Escape Key to Close
    const escToClose = (e) => {
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
    const enterToSubmit = (e) => {
        if (e.key === 'Enter') {
            const form = modalWrapper.querySelector('form');
            const btn = form.querySelector('#modal-action button[id*="add"], #modal-action button[id*="update"]');
            if (btn) {
                btn.click();
            }
        }
    };

    document.addEventListener('keydown', escToClose);
    document.addEventListener('keydown', enterToSubmit);
    document.body.appendChild(modalWrapper);
}