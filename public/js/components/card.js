function createCard(data) {
    const statusColor = {
        active: ['bg-[var(--border-success)]', 'text-[var(--border-success)]'],
        transparent: ['bg-transparent', 'text-transparent'],
        no_Image: ['bg-[var(--border-warning)]', 'text-[var(--border-warning)]'],
        inactive: ['bg-[var(--border-error)]', 'text-[var(--border-error)]'],
    };

    let clutter = `
        <div id="${data.id}" data-json='${JSON.stringify(data)}' oncontextmenu='${data.oncontextmenu || ""}' class="item card relative border border-gray-600 shadow rounded-xl min-w-[100px] h-[8rem] flex gap-4 p-4 cursor-pointer overflow-hidden fade-in" onclick='${data.onclick || ""}'>

            <button type="button" class="absolute bottom-0 right-0 rounded-full w-[25%] aspect-square flex items-center justify-center text-lg translate-x-1/4 translate-y-1/4 transition-all duration-200 ease-in-out cursor-pointer">
                <div class="absolute top-0 left-0 bg-[var(--h-bg-color)] blur-md rounded-full h-50 aspect-square"></div>
                <i class='fas fa-arrow-right text-2xl -rotate-45'></i>
            </button>
    `;

    if (data.user.status) {
        const status = data.user.status;
        const [dotClass, labelClass] = statusColor[status] || statusColor.inactive;
        clutter += `
            <div class="active_inactive_dot absolute top-2 right-2 w-[0.6rem] h-[0.6rem] rounded-full ${dotClass}"></div>
            <div class="active_inactive absolute top-2 right-2 text-xs capitalize ${labelClass} h-[1rem]">
                ${status.replace('_', ' ')}
            </div>
        `;
    }

    if (data.image) {
        clutter += `
            <div class="${data.classImg ?? ''} img aspect-square h-full rounded-[41.5%] overflow-hidden relative">
                <img src="${data.image}" loading="lazy" alt="" class="w-full h-full object-cover">
            </div>
        `;
    }

    let detailsHTML = '';
    if (data.details && typeof data.details === 'object') {
        detailsHTML = Object.entries(data.details).map(([label, value]) => {
            return `
            <p class="text-[var(--secondary-text)] tracking-wide text-sm capitalize">
                <strong>${label}:</strong> <span style="opacity: 0.9">${value}</span>
            </p>
        `;
        }).join('');
    }

    clutter += `
        <div class="text-start ${data.image ? 'pt-1' : ''}">
            <h5 class="text-xl mb-2 text-[var(--text-color)] capitalize font-semibold leading-none">
                ${data.name ?? 'N/A'}
            </h5>
            ${detailsHTML}
        </div>
    `;

    clutter += '</div>'; // Close the card div
    return clutter;
}
