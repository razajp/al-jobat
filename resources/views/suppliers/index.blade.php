@extends('app')
@section('title', 'Show Suppliers | ' . app('company')->name)
@section('content')
    <!-- Modal -->
    <div id="modal"
        class="hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-black bg-opacity-50 fade-in">
    </div>
    <div id="manageCategoryModal"
        class="hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-black bg-opacity-50 fade-in">
    </div>
    <!-- Main Content -->
    <div>
        <h1 class="text-3xl font-bold mb-5 text-center text-[--primary-color]">
            Show Suppliers
        </h1>

        <section class="text-center mx-auto ">
            <div
                class="show-box mx-auto w-full md:w-[80%] h-[70vh] bg-[--secondary-bg-color] rounded-xl shadow-lg overflow-y-auto p-7 pt-12 relative">
                <div
                    class="form-title text-center absolute top-0 left-0 w-full bg-[--primary-color] py-1 uppercase font-semibold">
                    <h4>Show Suppliers</h4>
                </div>

                <div
                    class="add-new-article-btn absolute bottom-8 right-5 hover:scale-105 hover:bottom-9 transition-all group duration-300 ease-in-out">
                    <a href="{{ route('suppliers.create') }}"
                        class="bg-[--primary-color] text-[--text-color] px-3 py-2 rounded-full hover:bg-[--h-primary-color] transition-all duration-300 ease-in-out"><i
                            class="fas fa-plus"></i></a>
                    <span
                        class="absolute shadow-xl right-7 top-0 border border-gray-600 transform -translate-x-1/2 bg-[--secondary-bg-color] text-[--text-color] text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
                        Add
                    </span>
                </div>

                @if (count($Suppliers) > 0)
                    <div class="card_container grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                        @foreach ($Suppliers as $supplier)
                            <div data-json='{{ $supplier }}'
                                class="contextMenuToggle modalToggle card relative border border-gray-600 shadow rounded-xl min-w-[100px] h-[8rem] flex gap-4 p-4 cursor-pointer overflow-hidden fade-in">
                                <x-card :data="[
                                    'image' =>
                                        $supplier->user['profile_picture'] == 'default_avatar.png'
                                            ? asset('images/default_avatar.png')
                                            : asset('storage/uploads/images/' . $supplier->user['profile_picture']),
                                    'name' => $supplier->supplier_name,
                                    'status' => $supplier->user->status,
                                    'details' => [
                                        'Person Name' => $supplier->person_name,
                                        'Phone' => $supplier->phone_number,
                                    ],
                                ]" />
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="no-article-message w-full h-full flex flex-col items-center justify-center gap-2">
                        <h1 class="text-md text-[--secondary-text] capitalize">No Suppliers yet</h1>
                        <a href="{{ route('suppliers.create') }}"
                            class="text-md bg-[--primary-color] text-[--text-color] px-4 py-2 rounded-md hover:bg-blue-600 transition-all duration-300 ease-in-out uppercase font-semibold">Add
                            New</a>
                    </div>
                @endif
            </div>
        </section>
        <div class="context-menu absolute top-0 text-sm" style="display: none;">
            <div
                class="border border-gray-600 w-48 bg-[--secondary-bg-color] text-[--text-color] shadow-lg rounded-xl transform transition-all 0.3s ease-in-out z-50">
                <ul class="p-2">
                    <li>
                        <button id="show-details" type="button"
                            class="flex items-center w-full px-4 py-2 text-left hover:bg-[--h-bg-color] rounded-md transition-all 0.3s ease-in-out">Show
                            Details</button>
                    </li>

                    <li>
                        <button id="manage-category" type="button"
                            class="flex items-center w-full px-4 py-2 text-left hover:bg-[--h-bg-color] rounded-md transition-all 0.3s ease-in-out">Manage
                            Category</button>
                    </li>

                    <li id="ac_in_context" class="hidden">
                        <form method="POST" action="{{ route('update-user-status') }}">
                            @csrf
                            <input type="hidden" id="user_id_context" name="user_id" value="">
                            <input type="hidden" id="user_status_context" name="status" value="">
                            <button id="ac_in_btn_context" type="submit"
                                class="flex w-full items-center text-left px-4 py-2 font-medium rounded-md transition-all 0.3s ease-in-out">In
                                Active</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        let currentUserRole = '{{ Auth::user()->role }}';

        let contextMenu = document.querySelector('.context-menu');
        let isContextMenuOpened = false;

        function closeContextMenu() {
            contextMenu.classList.remove('fade-in');
            contextMenu.style.display = 'none';
            isContextMenuOpened = false;
        };

        function openContextMenu() {
            closeAllDropdowns()
            contextMenu.classList.add('fade-in');
            contextMenu.style.display = 'block';
            isContextMenuOpened = true;
        };

        let contextMenuToggle = document.querySelectorAll('.contextMenuToggle');

        contextMenuToggle.forEach(toggle => {
            toggle.addEventListener('contextmenu', (e) => {
                generateContextMenu(e);
            });
        });

        function generateContextMenu(e) {
            contextMenu.classList.remove('fade-in');

            let ac_in_btn_context = document.getElementById('ac_in_btn_context');
            let ac_in_context = document.getElementById('ac_in_context');
            let item = e.target.closest('.modalToggle');
            let data = JSON.parse(item.dataset.json);

            ac_in_context.classList.add('hidden');

            if (ac_in_btn_context) {
                ac_in_btn_context.classList.add('text-[--border-error]');
                if (currentUserRole == "developer" || currentUserRole == "owner" || currentUserRole == "admin") {
                    if (data.user.status === 'active') {
                        ac_in_context.classList.remove('hidden');
                        ac_in_btn_context.classList.remove('text-[--border-success]');
                        ac_in_btn_context.classList.remove('hover:text-[--text-success]');
                        ac_in_btn_context.classList.remove('hover:bg-[--bg-success]');
                        ac_in_btn_context.classList.add('text-[--border-error]');
                        ac_in_btn_context.classList.add('hover:text-[--text-error]');
                        ac_in_btn_context.classList.add('hover:bg-[--bg-error]');
                        ac_in_btn_context.textContent = 'In Active';
                    } else {
                        ac_in_context.classList.remove('hidden');
                        ac_in_btn_context.classList.remove('text-[--border-error]');
                        ac_in_btn_context.classList.remove('hover:text-[--text-error]');
                        ac_in_btn_context.classList.remove('hover:bg-[--bg-error]');
                        ac_in_btn_context.classList.add('text-[--border-success]');
                        ac_in_btn_context.classList.add('hover:text-[--text-success]');
                        ac_in_btn_context.classList.add('hover:bg-[--bg-success]');
                        ac_in_btn_context.textContent = 'Active';
                    }
                }
            }

            const wrapper = document.querySelector(".wrapper"); // Replace with your wrapper's ID

            if (!contextMenu || !wrapper) return;

            const wrapperRect = wrapper.getBoundingClientRect(); // Get wrapper's position

            let x = e.clientX - wrapperRect.left; // Adjust X relative to wrapper
            let y = e.clientY - wrapperRect.top; // Adjust Y relative to wrapper

            // Prevent right edge overflow
            if (x + contextMenu.offsetWidth > wrapperRect.width) {
                x -= contextMenu.offsetWidth;
            }

            // Prevent bottom edge overflow
            if (y + contextMenu.offsetHeight > wrapperRect.height) {
                y -= contextMenu.offsetHeight;
            }

            contextMenu.style.left = `${x}px`;
            contextMenu.style.top = `${y}px`;

            openContextMenu();

            document.addEventListener('click', (e) => {
                if (e.target.id === "show-details") {
                    generateModal(item)
                };
            });

            document.addEventListener('click', (e) => {
                if (e.target.id === "ac_in_btn_context") {
                    user_id_context = document.getElementById('user_id_context');
                    user_status_context = document.getElementById('user_status_context');
                    user_id_context.value = data.user.id;
                    user_status_context.value = data.user.status;
                    ac_in_btn_context.click();
                };
                
                if (e.target.id === "manage-category") {
                    generateManageCategoryModal(item);
                };
            });

            // Function to remove context menu
            const removeContextMenu = (event) => {
                if (!contextMenu.contains(event.target)) {
                    closeContextMenu();
                    document.removeEventListener('click', removeContextMenu);
                    document.removeEventListener('contextmenu', removeContextMenu);
                };
            };

            // Wait for a small delay before attaching event listeners to avoid immediate removal
            setTimeout(() => {
                document.addEventListener('click', removeContextMenu);
            }, 10);
        };

        let isModalOpened = false;
        let card = document.querySelectorAll('.modalToggle')

        card.forEach(item => {
            item.addEventListener('click', () => {
                generateModal(item);
            });
        });

        function generateModal(item) {
            let modalDom = document.getElementById('modal')
            let data = JSON.parse(item.dataset.json);

            modalDom.innerHTML = `
                <x-modal id="modalForm" closeAction="closeModal" action="{{ route('update-user-status') }}">
                    <!-- Modal Content Slot -->
                    <div id="active_inactive_dot_modal"
                        class="absolute top-3 left-3 w-[0.7rem] h-[0.7rem] bg-[--border-success] rounded-full">
                    </div>
                    <div class="flex items-start relative h-[15rem]">
                        <div class="rounded-full h-full aspect-square overflow-hidden">
                            <img id="imageInModal" src="{{ asset('images/default_avatar.png') }}" alt=""
                                class="w-full h-full object-cover">
                        </div>
                
                        <div class="flex-1 ml-8 h-full overflow-y-auto my-scroller-2">
                            <h5 id="name" class="text-2xl my-1 text-[--text-color] capitalize font-semibold">${data.supplier_name}</h5>
                            <p class="text-[--secondary-text] mb-1 tracking-wide text-sm"><strong>Person Name:</strong> <span>${data.person_name}</span></p>
                            <p class="text-[--secondary-text] mb-1 tracking-wide text-sm"><strong>Username:</strong> <span>${data.user.username}</span></p>
                            <p class="text-[--secondary-text] mb-1 tracking-wide text-sm"><strong>Phone Number:</strong> <span>${data.phone_number}</span></p>
                            
                            <hr class="border-gray-600 my-3">
                
                            <div class="chipsContainer">
                                <div id="chips" class="w-full flex flex-wrap gap-2">
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <!-- Modal Action Slot -->
                    <x-slot name="actions">
                        <button id="manageCategoryBtn" type="button"
                            class="px-4 py-2 bg-[--secondary-bg-color] border border-gray-600 text-[--secondary-text] rounded-lg hover:bg-[--h-bg-color] transition-all duration-300 ease-in-out">
                            Manage Category
                        </button>

                        <button onclick="closeModal()" type="button"
                            class="px-4 py-2 bg-[--secondary-bg-color] border border-gray-600 text-[--secondary-text] rounded-lg hover:bg-[--h-bg-color] transition-all duration-300 ease-in-out">
                            Cancel
                        </button>

                        <div id="ac_in_modal">
                            <input type="hidden" id="user_id" name="user_id" value="${data.user.id}">
                            <input type="hidden" id="user_status" name="status" value="${data.user.status}">
                            <button id="ac_in_btn" type="submit"
                                class="px-4 py-2 bg-[--bg-error] border border-[--bg-error] text-[--text-error] font-semibold rounded-lg hover:bg-[--h-bg-error] transition-all duration-300 ease-in-out">
                                In Active
                            </button>
                        </div>
                    </x-slot>
                </x-modal>
            `;

            let ac_in_modal = document.getElementById('ac_in_modal');
            let imageInModal = document.getElementById('imageInModal');
            let ac_in_btn = document.getElementById('ac_in_btn');
            let active_inactive_dot_modal = document.getElementById('active_inactive_dot_modal');

            ac_in_modal.classList.add("hidden");

            if (currentUserRole == "developer" || currentUserRole == "owner" || currentUserRole == "admin") {
                ac_in_modal.classList.remove("hidden");
            }

            if (data.user.profile_picture == "default_avatar.png") {
                imageInModal.src = `images/default_avatar.png`
            } else {
                imageInModal.src = `storage/uploads/images/${data.user.profile_picture}`
            }

            
            document.addEventListener('click', (e) => {
                if (e.target.id === "manageCategoryBtn") {
                    generateManageCategoryModal(item);
                };
            });

            let chipsClutter = "";
            data.categories.forEach((category) => {
                chipsClutter += `
                    <div class="chip border border-gray-600 text-[--secondary-text] text-xs rounded-xl py-2 px-4 inline-flex items-center gap-2">
                        <div class="text tracking-wide">${category.title}</div>
                    </div>
                `
            });

            let chipsContainerDom = document.getElementById("chips");
            chipsContainerDom.innerHTML = chipsClutter;

            if (data.user.status === 'active') {
                ac_in_btn.classList.add('bg-[--bg-error]')
                ac_in_btn.classList.add('border-[--bg-error]')
                ac_in_btn.classList.remove('bg-[--bg-success]')
                ac_in_btn.classList.remove('border-[--bg-success]')
                ac_in_btn.classList.add('hover:bg-[--h-bg-error]')
                ac_in_btn.classList.remove('hover:bg-[--h-bg-success]')
                ac_in_btn.classList.add('text-[--text-error]')
                ac_in_btn.classList.remove('text-[--text-success]')
                ac_in_btn.textContent = 'In Active'
                active_inactive_dot_modal.classList.remove('bg-[--border-error]')
                active_inactive_dot_modal.classList.add('bg-[--border-success]')
            } else {
                ac_in_btn.classList.remove('bg-[--bg-error]')
                ac_in_btn.classList.remove('border-[--bg-error]')
                ac_in_btn.classList.add('bg-[--bg-success]')
                ac_in_btn.classList.add('border-[--bg-success]')
                ac_in_btn.classList.remove('hover:bg-[--h-bg-error]')
                ac_in_btn.classList.add('hover:bg-[--h-bg-success]')
                ac_in_btn.classList.remove('text-[--text-error]')
                ac_in_btn.classList.add('text-[--text-success]')
                ac_in_btn.textContent = 'Active'
                active_inactive_dot_modal.classList.add('bg-[--border-error]')
                active_inactive_dot_modal.classList.remove('bg-[--border-success]')
            }

            openModal()
        }

        document.addEventListener('click', (e) => {
            const { id } = e.target;
            if (id === 'modalForm') {
                closeModal();
            } else if (id === 'manageCategoryModalForm') {
                closeManageCategoryModal();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && isModalOpened) {
                closeContextMenu();
                closeModal();
                closeManageCategoryModal()
            }
        })

        function openModal() {
            isModalOpened = true;
            document.getElementById('modal').classList.remove('hidden');
            closeAllDropdowns();
            closeContextMenu();
        }

        function closeModal() {
            modal.classList.add('fade-out');

            modal.addEventListener('animationend', () => {
                modal.classList.add('hidden');
                modal.classList.remove('fade-out');
            }, {
                once: true
            });
        }
        let categoriesArray;
        const manageCategoryModalDom = document.getElementById('manageCategoryModal');
        let isManageCategoryModalOpened = false;

        function generateManageCategoryModal(item) {
            let data = JSON.parse(item.dataset.json);

            manageCategoryModalDom.innerHTML = `
                <x-modal id="manageCategoryModalForm" classForBody="p-5 h-[20rem]" closeAction="closeManageCategoryModal" action="{{ route('update-supplier-category') }}">
                    <!-- Modal Content Slot -->
                    <div class="flex items-start relative">
                        <div class="flex-1 h-full">
                            <h5 id="name" class="text-2xl mb-2 text-[--text-color] capitalize font-semibold">Manage Categories</h5>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <x-input 
                                    label="Supplire Name"
                                    value="${data.supplier_name}" 
                                    disabled
                                />

                                <x-select 
                                    label="Category"
                                    id="category_select"
                                    :options="$categories_options"
                                    showDefault
                                    class="grow"
                                    withButton
                                    btnId="addCategoryBtn"
                                />
                            </div>
                            
                            <hr class="border-gray-600 my-3">
                
                            <div class="chipsContainer">
                                <div id="chipsManagmeCategoryModal" class="w-full flex flex-wrap gap-2 overflow-y-auto my-scroller-2">
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <!-- Modal Action Slot -->
                    <x-slot name="actions">
                        <button onclick="closeManageCategoryModal()" type="button"
                            class="px-4 py-2 bg-[--secondary-bg-color] border border-gray-600 text-[--secondary-text] rounded-lg hover:bg-[--h-bg-color] transition-all duration-300 ease-in-out">
                            Cancel
                        </button>
                        <input type="hidden" id="supplier_id" name="supplier_id" value="${data.id}">
                        <input type="hidden" id="categories_array" name="categories_array">
                        <button type="submit"
                            class="px-5 py-2 bg-[--bg-success] border border-[--bg-success] text-nowrap rounded-lg hover:bg-[--h-bg-success] transition-all 0.3s ease-in-out">
                            Update Categories
                        </button>
                    </x-slot>
                </x-modal>
            `;

            let chipsClutter = "";
            data.categories.forEach((category) => {
                chipsClutter += `
                    <div data-id="${category.id}" class="chip border border-gray-600 text-gray-300 text-xs rounded-xl py-2 px-4 inline-flex items-center gap-2">
                        <div class="text tracking-wide">${category.title}</div>
                        ${data.categories.length > 1 ? `
                            <button class="delete" type="button">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                class="size-3 stroke-gray-300">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        ` : "" }
                    </div>
                `
            });

            let chipsContainerDom = document.getElementById("chipsManagmeCategoryModal");
            chipsContainerDom.innerHTML = chipsClutter;

            const categorySelectDom = document.getElementById("category_select");
            const addCategoryBtnDom = document.getElementById("addCategoryBtn");
            addCategoryBtnDom.disabled = true;
            
            categorySelectDom.addEventListener("change", (e) => {
                trackStateOfCategoryBtn(e.target.value);
            })
            
            function trackStateOfCategoryBtn(value){
                if (value != "") {
                    addCategoryBtnDom.disabled = false;
                } else {
                    addCategoryBtnDom.disabled = true;
                }
            }

            const categoriesArrayInput = document.getElementById("categories_array");
            categoriesArray = data.categories
            .filter(category => typeof category === 'object')  // Keep only objects
            .map(category => category.id.toString());           // Extract IDs as strings


            addCategoryBtnDom.addEventListener('click', () => {
                let selectedCategoryId = categorySelectDom.value;  // Get category ID
                let selectedCategoryName = categorySelectDom.options[categorySelectDom.selectedIndex].text;  // Get category name

                console.log(categoriesArray);
                
                // Check for duplicates based on ID
                if (categoriesArray.includes(selectedCategoryId)) {
                    console.warn('Category already exists!');
                    
                    // Highlight the existing chip
                    let existingChip = Array.from(chipsContainerDom.children).find(chip => 
                        chip.getAttribute('data-id') === selectedCategoryId
                    );

                    if (existingChip) {
                        messageBox.innerHTML = `
                            <x-alert type="error" :messages="'This category already exists.'" />
                        `;
                        messageBoxAnimation();
                        existingChip.classList.add('bg-[--bg-error]', 'transition', 'duration-300');
                        setTimeout(() => {
                            existingChip.classList.remove('bg-[--bg-error]');
                        }, 5000);  // Remove highlight after 5 seconds
                        categorySelectDom.value = '';  // Clear selection
                        addCategoryBtnDom.disabled = true;  // Disable button
                        categorySelectDom.focus();
                    }

                    return;  // Stop the function if duplicate is found
                }

                if (selectedCategoryId) {
                    // Create the chip element
                    let chip = document.createElement('div');
                    chip.className = 'chip border border-gray-600 text-gray-300 text-xs rounded-xl py-2 px-4 inline-flex items-center gap-2 fade-in';
                    chip.setAttribute('data-id', selectedCategoryId);  // Store ID in a data attribute
                    chip.innerHTML = `
                        <div class="text tracking-wide">${selectedCategoryName}</div>
                        <button class="delete" type="button">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                class="size-3 stroke-gray-300">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    `;

                    if (chipsContainerDom) {
                        chipsContainerDom.appendChild(chip);
                        categoriesArray.push(selectedCategoryId);  // Store category ID in array
                        categoriesArrayInput.value = JSON.stringify(categoriesArray);  // Update hidden input with IDs
                        categorySelectDom.value = '';  // Clear selection
                        addCategoryBtnDom.disabled = true;  // Disable button
                        categorySelectDom.focus();
                        const allChips = chipsContainerDom.querySelectorAll('.chip');
                        allChips.forEach((chip) => {
                            let deleteBtn = chip.querySelector('.delete')
                            if (deleteBtn.classList.contains('hidden')) {
                                deleteBtn.classList.remove('hidden')
                            }
                        })
                    } else {
                        console.error('Chip container not found!');
                    }
                } else {
                    console.warn('No category selected!');
                }
            })

            chipsContainerDom.addEventListener('click', (e) => {
                const deleteButton = e.target.closest('.delete');

                if (deleteButton) {
                    const allChips = chipsContainerDom.querySelectorAll('.chip');

                    // Normal delete logic
                    const clickedChip = deleteButton.parentElement;
                    clickedChipId = clickedChip.dataset.id;

                    clickedChip.classList.add('fade-out');

                    setTimeout(() => {
                        clickedChip.remove();
                        categoriesArray = categoriesArray.filter(cat => cat !== clickedChipId);
                        categoriesArrayInput.value = JSON.stringify(categoriesArray);  // Update hidden input with IDs

                        // Update allChips after deletion
                        const updatedChips = chipsContainerDom.querySelectorAll('.chip');

                        // Agar sirf 1 chip bachi hai toh uska delete button hide karo
                        if (updatedChips.length === 1) {
                            const lastChipDeleteBtn = updatedChips[0].querySelector('.delete');
                            if (lastChipDeleteBtn) {
                                lastChipDeleteBtn.classList.add('hidden');
                            }
                        }
                    }, 300);
                }
            });

            openManageCategoryModal();
        }

        function openManageCategoryModal() {
            isManageCategoryModalOpened = true;
            manageCategoryModalDom.classList.remove('hidden');
            closeAllDropdowns();
            closeContextMenu()
        }

        function closeManageCategoryModal() {
            manageCategoryModalDom.classList.add('fade-out');

            manageCategoryModalDom.addEventListener('animationend', () => {
                manageCategoryModalDom.classList.add('hidden');
                manageCategoryModalDom.classList.remove('fade-out');
            }, {
                once: true
            });
        }
    </script>
@endsection
