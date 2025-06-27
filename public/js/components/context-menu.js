function createContextMenu(data) {
    const statusColor = {
        active: ['[var(--bg-success)]', '[var(--text-success)]', '[var(--border-success)]'],
        transparent: ['transparent', 'transparent', 'transparent'],
        no_Image: ['[var(--bg-warning)]', '[var(--text-warning)]', '[var(--border-warning)]'],
        inactive: ['[var(--bg-error)]', '[var(--text-error)]', '[var(--border-error)]'],
    };

    // Remove old menu if exists
    const old = document.getElementById('context-menu');
    if (old) old.remove();

    const contextMenu = document.createElement('div');
    contextMenu.id = 'context-menu';
    contextMenu.className = 'context-menu absolute text-sm z-50';
    contextMenu.style.top = `${data.y}px`;
    contextMenu.style.left = `${data.x}px`;

    let clutter = `
        <div class="border border-gray-600 w-48 bg-[var(--secondary-bg-color)] text-[var(--text-color)] shadow-lg rounded-xl transition-all duration-300 ease-in-out">
            <ul class="p-2">
                <li>
                    <button id="show-details" type="button" onclick='generateModal(${JSON.stringify(data.data)})'
                        class="flex items-center w-full px-4 py-2 text-left hover:bg-[var(--h-bg-color)] rounded-md transition-all duration-300 ease-in-out cursor-pointer">
                        Show Details
                    </button>
                </li>
    `;

    if (Array.isArray(data.actions)) {
        data.actions.forEach(action => {
            clutter += `
                <li>
                    <button id="${action.id}-in-context" type="button"
                        class="flex items-center w-full px-4 py-2 text-left hover:bg-[var(--h-bg-color)] rounded-md transition-all duration-300 ease-in-out cursor-pointer">
                        ${action.text}
                    </button>
                </li>
            `;
        });
    }

    if (data.details?.Balance == 0.0) {
        if (data.user?.status || data.status) {
            let status = data.user?.status ?? data.status;
            const [bgColor, textColor, borderColor] = statusColor[status === 'active' ? 'inactive' : 'active'] || statusColor.inactive;

            clutter += `
                <li id="ac_in_context">
                    <form method="POST" action="/update-user-status">
                        <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]')?.content}">
                        <input type="hidden" name="user_id" value="${data.user?.id ?? data.uId}">
                        <input type="hidden" name="status" value="${status}">
                        <button type="submit"
                            class="flex w-full items-center text-left px-4 py-2 font-medium rounded-md transition-all duration-300 ease-in-out cursor-pointer text-${borderColor} hover:bg-${bgColor} hover:text-${textColor}">
                            In ${status.replace('_', ' ')}
                        </button>
                    </form>
                </li>
            `;
        }
    }

    clutter += `
            </ul>
        </div>
    `;

    contextMenu.innerHTML = clutter;
    document.body.appendChild(contextMenu);

    // Auto-close on outside click
    const closeMenu = (e) => {
        if (!contextMenu.contains(e.target)) {
            contextMenu.remove();
            document.removeEventListener('mousedown', closeMenu);
            document.removeEventListener('keydown', escClose);
        }
    };

    const escClose = (e) => {
        if (e.key === 'Escape') {
            contextMenu.remove();
            document.removeEventListener('mousedown', closeMenu);
            document.removeEventListener('keydown', escClose);
        }
    };

    document.addEventListener('mousedown', closeMenu);
    document.addEventListener('keydown', escClose);
}
