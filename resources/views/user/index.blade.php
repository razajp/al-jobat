@extends('app')
@section('title', 'Show Users | Al Jobat')
@section('content')
    <!-- Modal -->
    <div id="userModal"
        class="hidden fixed inset-0 z-50 text-sm flex items-center justify-center bg-black bg-opacity-50 ">
        <!-- Modal Content -->
        <div class="bg-[--secondary-bg-color] rounded-lg shadow-lg w-full max-w-lg p-6 relative">
            <!-- Close Button -->
            <button onclick="closeUserModal()"
                class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-all duration-300 ease-in-out">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                    class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Modal Body -->
            <div id="modal_body" class="modal_body flex items-start">
                <div class="flex text-[--border-error] rounded-full w-[30%] aspect-square overflow-hidden">
                    <img id="userImage" src="{{ asset('/images/default_avatar.png') }}" alt=""
                        class="w-full h-full object-cover">
                </div>
                <div class="content ml-5">
                    <h5 id="name" class="text-3xl my-1 text-[--text-color] capitalize font-semibold">Hasan Raza
                    </h5>
                    <p class="text-[--secondary-text] mb-1 tracking-wide text-sm"><strong>Username:</strong> <span
                            id="username" class="username">Salaried-staff</span></p>
                    <p class="text-[--secondary-text] mb-1 tracking-wide text-sm"><strong>Role:</strong> <span
                            id="role" class="role">Office Staff</span></p>
                </div>
                <div id="active_inactive_dot_modal"
                    class="active_inactive_dot absolute top-3 left-3 w-[0.6rem] h-[0.6rem] bg-[--border-error] rounded-full">
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end space-x-3">
                <!-- Cancel Button -->
                <button onclick="closeUserModal()"
                    class="px-4 py-2 bg-[--secondary-bg-color] border text-[--secondary-text] rounded hover:bg-[--bg-color] transition-all duration-300 ease-in-out">
                    Cancel
                </button>

                <form id="ac_in_modal" method="POST" action="{{ route('update-user-status') }}">
                    @csrf
                    <input type="hidden" id="user_id" name="user_id" value="">
                    <input type="hidden" id="user_status" name="status" value="">
                    <button id="ac_in_btn" type="submit"
                        class="px-4 py-2 bg-[--danger-color] text-white rounded hover:bg-[--h-danger-color] transition-all duration-300 ease-in-out">
                        In Active
                    </button>
                </form>
            </div>
        </div>
    </div>
    <!-- Main Content -->
    <div>
        <h1 class="text-3xl font-bold mb-5 text-center text-[--primary-color]">
            Show Users
        </h1>

        <section class="text-center mx-auto ">
            <div
                class="show-box mx-auto w-[80%] h-[70vh] bg-[--secondary-bg-color] rounded-xl shadow overflow-y-auto p-7 pt-12 relative">
                <div
                    class="form-title text-center absolute top-0 left-0 w-full bg-[--primary-color] py-1 shadow-lg uppercase font-semibold">
                    <h4>Show Users</h4>
                </div>

                <div
                    class="add-new-article-btn absolute bottom-8 right-5 hover:scale-105 hover:bottom-9 transition-all group duration-300 ease-in-out">
                    <a href="{{ route('users.create') }}"
                        class="bg-[--primary-color] text-[--text-color] px-3 py-2 rounded-full hover:bg-[--h-primary-color] transition-all duration-300 ease-in-out"><i
                            class="fas fa-plus"></i></a>
                    <span
                        class="absolute shadow-xl right-7 top-0 border border-gray-600 transform -translate-x-1/2 bg-[--secondary-bg-color] text-[--text-color] text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
                        Add
                    </span>
                </div>

                @if (count($users) > 0)
                    <div class="card_container grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                        @foreach ($users as $user)
                            {{-- <div data-user="{{ $user }}"
                                class="contextMenuToggle modalToggle card relative border border-gray-600 shadow rounded-xl min-w-[100px] h-[7.8rem] flex gap-3 p-4 cursor-pointer overflow-hidden fade-in">
                                <div class="img aspect-square h-full rounded-full overflow-hidden">
                                    @if ($user->profile_picture == 'default_avatar.png')
                                        <img src="{{ asset('images/default_avatar.png') }}" alt=""
                                            class="w-full h-full object-cover">
                                    @else
                                        <img src="{{ asset('storage/uploads/images/' . $user->profile_picture) }}" alt=""
                                            class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div class="details text-start">
                                    <h5 class="text-xl my-1 text-[--text-color] capitalize font-semibold">
                                        {{ $user->name }}
                                    </h5>
                                    <p class="text-[--secondary-text] tracking-wide">
                                        <strong>Username:</strong>
                                        <span class="username">{{ $user->username }}</span>
                                    </p>
                                    <p class="text-[--secondary-text] tracking-wide capitalize">
                                        <strong>Role:</strong> <span class="role">{{ $user->role }}</span>
                                    </p>
                                </div>
                                <button
                                    class="absolute bottom-0 right-0 rounded-full w-[25%] aspect-square flex items-center justify-center bg-[--h-bg-color] text-lg translate-x-1/4 translate-y-1/4 transition-all duration-200 ease-in-out hover:scale-110">
                                    <i class='fas fa-arrow-right text-2xl -rotate-45'></i>
                                </button>
                            
                                @if ($user->status === 'active')
                                    <div
                                        class="active_inactive_dot absolute top-2 right-2 w-[0.6rem] h-[0.6rem] bg-[--border-success] rounded-full">
                                    </div>
                                    <div
                                        class="active_inactive absolute text-[--border-success] top-1 right-2 h-[1rem]">
                                        Active</div>
                                @else
                                    <div
                                        class="active_inactive_dot absolute top-2 right-2 w-[0.6rem] h-[0.6rem] bg-[--border-error] rounded-full">
                                    </div>
                                    <div
                                        class="active_inactive absolute text-[--border-error] top-1 right-2 h-[1rem]">
                                        In Active</div>
                                @endif
                            </div> --}}
                            <div data-user='{{ $user }}'
                                class="contextMenuToggle modalToggle card relative border border-gray-600 shadow rounded-xl min-w-[100px] h-[8rem] flex gap-4 p-4 cursor-pointer overflow-hidden fade-in">
                                <x-card :data="[
                                    'image' => $user->profile_picture == 'default_avatar.png' ? asset('images/default_avatar.png') : asset('storage/uploads/images/' . $user->profile_picture),
                                    'name' => $user->name,
                                    'status' => $user->status,
                                    'details' => [
                                        'Username' => $user->username,
                                        'Role' => $user->role
                                    ]
                                ]"/>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="no-article-message w-full h-full flex flex-col items-center justify-center gap-2">
                        <h1 class="text-md text-[--secondary-text] capitalize">No User yet</h1>
                        <a href="{{ route('users.create') }}"
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
        let currentUserRole = '{{Auth::user()->role}}';

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
            console.log(e);

            let ac_in_btn_context = document.getElementById('ac_in_btn_context');
            let ac_in_context = document.getElementById('ac_in_context');
            let item = e.target.closest('.modalToggle');
            let user = JSON.parse(item.dataset.user);

            ac_in_context.classList.add('hidden');

            if (ac_in_btn_context) {
                if (user.role == currentUserRole) {
                } else if (currentUserRole == "owner" && (user.role == "developer" || user.role == "owner")) {
                } else if (currentUserRole == "admin" && (user.role == "developer" || user.role == "owner" || user.role == "admin")) {
                } else {
                    if (user.status === 'active') {
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
                    user_id_context.value = user.id;
                    user_status_context.value = user.status;
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
                generateModal(item)
            })
        })

        function generateModal(item) {
            let user = JSON.parse(item.dataset.user);
            let userImage = document.getElementById('userImage');
            let name = document.getElementById('name');
            let username = document.getElementById('username');
            let role = document.getElementById('role');
            let ac_in_btn = document.getElementById('ac_in_btn');
            let user_id = document.getElementById('user_id');
            let user_status = document.getElementById('user_status');
            let active_inactive_dot_modal = document.getElementById('active_inactive_dot_modal');
            let ac_in_modal = document.getElementById('ac_in_modal');

            ac_in_modal.classList.add("hidden");
            
            if (user.role == currentUserRole) {
            } else if (currentUserRole == "owner" && (user.role == "developer" || user.role == "owner")) {
            } else if (currentUserRole == "admin" && (user.role == "developer" || user.role == "owner" || user.role == "admin")) {
            } else {
                ac_in_modal.classList.remove("hidden");
            }

            if (user.profile_picture == "default_avatar.png") {
                userImage.src = `images/default_avatar.png`
            } else {
                userImage.src = `storage/uploads/images/${user.profile_picture}`
            }

            if (user.status === 'active') {
                ac_in_btn.classList.add('bg-[--danger-color]')
                ac_in_btn.classList.remove('bg-[--success-color]')
                ac_in_btn.classList.add('hover:bg-[--h-danger-color]')
                ac_in_btn.classList.remove('hover:bg-[--h-success-color]')
                ac_in_btn.textContent = 'In Active'
                active_inactive_dot_modal.classList.remove('bg-[--border-error]')
                active_inactive_dot_modal.classList.add('bg-[--border-success]')
            } else {
                ac_in_btn.classList.remove('bg-[--danger-color]')
                ac_in_btn.classList.remove('hover:bg-[--h-danger-color]')
                ac_in_btn.classList.add('hover:bg-[--h-success-color]')
                ac_in_btn.classList.add('bg-[--success-color]')
                ac_in_btn.textContent = 'Active'
                active_inactive_dot_modal.classList.add('bg-[--border-error]')
                active_inactive_dot_modal.classList.remove('bg-[--border-success]')
            }

            user_id.value = user.id
            user_status.value = user.status
            name.textContent = user.name
            username.textContent = user.username
            role.textContent = user.role

            openUserModal()
        }

        document.getElementById('userModal').addEventListener('click', (e) => {
            if (e.target.id === 'userModal') {
                closeUserModal()
            }
        })

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && isModalOpened) {
                closeUserModal()
                closeContextMenu()
            }
        })

        function openUserModal() {
            isModalOpened = true;
            document.getElementById('userModal').classList.remove('hidden');
            closeAllDropdowns();
            closeContextMenu()
        }

        function closeUserModal() {
            document.getElementById('userModal').classList.add('hidden');
        }
    </script>
@endsection
