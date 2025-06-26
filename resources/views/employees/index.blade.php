@extends('app')
@section('title', 'Show Employees | ' . app('company')->name)
@section('content')
    @php
        $searchFields = [
            "Employee Name" => [
                "id" => "employee_name",
                "type" => "text",
                "placeholder" => "Enter employee name",
                "oninput" => "runDynamicFilter()",
                "dataFilterPath" => "employee_name",
            ],
            "Urdu Title" => [
                "id" => "urdu_title",
                "type" => "text",
                "placeholder" => "Enter urdu title",
                "oninput" => "runDynamicFilter()",
                "dataFilterPath" => "urdu_title",
            ],
            "Phone" => [
                "id" => "phone",
                "type" => "text",
                "placeholder" => "Enter phone number",
                "oninput" => "runDynamicFilter()",
                "dataFilterPath" => "phone_number",
            ],
            "Category" => [
                "id" => "category",
                "type" => "select",
                "options" => [
                            'staff' => ['text' => 'Staff'],
                            'worker' => ['text' => 'Worker'],
                        ],
                "onchange" => "runDynamicFilter()",
                "dataFilterPath" => "category",
            ],
            "Type" => [
                "id" => "type",
                "type" => "select",
                "options" => $all_types,
                "onchange" => "runDynamicFilter()",
                "dataFilterPath" => "type.title",
            ],
            "Date Range" => [
                "id" => "date_range_start",
                "type" => "date",
                "id2" => "date_range_end",
                "type2" => "date",
                "oninput" => "runDynamicFilter()",
                "dataFilterPath" => "date",
            ]
        ];
    @endphp
    <!-- Modal -->
    <div id="modal"
        class="hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-[var(--overlay-color)] fade-in">
    </div>
    <div>
        <div class="w-[80%] mx-auto">
            <x-search-header heading="Employees" :search_fields=$searchFields/>
        </div>

        <!-- Main Content -->
        <section class="text-center mx-auto">
            <div class="show-box mx-auto w-[80%] h-[70vh] bg-[var(--secondary-bg-color)] rounded-xl shadow overflow-y-auto pt-8.5 pr-2 relative">
                <x-form-title-bar title="Show Employees" changeLayoutBtn layout="{{ $authLayout }}" />

                @if (count($employees) > 0)
                    <div class="absolute bottom-3 right-3 flex items-center gap-2 w-fll z-50">
                        <x-section-navigation-button link="{{ route('employees.create') }}" title="Add New Employee" icon="fa-plus" />
                    </div>
                
                    <div class="details h-full z-40">
                        <div class="container-parent h-full overflow-y-auto my-scrollbar-2">
                            <div class="card_container py-0 p-3 h-full flex flex-col">
                                <div id="table-head"class="grid grid-cols-5 bg-[var(--h-bg-color)] rounded-lg font-medium py-2">
                                    <div class="text-left pl-5">Employee Name</div>
                                    <div class="text-left pl-5">Category</div>
                                    <div class="text-center">Type</div>
                                    <div class="text-center">Balance</div>
                                    <div class="text-right pr-5">Status</div>
                                </div>
                                <p id="noItemsError" style="display: none" class="text-sm text-[var(--border-error)]">No items found</p>
                                <div class="search_container grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 overflow-y-auto grow my-scrollbar-2">
                                    {{-- class="search_container overflow-y-auto grow my-scrollbar-2"> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <p id="noItemsError" style="display: none" class="text-sm text-[var(--border-error)]">No items found</p>
                @else
                    <div class="no-article-message w-full h-full flex flex-col items-center justify-center gap-2">
                        <h1 class="text-md text-[var(--secondary-text)] capitalize">No Employee yet</h1>
                        <a href="{{ route('employees.create') }}"
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
                        <button id="edit-in-context" type="button"
                            class="flex items-center w-full px-4 py-2 text-left hover:bg-[var(--h-bg-color)] rounded-md transition-all duration-300 ease-in-out cursor-pointer">Edit 
                            Employee</button>
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
                <span class="text-left pl-5">${data.details["Category"]}</span>
                <span class="text-center capitalize">${data.details["Type"]}</span>
                <span class="text-right">${Number(data.details["Balance"]).toFixed(1)}</span>
                <span class="text-right pr-5 capitalize ${data.status === 'active' ? 'text-[var(--border-success)]' : 'text-[var(--border-error)]'}">
                    ${data.status}
                </span>
            </div>`;
        }

        const fetchedData = @json($employees);
        let allDataArray = fetchedData.map(item => {
            return {
                id: item.id,
                status: item.status,
                image: item.profile_picture == 'default_avatar.png' ? '/images/default_avatar.png' : `/storage/uploads/images/${item.profile_picture}`,
                name: item.employee_name,
                details: {
                    'Category': item.category,
                    'Type': item.type.title,
                    'Balance': item.balance ?? 0,
                },
                oncontextmenu: "generateContextMenu(event)",
                onclick: "generateModal(this)",
                joining_date: item.joining_date,
                cnic_no: item.cnic_no,
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

            let item = e.target.closest('.item');
            let data = JSON.parse(item.dataset.json);

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
                    window.location.href = "{{ route('employees.edit', ':id') }}".replace(':id', data.id);
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
                uId: data.id,
                status: data.status,
                method: "POST",
                action: "{{ route('update-employee-status') }}",
                class: '',
                closeAction: 'closeModal()',
                image: data.image,
                name: data.name,
                details: {
                    'Category': data.category,
                    'Type': data.type,
                    'Phone Number': data.phone_number,
                    'Joining Date': data.joining_date,
                    'C.N.I.C No.': data.cnic_no,
                    'Balance': data.Balance ?? 0,
                },
                profile: true,
                bottomActions: [
                    {id: 'edit-in-modal', text: 'Edit Employee'}
                ],
            }

            modalDom.innerHTML = createModal(modalData);

            let editInModalDom = document.getElementById('edit-in-modal');

            editInModalDom.addEventListener('click', () => {
                window.location.href = "{{ route('employees.edit', ':id') }}".replace(':id', data.id);
            });

            openModal()
        }

        document.addEventListener('mousedown', (e) => {
            const { id } = e.target;
            if (id === 'modalForm') {
                closeModal();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && isModalOpened) {
                closeContextMenu();
                closeModal();
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

        const categorySearchDom = document.getElementById('category');
        const typeSearchDom = document.getElementById('type');

        if (categorySearchDom) {
            categorySearchDom.addEventListener('change', () => {
                setTypeOptions(categorySearchDom.value);
            });
        }

        function setTypeOptions(category) {
            typeSearchDom.innerHTML = '<option value="">-- Select Type --</option>';
            let allTypes = @json($all_types);
            allTypes.forEach(type => {
                if (category != '') {
                    if (type.category === category) {
                        const option = document.createElement('option');
                        option.value = type.text;
                        option.textContent = type.text;
                        typeSearchDom.appendChild(option);
                    }
                } else {
                    const option = document.createElement('option');
                    option.value = type.text;
                    option.textContent = type.text;
                    typeSearchDom.appendChild(option);
                }
            });
        }
    </script>
@endsection
