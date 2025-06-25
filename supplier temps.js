

        // function generateModal(item) {
        //     let modalDom = document.getElementById('modal')
        //     let data = JSON.parse(item.dataset.json);



        //     let ac_in_modal = document.getElementById('ac_in_modal');
        //     let imageInModal = document.getElementById('imageInModal');
        //     let ac_in_btn = document.getElementById('ac_in_btn');
        //     let active_inactive_dot_modal = document.getElementById('active_inactive_dot_modal');

        //     ac_in_modal.classList.add("hidden");

        //     if (data.balance == 0.00) {
        //         if (currentUserRole == "developer" || currentUserRole == "owner" || currentUserRole == "admin") {
        //             ac_in_modal.classList.remove("hidden");
        //         }
        //     }

        //     if (data.user.profile_picture == "default_avatar.png") {
        //         imageInModal.src = `images/default_avatar.png`
        //     } else {
        //         imageInModal.src = `storage/uploads/images/${data.user.profile_picture}`
        //     }

        //     document.getElementById('edit-in-modal').addEventListener('click', () => {
        //         window.location.href = "{{ route('suppliers.edit', ':id') }}".replace(':id', data.id);
        //     });
            
        //     document.addEventListener('mousedown', (e) => {
        //         if (e.target.id === "manageCategoryBtn") {
        //             generateManageCategoryModal(item);
        //         }
        //     });

        //     let chipsClutter = "";
        //     data.categories.forEach((category) => {
        //         chipsClutter += `
        //             <div class="chip border border-gray-600 text-[var(--secondary-text)] text-xs rounded-xl py-2 px-4 inline-flex items-center gap-2">
        //                 <div class="text tracking-wide">${category.title}</div>
        //             </div>
        //         `
        //     });

        //     let chipsContainerDom = document.getElementById("chips");
        //     chipsContainerDom.innerHTML = chipsClutter;

        //     if (data.user.status === 'active') {
        //         ac_in_btn.classList.add('bg-[var(--bg-error)]')
        //         ac_in_btn.classList.add('border-[var(--bg-error)]')
        //         ac_in_btn.classList.remove('bg-[var(--bg-success)]')
        //         ac_in_btn.classList.remove('border-[var(--bg-success)]')
        //         ac_in_btn.classList.add('hover:bg-[var(--h-bg-error)]')
        //         ac_in_btn.classList.remove('hover:bg-[var(--h-bg-success)]')
        //         ac_in_btn.classList.add('text-[var(--text-error)]')
        //         ac_in_btn.classList.remove('text-[var(--text-success)]')
        //         ac_in_btn.textContent = 'In Active'
        //         active_inactive_dot_modal.classList.remove('bg-[var(--border-error)]')
        //         active_inactive_dot_modal.classList.add('bg-[var(--border-success)]')
        //     } else {
        //         ac_in_btn.classList.remove('bg-[var(--bg-error)]')
        //         ac_in_btn.classList.remove('border-[var(--bg-error)]')
        //         ac_in_btn.classList.add('bg-[var(--bg-success)]')
        //         ac_in_btn.classList.add('border-[var(--bg-success)]')
        //         ac_in_btn.classList.remove('hover:bg-[var(--h-bg-error)]')
        //         ac_in_btn.classList.add('hover:bg-[var(--h-bg-success)]')
        //         ac_in_btn.classList.remove('text-[var(--text-error)]')
        //         ac_in_btn.classList.add('text-[var(--text-success)]')
        //         ac_in_btn.textContent = 'Active'
        //         active_inactive_dot_modal.classList.add('bg-[var(--border-error)]')
        //         active_inactive_dot_modal.classList.remove('bg-[var(--border-success)]')
        //     }

        //     openModal()
        // }