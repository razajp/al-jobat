function createModal(data) {
    const statusColor = {
        active: ['[var(--bg-success)]', '[var(--h-bg-success)]', '[var(--border-success)]'],
        transparent: ['transparent', 'transparent', 'transparent'],
        no_Image: ['[var(--bg-warning)]', '[var(--h-bg-warning)]', '[var(--border-warning)]'],
        inactive: ['[var(--bg-error)]', '[var(--h-bg-error)]', '[var(--border-error)]'],
    };

    let clutter = `
        <form id="${data.id}" method="${data.method ?? 'POST'}" action="${data.action}" enctype="multipart/form-data" class="w-full h-full flex flex-col space-y-4 relative items-center justify-center">
            <div class="${data.class} bg-[var(--secondary-bg-color)] rounded-2xl shadow-lg w-full max-w-2xl p-6 flex relative">
                <div id="modal-close" onclick="${data.closeAction}"
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

    if (data.user.status) {
        const [bgColor, hoverBgColor, textColor] = statusColor[data.user.status] || statusColor.inactive;
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
                <div class="${!data.user ? 'rounded-lg' : 'rounded-[41.5%]'} h-full aspect-square overflow-hidden">
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
                        <div class="flex-1 ml-8 h-full overflow-y-auto my-scrollbar-2">
                            <h5 id="name" class="text-2xl my-1 text-[var(--text-color)] capitalize font-semibold">${data.name}</h5>
                            ${detailsHTML}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="modal-action"
            class="bg-[var(--secondary-bg-color)] rounded-2xl shadow-lg max-w-3xl w-auto p-3 relative text-sm">
            <div class="flex gap-4">
                <button onclick="${data.closeAction}" type="button"
                    class="px-4 py-2 bg-[var(--secondary-bg-color)] border border-gray-600 text-[var(--secondary-text)] rounded-lg hover:bg-[var(--h-bg-color)] transition-all duration-300 ease-in-out cursor-pointer hover:scale-[0.95]">
                    Cancel
                </button>
    `;
    
    if (data.bottomActions) {
        data.bottomActions.forEach(action => {
            clutter += `
                <button id="${action.id}" type="${action.type ?? 'button'}"
                    class="px-4 py-2 bg-[var(--secondary-bg-color)] border border-gray-600 text-[var(--secondary-text)] rounded-lg hover:bg-[var(--h-bg-color)] transition-all duration-300 ease-in-out cursor-pointer hover:scale-[0.95]">
                    ${action.text}
                </button>
            `;
        });
    }


    if (data.user.status) {
        const [bgColor, hoverBgColor, textColor] = statusColor[data.user.status = 'active' ? 'in_active' : 'active'] || statusColor.inactive;
        clutter += `
            <div id="ac_in_modal">
                <input type="hidden" id="user_id" name="user_id" value="${data.user.id}">
                <input type="hidden" id="user_status" name="status" value="${data.user.status}">
                <button id="ac_in_btn" type="submit"
                    class="px-4 py-2 bg-${bgColor} border border-${bgColor} text-${textColor} font-semibold rounded-lg hover:bg-${hoverBgColor} transition-all duration-300 ease-in-out cursor-pointer hover:scale-[0.95] capitalize">
                    ${data.user.status.replace('_', ' ')}
                </button>
            </div>
        `;
    }

    clutter += `
                </div>
            </div>
        </form>
    `;
    return clutter;
}