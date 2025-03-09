@extends('app')
@section('title', 'Show Customers | ' . app('company')->name)
@section('content')
    <!-- Modal -->
    <div id="modal"
        class="hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-black bg-opacity-50 fade-in">
    </div>
    <!-- Main Content -->
    <div>
        <h1 class="text-3xl font-bold mb-5 text-center text-[--primary-color]">
            Show Customers
        </h1>

        <section class="text-center mx-auto ">
            <div
                class="show-box mx-auto w-full md:w-[80%] h-[70vh] bg-[--secondary-bg-color] rounded-xl shadow-lg overflow-y-auto p-7 pt-12 relative">
                <div
                    class="form-title text-center absolute top-0 left-0 w-full bg-[--primary-color] py-1 uppercase font-semibold">
                    <h4>Show Customers</h4>
                </div>

                <div
                    class="add-new-article-btn absolute bottom-8 right-5 hover:scale-105 hover:bottom-9 transition-all group duration-300 ease-in-out">
                    <a href="{{ route('customers.create') }}"
                        class="bg-[--primary-color] text-[--text-color] px-3 py-2 rounded-full hover:bg-[--h-primary-color] transition-all duration-300 ease-in-out"><i
                            class="fas fa-plus"></i></a>
                    <span
                        class="absolute shadow-xl right-7 top-0 border border-gray-600 transform -translate-x-1/2 bg-[--secondary-bg-color] text-[--text-color] text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
                        Add
                    </span>
                </div>

                @if (count($customers) > 0)
                    <div class="card_container grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                        @foreach ($customers as $customer)
                            <div data-json='{{ $customer }}'
                                class="contextMenuToggle modalToggle card relative border border-gray-600 shadow rounded-xl min-w-[100px] h-[8rem] flex gap-4 p-4 cursor-pointer overflow-hidden fade-in">
                                <x-card :data="[
                                    'image' =>
                                        $customer->user['profile_picture'] == 'default_avatar.png'
                                            ? asset('images/default_avatar.png')
                                            : asset('storage/uploads/images/' . $customer->user['profile_picture']),
                                    'name' => $customer->customer_name,
                                    'status' => $customer->user->status,
                                    'details' => [
                                        'Person Name' => $customer->person_name,
                                        'Category' => $customer->category->title,
                                    ],
                                ]" />
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="no-article-message w-full h-full flex flex-col items-center justify-center gap-2">
                        <h1 class="text-md text-[--secondary-text] capitalize">No Customer yet</h1>
                        <a href="{{ route('cutomers.create') }}"
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
                            <h5 id="name" class="text-2xl my-1 text-[--text-color] capitalize font-semibold">${data.customer_name}</h5>
                            <p class="text-[--secondary-text] mb-1 tracking-wide text-sm"><strong>Person Name:</strong> <span>${data.person_name}</span></p>
                            <p class="text-[--secondary-text] mb-1 tracking-wide text-sm"><strong>Username:</strong> <span>${data.user.username}</span></p>
                            <p class="text-[--secondary-text] mb-1 tracking-wide text-sm"><strong>Phone Number:</strong> <span>${data.phone_number}</span></p>
                            <p class="text-[--secondary-text] mb-1 tracking-wide text-sm"><strong>Category:</strong> <span>${data.category.title}</span></p>
                        </div>
                    </div>
                
                    <!-- Modal Action Slot -->
                    <x-slot name="actions">
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
    </script>
@endsection
