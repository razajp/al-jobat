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
            <div class="${data.class} bg-[var(--secondary-bg-color)] rounded-2xl shadow-lg w-full max-w-2xl p-6 flex relative">
                <div id="modal-close" onclick="closeModal('${data.id}')"
                    class="absolute top-0 -right-4 translate-x-full bg-[var(--secondary-bg-color)] rounded-2xl shadow-lg w-auto p-3 text-sm transition-all duration-300 ease-in-out hover:scale-[0.95] cursor-pointer">
                    <button type="button"
                        class="z-10 text-gray-400 hover:text-gray-600 hover:scale-[0.95] transition-all duration-300 ease-in-out">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                            class="w-6 h-6" style="display: inline">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="flex flex-col w-full">
                    <div class="w-full h-full">
    `;

    if (data.user?.status || data.status) {
        const [bgColor, hoverBgColor, textColor] = statusColor[data.user?.status ?? data.status] || statusColor.inactive;
        clutter += `
            <div id="active_inactive_dot_modal"
                class="absolute top-3 left-3 w-[0.7rem] h-[0.7rem] bg-${textColor} rounded-full">
            </div>
        `;
    }
    
    clutter += `
        <div class="flex items-start relative h-[15rem]">
    `;
    
    if (data.image) {
        clutter += `
                <div class="${!data.profile ? 'rounded-lg' : 'rounded-[41.5%]'} h-full aspect-square overflow-hidden">
                    <img id="imageInModal" src="${data.image}" alt=""
                        class="w-full h-full object-cover">
                </div>
        `;
    }
    
    let detailsHTML = '';
    if (data.details && typeof data.details === 'object') {
        detailsHTML = Object.entries(data.details).map(([label, value]) => {
            return `
            <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm capitalize">
                <strong>${label}:</strong> <span style="opacity: 0.9">${value}</span>
            </p>
        `;
        }).join('');
    }

    clutter += `
        <div class="flex-1 ${data.image ? 'ml-8' : ''} h-full overflow-y-auto my-scrollbar-2">
            <h5 id="name" class="text-2xl my-1 text-[var(--text-color)] capitalize font-semibold">${data.name}</h5>
            ${detailsHTML}
    `;

    if (data.fields) {
        clutter += `
            <hr class="w-full my-3 border-gray-600">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        `;
        data.fields.forEach(field => {
            if (field.category == 'input') {
                if (field.type != 'hidden') {
                    clutter += `
                        <div class="form-group relative">
                            <label for="${field.name ?? ''}" class="block font-medium text-[var(--secondary-text)] mb-2">${field.label}</label>

                            <div class="relative flex gap-4">
                                <input id="${field.id ?? ''}" type="${field.type ?? 'text'}" name="${field.name ?? ''}" value="${field.value ?? ''}" placeholder="${field.placeholder ?? ''}" ${field.required ? 'required' : ''} ${field.disabled ? 'disabled' : ''} ${field.readonly ? 'readonly' : ''} oninput="${field.oninput ?? ''}" onchange="${field.onchange ?? ''}" class="w-full rounded-lg bg-[var(--h-bg-color)] border-gray-600 text-[var(--text-color)] px-3 ${field.type == 'date' ? 'py-[7px]' : 'py-2'} border focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300 ease-in-out disabled:bg-transparent placeholder:capitalize">
                            </div>
                        </div>
                    `;
                } else {
                    clutter += `
                        <input id="${field.id ?? ''}" type="hidden" name="${field.name ?? ''}" value="${field.value ?? ''}">
                    `;
                }
            } else if (field.category == 'select') {
                let buttonHTML = '';
                let optionsHTML = '';
                
                if (field.btnId) {
                    buttonHTML = `
                        <button onclick="${field.onclick ?? ''}" id="${field.btnId ?? ''}" type="button" class="bg-[var(--primary-color)] px-4 rounded-lg hover:bg-[var(--h-primary-color)] transition-all duration-300 ease-in-out cursor-pointer text-lg font-bold disabled:opacity-50 disabled:cursor-not-allowed" disabled>+</button>
                    `;
                }

                if (field.options && field.options.length > 0) {
                    optionsHTML = `<option value="">-- Select Category --</option>`;
                    
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
                        
                        <div class="selectParent relative flex gap-4">
                            <select id="${field.id ?? ''}" name="${field.name ?? ''}" onchange="${field.onchange}" class="w-full rounded-lg bg-[var(--h-bg-color)] border-gray-600 text-[var(--text-color)] px-3 py-2 border appearance-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300 ease-in-out disabled:bg-transparent" ${field.required ? 'required' : ''} ${field.disabled ? 'disabled' : ''} ${field.readonly ? 'readonly' : ''}>
                                ${optionsHTML}
                            </select>
                            ${buttonHTML}
                        </div>
                    </div>
                `;
            }
        });

        clutter += `
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
        
    clutter += `
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="modal-action"
            class="bg-[var(--secondary-bg-color)] rounded-2xl shadow-lg max-w-3xl w-auto p-3 relative text-sm">
            <div class="flex gap-4">
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
                        class="px-4 py-2 bg-[var(--secondary-bg-color)] border border-gray-600 text-[var(--secondary-text)] rounded-lg hover:bg-[var(--h-bg-color)] transition-all duration-300 ease-in-out cursor-pointer hover:scale-[0.95]">
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
        if (e.target.id === `${data.id}`) {
            const form = e.target;
            form.classList.add('scale-out');
            form.addEventListener('animationend', () => {
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
        }
    };

    document.addEventListener('keydown', escToClose);
    document.body.appendChild(modalWrapper);
}