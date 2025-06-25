@extends('app')
@section('title', 'Show Suppliers | ' . app('company')->name)
@section('content')
    @php
        $searchFields = [
            "Supplier Name" => [
                "id" => "supplier_name",
                "type" => "text",
                "placeholder" => "Enter supplier name",
                "dataFilterPath" => "name",
            ],
            "Urdu Title" => [
                "id" => "urdu_title",
                "type" => "text",
                "placeholder" => "Enter urdu title",
                "dataFilterPath" => "details.Urdu Title",
            ],
            "Username" => [
                "id" => "username",
                "type" => "text",
                "placeholder" => "Enter username",
                "dataFilterPath" => "user.username",
            ],
            "Phone" => [
                "id" => "phone",
                "type" => "text",
                "placeholder" => "Enter phone number",
                "dataFilterPath" => "details.Phone",
            ],
            "Date Range" => [
                "id" => "date_range_start",
                "type" => "date",
                "id2" => "date_range_end",
                "type2" => "date",
                "dataFilterPath" => "date",
            ]
        ];
    @endphp
    <!-- Modal -->
    <div id="modal"
        class="hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-[var(--overlay-color)] fade-in">
    </div>
    <div id="manageCategoryModal"
        class="hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-[var(--overlay-color)] fade-in">
    </div>
    <div>
        <div class="w-[80%] mx-auto">
            <x-search-header heading="Suppliers" :search_fields=$searchFields/>
        </div>

        <section class="text-center mx-auto ">
            <div
                class="show-box mx-auto w-[80%] h-[70vh] bg-[var(--secondary-bg-color)] rounded-xl shadow overflow-y-auto pt-8.5 pr-2 relative">
                <x-form-title-bar title="Show Suppliers" changeLayoutBtn layout="{{ $authLayout }}" />

                @if (count($suppliers) > 0)
                    <div class="absolute bottom-3 right-3 flex items-center gap-2 w-fll z-50">
                        <x-section-navigation-button link="{{ route('suppliers.create') }}" title="Add New Supplier" icon="fa-plus" />
                    </div>
                
                    <div class="details h-full z-40">
                        <div class="container-parent h-full overflow-y-auto my-scrollbar-2">
                            <div class="card_container pt-4 p-5 pr-3 h-full flex flex-col">
                                <div id="table-head" class="grid grid-cols-5 bg-[var(--h-bg-color)] rounded-lg font-medium py-2">
                                    <div class="text-left pl-5">Supplier</div>
                                    <div class="text-center">Urdu Title</div>
                                    <div class="text-center">Phone</div>
                                    <div class="text-right">Balance</div>
                                    <div class="text-right pr-5">Status</div>
                                </div>
                                <p id="noItemsError" style="display: none" class="text-sm text-[var(--border-error)]">No items found</p>
                                <div class="search_container grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="no-article-message w-full h-full flex flex-col items-center justify-center gap-2">
                        <h1 class="text-md text-[var(--secondary-text)] capitalize">No Suppliers yet</h1>
                        <a href="{{ route('suppliers.create') }}"
                        class="text-sm bg-[var(--primary-color)] text-[var(--text-color)] px-4 py-2 rounded-md hover:bg-[var(--h-primary-color)] hover:scale-105 hover:mb-2 transition-all duration-300 ease-in-out font-semibold">Add
                            New</a>
                    </div>
                @endif
            </div>
        </section>
        <div class="context-menu absolute top-0 text-sm" style="display: none;">
            <div
                class="border border-gray-600 w-48 bg-[var(--secondary-bg-color)] text-[var(--text-color)] shadow-lg rounded-xl transform transition-all duration-300 ease-in-out z-50">
                <ul class="p-2">
                    <li>
                        <button id="show-details" type="button"
                            class="flex items-center w-full px-4 py-2 text-left hover:bg-[var(--h-bg-color)] rounded-md transition-all duration-300 ease-in-out cursor-pointer">Show
                            Details</button>
                    </li>

                    <li>
                        <button id="manage-category" type="button"
                            class="flex items-center w-full px-4 py-2 text-left hover:bg-[var(--h-bg-color)] rounded-md transition-all duration-300 ease-in-out cursor-pointer">Manage
                            Category</button>
                    </li>

                    <li>
                        <button id="edit-in-context" type="button"
                            class="flex items-center w-full px-4 py-2 text-left hover:bg-[var(--h-bg-color)] rounded-md transition-all duration-300 ease-in-out cursor-pointer">Edit Supplier</button>
                    </li>

                    <li id="ac_in_context" class="hidden">
                        <form method="POST" action="{{ route('update-user-status') }}">
                            @csrf
                            <input type="hidden" id="user_id_context" name="user_id" value="">
                            <input type="hidden" id="user_status_context" name="status" value="">
                            <button id="ac_in_btn_context" type="submit"
                                class="flex w-full items-center text-left px-4 py-2 font-medium rounded-md transition-all duration-300 ease-in-out cursor-pointer">In
                                Active</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        let currentUserRole = '{{ Auth::user()->role }}';
        let authLayout = '{{ $authLayout }}';

        function createRow(data) {
            return `
            <div id="${data.id}" oncontextmenu='${data.oncontextmenu || ""}' onclick='${data.onclick || ""}'
                class="item row relative group grid text- grid-cols-5 border-b border-[var(--h-bg-color)] items-center py-2 cursor-pointer hover:bg-[var(--h-secondary-bg-color)] transition-all fade-in ease-in-out"
                data-json='${JSON.stringify(data)}'>

                <span class="text-left pl-5">${data.name}</span>
                <span class="text-left pl-5">${data.details["Urdu Title"]}</span>
                <span class="text-center capitalize">${data.details["Phone"]}</span>
                <span class="text-right">${Number(data.details["Balance"]).toFixed(1)}</span>
                <span class="text-right pr-5 capitalize ${data.user.status === 'active' ? 'text-[var(--border-success)]' : 'text-[var(--border-error)]'}">
                    ${data.user.status}
                </span>
            </div>`;
        }

        const fetchedData = @json($suppliers);
        let allDataArray = fetchedData.map(item => {
            return {
                id: item.id,
                image: item.user.profile_picture == 'default_avatar.png' ? '/images/default_avatar.png' : `/storage/uploads/images/${item.user.profile_picture}`,
                name: item.supplier_name,
                details: {
                    'Urdu Title': item.urdu_title,
                    'Phone': item.phone_number,
                    'Balance': item.balance,
                },
                user: {
                    id: item.user.id,
                    username: item.user.username,
                    status: item.user.status,
                },
                oncontextmenu: "generateContextMenu(event)",
                onclick: "generateModal(this)",
                date: item.date,
                visible: true,
            };
        });

        let contextMenu = document.querySelector('.context-menu');
        let isContextMenuOpened = false;

        function closeContextMenu() {
            contextMenu.classList.remove('fade-in');
            contextMenu.style.display = 'none';
            isContextMenuOpened = false;
        }

        function openContextMenu() {
            closeAllDropdowns()
            contextMenu.classList.add('fade-in');
            contextMenu.style.display = 'block';
            isContextMenuOpened = true;
        }

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
            let item = e.target.closest('.item');
            let data = JSON.parse(item.dataset.json);

            ac_in_context.classList.add('hidden');

            if (data.balance == 0.00) {z
                if (ac_in_btn_context) {
                    ac_in_btn_context.classList.add('text-[var(--border-error)]');
                    if (currentUserRole == "developer" || currentUserRole == "owner" || currentUserRole == "admin") {
                        if (data.user.status === 'active') {
                            ac_in_context.classList.remove('hidden');
                            ac_in_btn_context.classList.remove('text-[var(--border-success)]');
                            ac_in_btn_context.classList.remove('hover:text-[var(--text-success)]');
                            ac_in_btn_context.classList.remove('hover:bg-[var(--bg-success)]');
                            ac_in_btn_context.classList.add('text-[var(--border-error)]');
                            ac_in_btn_context.classList.add('hover:text-[var(--text-error)]');
                            ac_in_btn_context.classList.add('hover:bg-[var(--bg-error)]');
                            ac_in_btn_context.textContent = 'In Active';
                        } else {
                            ac_in_context.classList.remove('hidden');
                            ac_in_btn_context.classList.remove('text-[var(--border-error)]');
                            ac_in_btn_context.classList.remove('hover:text-[var(--text-error)]');
                            ac_in_btn_context.classList.remove('hover:bg-[var(--bg-error)]');
                            ac_in_btn_context.classList.add('text-[var(--border-success)]');
                            ac_in_btn_context.classList.add('hover:text-[var(--text-success)]');
                            ac_in_btn_context.classList.add('hover:bg-[var(--bg-success)]');
                            ac_in_btn_context.textContent = 'Active';
                        }
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

            document.addEventListener('mousedown', (e) => {
                if (e.target.id === "show-details") {
                    generateModal(item)
                }
            });

            document.addEventListener('mousedown', (e) => {
                if (e.target.id === "edit-in-context") {
                    window.location.href = "{{ route('suppliers.edit', ':id') }}".replace(':id', data.id);
                }
            });

            document.addEventListener('mousedown', (e) => {
                if (e.target.id === "ac_in_btn_context") {
                    user_id_context = document.getElementById('user_id_context');
                    user_status_context = document.getElementById('user_status_context');
                    user_id_context.value = data.user.id;
                    user_status_context.value = data.user.status;
                    ac_in_btn_context.click();
                }
                
                if (e.target.id === "manage-category") {
                    generateManageCategoryModal(item);
                }
            });

            // Function to remove context menu
            const removeContextMenu = (event) => {
                if (!contextMenu.contains(event.target)) {
                    closeContextMenu();
                    document.removeEventListener('click', removeContextMenu);
                    document.removeEventListener('contextmenu', removeContextMenu);
                }
            }

            // Wait for a small delay before attaching event listeners to avoid immediate removal
            setTimeout(() => {
                document.addEventListener('mousedown', removeContextMenu);
            }, 10);
        }

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

            let modalData = {
                id: 'modalForm',
                method: "POST",
                action: "{{ route('update-user-status') }}",
                class: '',
                closeAction: 'closeModal()',
                image: data.image,
                name: data.name,
                details: {
                    'Urud Title': data.details['Urdu Title'],
                    'Username': data.user.username,
                    'Phone Number': data.details['Phone'],
                    'Balance': formatNumbersWithDigits(data.details['Balance'], 1, 1),
                },
                user: data.user,
                bottomActions: [
                    {id: 'edit-in-modal', text: 'Edit Supplier'}
                ],
            }

            modalDom.innerHTML = createModal(modalData);

            let editInModalDom = document.getElementById('edit-in-modal');
            editInModalDom.addEventListener('click', () => {
                window.location.href = "{{ route('suppliers.edit', ':id') }}".replace(':id', data.id);
            });

            openModal()
        }

        document.addEventListener('mousedown', (e) => {
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
                            <h5 id="name" class="text-2xl mb-2 text-[var(--text-color)] capitalize font-semibold">Manage Categories</h5>
                            
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
                                <div id="chipsManagmeCategoryModal" class="w-full flex flex-wrap gap-2 overflow-y-auto my-scrollbar-2 text-[var(--text-color)]">
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <!-- Modal Action Slot -->
                    <x-slot name="actions">
                        <button onclick="closeManageCategoryModal()" type="button"
                            class="px-4 py-2 bg-[var(--secondary-bg-color)] border border-gray-600 text-[var(--secondary-text)] rounded-lg hover:bg-[var(--h-bg-color)] transition-all duration-300 ease-in-out cursor-pointer hover:scale-[0.95]">
                            Cancel
                        </button>

                        <input type="hidden" id="supplier_id" name="supplier_id" value="${data.id}">
                        <input type="hidden" id="categories_array" name="categories_array">
                        <button type="submit"
                            class="px-5 py-2 bg-[var(--bg-success)] border border-[var(--bg-success)] text-nowrap rounded-lg hover:bg-[var(--h-bg-success)] transition-all duration-300 ease-in-out cursor-pointer hover:scale-[0.95]">
                            Update Categories
                        </button>
                    </x-slot>
                </x-modal>
            `;

            let chipsClutter = "";
            data.categories.forEach((category) => {
                chipsClutter += `
                    <div data-id="${category.id}" class="chip border border-gray-600 text-xs rounded-xl py-2 px-4 inline-flex items-center gap-2">
                        <div class="text tracking-wide">${category.title}</div>
                        <button class="delete cursor-pointer ${data.categories.length <= 1 ? "hidden" : ""}" type="button">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                            class="size-3 stroke-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
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
                        existingChip.classList.add('bg-[var(--bg-error)]', 'transition', 'duration-300');
                        setTimeout(() => {
                            existingChip.classList.remove('bg-[var(--bg-error)]');
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
                    chip.className = 'chip border border-gray-600 text-xs rounded-xl py-2 px-4 inline-flex items-center gap-2 fade-in';
                    chip.setAttribute('data-id', selectedCategoryId);  // Store ID in a data attribute
                    chip.innerHTML = `
                        <div class="text tracking-wide">${selectedCategoryName}</div>
                        <button class="delete cursor-pointer" type="button">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                class="size-3 stroke-gray-400">
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

        // Function for Search
        function filterData(search) {
            const filteredData = cardsDataArray.filter(item => {
                switch (filterType) {
                    case 'all':
                        return (
                            item.supplier_name.toLowerCase().includes(search) ||
                            item.urdu_title.toLowerCase().includes(search) ||
                            item.person_name.toLowerCase().includes(search) ||
                            item.user.username.toLowerCase().includes(search)
                        );
                        break;
                        
                    case 'supplier_name':
                        return (
                            item.supplier_name.toLowerCase().includes(search)
                        );
                        break;
                        
                    case 'urdu_title':
                        return (
                            item.urdu_title.toLowerCase().includes(search)
                        );
                        break;
                        
                        
                    case 'person_name':
                        return (
                            item.person_name.toLowerCase().includes(search)
                        );
                        break;
                        
                    case 'username':
                        return (
                            item.user.username.toLowerCase().includes(search)
                        );
                        break;
                
                    default:
                        return (
                            item.supplier_name.toLowerCase().includes(search) ||
                            item.urdu_title.toLowerCase().includes(search) ||
                            item.person_name.toLowerCase().includes(search) ||
                            item.user.username.toLowerCase().includes(search)
                        );
                        break;
                }
            });

            return filteredData;
        }
    </script>
@endsection
