@extends('app')
@section('title', 'Show Customers | ' . app('company')->name)
@section('content')
    @php
        $searchFields = [
            'Customer Name' => [
                'id' => 'customer_name',
                'type' => 'text',
                'placeholder' => 'Enter customer name',
                'dataFilterPath' => 'name',
            ],
            'Urdu Title' => [
                'id' => 'urdu_title',
                'type' => 'text',
                'placeholder' => 'Enter urdu title',
                'dataFilterPath' => 'details.Urdu Title',
            ],
            'Username' => [
                'id' => 'username',
                'type' => 'text',
                'placeholder' => 'Enter username',
                'dataFilterPath' => 'user.username',
            ],
            'Phone' => [
                'id' => 'phone',
                'type' => 'text',
                'placeholder' => 'Enter phone number',
                'dataFilterPath' => 'phone_number',
            ],
            'Category' => [
                'id' => 'category',
                'type' => 'select',
                'options' => [
                    'cash' => ['text' => 'Cash'],
                    'regular' => ['text' => 'Regular'],
                    'site' => ['text' => 'Site'],
                    'other' => ['text' => 'Others'],
                ],
                'dataFilterPath' => 'details.Category',
            ],
            'City' => [
                'id' => 'city',
                'type' => 'select',
                'options' => $cities_options,
                'dataFilterPath' => 'city',
            ],
            'Date Range' => [
                'id' => 'date_range_start',
                'type' => 'date',
                'id2' => 'date_range_end',
                'type2' => 'date',
                'dataFilterPath' => 'date',
            ],
        ];
    @endphp
    <!-- Modal -->
    <div id="modal"
        class="hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-[var(--overlay-color)] fade-in">
    </div>
    <div>
        <div class="w-[80%] mx-auto">
            <x-search-header heading="Customers" :search_fields=$searchFields />
        </div>

        <!-- Main Content -->
        <section class="text-center mx-auto">
            <div
                class="show-box mx-auto w-[80%] h-[70vh] bg-[var(--secondary-bg-color)] rounded-xl shadow overflow-y-auto pt-8.5 pr-2 relative">
                <x-form-title-bar title="Show Customers" changeLayoutBtn layout="{{ $authLayout }}" />

                @if (count($customers) > 0)
                    <div class="absolute bottom-3 right-3 flex items-center gap-2 w-fll z-50">
                        <x-section-navigation-button link="{{ route('customers.create') }}" title="Add New Customer"
                            icon="fa-plus" />
                    </div>

                    <div class="details h-full z-40">
                        <div class="container-parent h-full overflow-y-auto my-scrollbar-2">
                            <div class="card_container pt-4 p-5 pr-3 h-full flex flex-col">
                                <div id="table-head" class="grid grid-cols-7 bg-[var(--h-bg-color)] rounded-lg font-medium py-2 hidden">
                                    <div class="text-left pl-5">Customer</div>
                                    <div class="text-left pl-5">Urdu Title</div>
                                    <div class="text-center">Category</div>
                                    <div class="text-center">City</div>
                                    <div class="text-center">Phone</div>
                                    <div class="text-right">Balance</div>
                                    <div class="text-right pr-5">Status</div>
                                </div>
                                <p id="noItemsError" style="display: none" class="text-sm text-[var(--border-error)]">No items found</p>
                                <div class="search_container grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 overflow-y-auto grow my-scrollbar-2">
                                    {{-- class="search_container overflow-y-auto grow my-scrollbar-2"> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="no-article-message w-full h-full flex flex-col items-center justify-center gap-2">
                        <h1 class="text-md text-[var(--secondary-text)] capitalize">No Customer yet</h1>
                        <a href="{{ route('customers.create') }}"
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
                            Customer</button>
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
                class="item row relative group grid text- grid-cols-7 border-b border-[var(--h-bg-color)] items-center py-2 cursor-pointer hover:bg-[var(--h-secondary-bg-color)] transition-all fade-in ease-in-out"
                data-json='${JSON.stringify(data)}'>

                <span class="text-left pl-5">${data.name}</span>
                <span class="text-left pl-5">${data.details["Urdu Title"]}</span>
                <span class="text-center capitalize">${data.details["Category"]}</span>
                <span class="text-center capitalize">${data.city}</span>
                <span class="text-center">${data.phone_number}</span>
                <span class="text-right">${Number(data.details["Balance"]).toFixed(1)}</span>
                <span class="text-right pr-5 capitalize ${data.user.status === 'active' ? 'text-[var(--border-success)]' : 'text-[var(--border-error)]'}">
                    ${data.user.status}
                </span>
            </div>`;
        }

        const fetchedData = @json($customers);
        let allDataArray = fetchedData.map(item => {
            return {
                id: item.id,
                image: item.user.profile_picture == 'default_avatar.png' ? '/images/default_avatar.png' : `/storage/uploads/images/${item.user.profile_picture}`,
                name: item.customer_name,
                details: {
                    'Urdu Title': item.urdu_title,
                    'Category': item.category,
                    'Balance': item.balance,
                },
                person_name: item.person_name,
                phone_number: item.phone_number,
                user: {
                    id: item.user.id,
                    username: item.user.username,
                    status: item.user.status,
                },
                city: item.city.title,
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

        function generateContextMenu(e) {
            contextMenu.classList.remove('fade-in');

            let ac_in_btn_context = document.getElementById('ac_in_btn_context');
            let ac_in_context = document.getElementById('ac_in_context');
            let item = e.target.closest('.item');
            let data = JSON.parse(item.dataset.json);

            ac_in_context.classList.add('hidden');

            if (ac_in_btn_context && data.balance == 0) {
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
                    window.location.href = "{{ route('customers.edit', ':id') }}".replace(':id', data.id);
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
                    'Person Name': data.person_name,
                    'Username': data.user.username,
                    'Phone Number': data.phone_number,
                    'Balance': formatNumbersWithDigits(data.details['Balance'], 1, 1),
                    'Category': data.details['Category'],
                    'City': data.city,
                },
                user: data.user,
                bottomActions: [
                    {id: 'edit-in-modal', text: 'Edit Customer'}
                ],
            }

            modalDom.innerHTML = createModal(modalData);

            // let ac_in_modal = document.getElementById('ac_in_modal');
            // let ac_in_btn = document.getElementById('ac_in_btn');
            // let active_inactive_dot_modal = document.getElementById('active_inactive_dot_modal');

            // ac_in_modal.classList.add("hidden");

            // if (currentUserRole == "developer" || currentUserRole == "owner" || currentUserRole == "admin") {
            //     ac_in_modal.classList.remove("hidden");
            // }

            // if (data.balance == 0) {
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
            // } else {
            //     ac_in_modal.classList.add("hidden");
            // }

            let editInModalDom = document.getElementById('edit-in-modal');
            editInModalDom.addEventListener('click', () => {
                window.location.href = "{{ route('customers.edit', ':id') }}".replace(':id', data.id);
            });

            openModal()
        }

        document.addEventListener('mousedown', (e) => {
            const {
                id
            } = e.target;
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

        // Function for Search
        function filterData(search) {
            const filteredData = cardsDataArray.filter(item => {
                switch (filterType) {
                    case 'all':
                        return (
                            item.customer_name.toLowerCase().includes(search) ||
                            item.urdu_title.toLowerCase().includes(search) ||
                            item.person_name.toLowerCase().includes(search) ||
                            item.category.toLowerCase().includes(search) ||
                            item.user.username.toLowerCase().includes(search)
                        );
                        break;

                    case 'customer_name':
                        return (
                            item.customer_name.toLowerCase().includes(search)
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

                    case 'category':
                        return (
                            item.category.toLowerCase().includes(search)
                        );
                        break;

                    case 'username':
                        return (
                            item.user.username.toLowerCase().includes(search)
                        );
                        break;

                    default:
                        return (
                            item.customer_name.toLowerCase().includes(search) ||
                            item.urdu_title.toLowerCase().includes(search) ||
                            item.person_name.toLowerCase().includes(search) ||
                            item.category.toLowerCase().includes(search) ||
                            item.user.username.toLowerCase().includes(search)
                        );
                        break;
                }
            });

            return filteredData;
        }
    </script>
@endsection
