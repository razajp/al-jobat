@extends('app')
@section('title', 'Show Customers | ' . app('company')->name)
@section('content')
    <!-- Modal -->
    <div id="modal"
        class="hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-[var(--overlay-color)] fade-in">
    </div>
    <div>
        <div class="w-[80%] mx-auto">
            <x-search-header heading="Customers" :filter_items="[
                'all' => 'All',
                'customer_name' => 'Customer Name',
                'urdu_title' => 'Urdu Title',
                'person_name' => 'Person Name',
                'category' => 'Category',
                'username' => 'Username',
            ]"/>
        </div>

        <!-- Main Content -->
        <section class="text-center mx-auto">
            <div
                class="show-box mx-auto w-full md:w-[80%] h-[70vh] bg-[var(--secondary-bg-color)] rounded-xl shadow-lg overflow-y-auto p-7 pt-12 relative">
                <div
                    class="form-title text-center absolute top-0 left-0 w-full bg-[var(--primary-color)] py-1 uppercase font-semibold">
                    <h4>Show Customers</h4>

                    <div class="buttons absolute top-0 right-4 text-sm h-full flex items-center">
                        <div class="relative group">
                            <form method="POST" action="{{ route('change-data-layout') }}">
                                @csrf
                                <input type="hidden" name="layout" value="{{ $authLayout }}">
                                @if ($authLayout == 'grid')
                                    <button type="submit" class="group cursor-pointer">
                                        <i class='fas fa-list-ul text-white'></i>
                                        <span
                                            class="absolute shadow-md text-nowrap border border-gray-600 z-10 -right-1 top-8 bg-[var(--h-secondary-bg-color)] text-[var(--text-color)] text-[12px] rounded px-3 py-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">List</span>
                                    </button>
                                @else
                                    <button type="submit" class="group cursor-pointer">
                                        <i class='fas fa-grip text-white'></i>
                                        <span
                                            class="absolute shadow-md text-nowrap border border-gray-600 z-10 -right-1 top-8 bg-[var(--h-secondary-bg-color)] text-[var(--text-color)] text-[12px] rounded px-3 py-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">Grid</span>
                                    </button>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>

                <div
                    class="add-new-article-btn absolute z-[999] bottom-8 right-5 hover:scale-105 hover:bottom-9 transition-all group duration-300 ease-in-out">
                    <a href="{{ route('customers.create') }}"
                        class="bg-[var(--primary-color)] text-[var(--text-color)] px-3 py-2 rounded-full hover:bg-[var(--h-primary-color)] transition-all duration-300 ease-in-out"><i
                            class="fas fa-plus"></i></a>
                    <span
                        class="absolute shadow-xl right-7 top-0 border border-gray-600 transform -translate-x-1/2 bg-[var(--secondary-bg-color)] text-[var(--text-color)] text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
                        Add
                    </span>
                </div>

                @if (count($customers) > 0)
                    <div class="card_container">
                        @if ($authLayout == 'grid')
                            <div class="search_container grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                                @foreach ($customers as $customer)
                                    <div id='{{ $customer->id }}' data-json='{{ $customer }}'
                                        class="contextMenuToggle modalToggle card relative border border-gray-600 shadow rounded-xl min-w-[100px] h-[8rem] flex gap-4 p-4 cursor-pointer overflow-hidden fade-in">
                                        <x-card :data="[
                                            'image' =>
                                                $customer->user['profile_picture'] == 'default_avatar.png'
                                                    ? asset('images/default_avatar.png')
                                                    : asset('storage/uploads/images/' . $customer->user['profile_picture']),
                                            'name' => $customer->customer_name,
                                            'status' => $customer->user->status,
                                            'details' => [
                                                'Urdu Title' => $customer->urdu_title,
                                                'Category' => $customer->category,
                                                'Balance' => number_format($customer->balance, 1),
                                            ],
                                        ]" />
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="grid grid-cols-5 bg-[var(--h-bg-color)] rounded-lg font-medium py-2">
                                <div class="text-left pl-5">Customer</div>
                                <div class="text-left pl-5">Urdu Title</div>
                                <div class="text-center">Category</div>
                                <div class="text-right">Balance</div>
                                <div class="text-right pr-5">Status</div>
                            </div>
                            <div class="search_container overflow-y-auto grow my-scrollbar-2">
                                @forEach ($customers as $customer)
                                    <div id="{{ $customer->id }}" data-json='{{ $customer }}' class="contextMenuToggle modalToggle relative group grid text- grid-cols-5 border-b border-[var(--h-bg-color)] items-center py-2 cursor-pointer hover:bg-[var(--h-secondary-bg-color)] transition-all fade-in ease-in-out">
                                        <span class="text-left pl-5">{{ $customer->customer_name }}</span>
                                        <span class="text-left pl-5">{{ $customer->urdu_title }}</span>
                                        <span class="text-center">{{ $customer->category }}</span>
                                        <span class="text-right">{{ number_format($customer->balance, 1) }}</span>
                                        <span class="text-right pr-5 capitalize {{ $customer->user->status == 'active' ? 'text-[var(--border-success)]' : 'text-[var(--border-error)]' }}">{{ $customer->user->status }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @else
                    <div class="no-article-message w-full h-full flex flex-col items-center justify-center gap-2">
                        <h1 class="text-md text-[var(--secondary-text)] capitalize">No Customer yet</h1>
                        <a href="{{ route('customers.create') }}"
                            class="text-md bg-[var(--primary-color)] text-[var(--text-color)] px-4 py-2 rounded-md hover:bg-blue-600 transition-all duration-300 ease-in-out uppercase font-semibold">Add
                            New</a>
                    </div>
                @endif
            </div>
        </section>
        <div class="context-menu absolute top-0 text-sm" style="display: none;">
            <div
                class="border border-gray-600 w-48 bg-[var(--secondary-bg-color)] text-[var(--text-color)] shadow-lg rounded-xl transform transition-all 0.3s ease-in-out z-50">
                <ul class="p-2">
                    <li>
                        <button id="show-details" type="button"
                            class="flex items-center w-full px-4 py-2 text-left hover:bg-[var(--h-bg-color)] rounded-md transition-all 0.3s ease-in-out cursor-pointer">Show
                            Details</button>
                    </li>

                    <li id="ac_in_context" class="hidden">
                        <form method="POST" action="{{ route('update-user-status') }}">
                            @csrf
                            <input type="hidden" id="user_id_context" name="user_id" value="">
                            <input type="hidden" id="user_status_context" name="status" value="">
                            <button id="ac_in_btn_context" type="submit"
                                class="flex w-full items-center text-left px-4 py-2 font-medium rounded-md transition-all 0.3s ease-in-out cursor-pointer">In
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
            let item = e.target.closest('.modalToggle');
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
                        class="absolute top-3 left-3 w-[0.7rem] h-[0.7rem] bg-[var(--border-success)] rounded-full">
                    </div>
                    <div class="flex items-start relative h-[15rem]">
                        <div class="rounded-full h-full aspect-square overflow-hidden">
                            <img id="imageInModal" src="{{ asset('images/default_avatar.png') }}" alt=""
                                class="w-full h-full object-cover">
                        </div>
                
                        <div class="flex-1 ml-8 h-full overflow-y-auto my-scrollbar-2">
                            <h5 id="name" class="text-2xl my-1 text-[var(--text-color)] capitalize font-semibold">${data.customer_name}</h5>
                            <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm"><strong>Urdu Title:</strong> <span>${data.urdu_title}</span></p>
                            <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm"><strong>Person Name:</strong> <span>${data.person_name}</span></p>
                            <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm"><strong>Username:</strong> <span>${data.user.username}</span></p>
                            <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm"><strong>Phone Number:</strong> <span>${data.phone_number}</span></p>
                            <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm"><strong>Balance:</strong> <span>${formatNumbersWithDigits(data.balance, 1, 1)}</span></p>
                            <p class="text-[var(--secondary-text)] mb-1 tracking-wide text-sm"><strong>Category:</strong> <span>${data.category}</span></p>
                        </div>
                    </div>
                
                    <!-- Modal Action Slot -->
                    <x-slot name="actions">
                        <button onclick="closeModal()" type="button"
                            class="px-4 py-2 bg-[var(--secondary-bg-color)] border border-gray-600 text-[var(--secondary-text)] rounded-lg hover:bg-[var(--h-bg-color)] transition-all duration-300 ease-in-out cursor-pointer">
                            Cancel
                        </button>

                        <div id="ac_in_modal">
                            <input type="hidden" id="user_id" name="user_id" value="${data.user.id}">
                            <input type="hidden" id="user_status" name="status" value="${data.user.status}">
                            <button id="ac_in_btn" type="submit"
                                class="px-4 py-2 bg-[var(--bg-error)] border border-[var(--bg-error)] text-[var(--text-error)] font-semibold rounded-lg hover:bg-[var(--h-bg-error)] transition-all duration-300 ease-in-out cursor-pointer">
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
            
            if (data.balance == 0) {
                if (data.user.status === 'active') {
                    ac_in_btn.classList.add('bg-[var(--bg-error)]')
                    ac_in_btn.classList.add('border-[var(--bg-error)]')
                    ac_in_btn.classList.remove('bg-[var(--bg-success)]')
                    ac_in_btn.classList.remove('border-[var(--bg-success)]')
                    ac_in_btn.classList.add('hover:bg-[var(--h-bg-error)]')
                    ac_in_btn.classList.remove('hover:bg-[var(--h-bg-success)]')
                    ac_in_btn.classList.add('text-[var(--text-error)]')
                    ac_in_btn.classList.remove('text-[var(--text-success)]')
                    ac_in_btn.textContent = 'In Active'
                    active_inactive_dot_modal.classList.remove('bg-[var(--border-error)]')
                    active_inactive_dot_modal.classList.add('bg-[var(--border-success)]')
                } else {
                    ac_in_btn.classList.remove('bg-[var(--bg-error)]')
                    ac_in_btn.classList.remove('border-[var(--bg-error)]')
                    ac_in_btn.classList.add('bg-[var(--bg-success)]')
                    ac_in_btn.classList.add('border-[var(--bg-success)]')
                    ac_in_btn.classList.remove('hover:bg-[var(--h-bg-error)]')
                    ac_in_btn.classList.add('hover:bg-[var(--h-bg-success)]')
                    ac_in_btn.classList.remove('text-[var(--text-error)]')
                    ac_in_btn.classList.add('text-[var(--text-success)]')
                    ac_in_btn.textContent = 'Active'
                    active_inactive_dot_modal.classList.add('bg-[var(--border-error)]')
                    active_inactive_dot_modal.classList.remove('bg-[var(--border-success)]')
                }
            } else {
                ac_in_modal.classList.add("hidden");
            }

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
