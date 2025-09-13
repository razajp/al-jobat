@extends('app')
@section('title', 'Show Users | ' . app('company')->name)
@section('content')
    @php
        $searchFields = [
            "Name" => [
                "id" => "name",
                "type" => "text",
                "placeholder" => "Enter name",
                "dataFilterPath" => "name",
            ],
            "Username" => [
                "id" => "username",
                "type" => "text",
                "placeholder" => "Enter username",
                "dataFilterPath" => "details.Username",
            ],
            'Role' => [
                'id' => 'role',
                'type' => 'select',
                'options' => [
                    'owner' => ['text' => 'Owner'],
                    'admin' => ['text' => 'Admin'],
                    'accountant' => ['text' => 'Accountant'],
                    'store_keeper' => ['text' => 'Store Keeper '],
                    'guest' => ['text' => 'Guest'],
                ],
                'dataFilterPath' => 'details.Role',
            ],
            'Status' => [
                'id' => 'status',
                'type' => 'select',
                'options' => [
                    'active' => ['text' => 'Active'],
                    'in_active' => ['text' => 'In Active'],
                ],
                'dataFilterPath' => 'status',
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
    <!-- Main Content -->
    <div>

        <div class="w-[80%] mx-auto">
            <x-search-header heading="Users" :search_fields=$searchFields/>
        </div>

        <section class="text-center mx-auto ">
            <div
                class="show-box mx-auto w-[80%] h-[70vh] bg-[var(--secondary-bg-color)] border border-[var(--glass-border-color)]/20 rounded-xl shadow overflow-y-auto pt-8.5 relative">
                <x-form-title-bar title="Show Users" />

                @if (count($users) > 0)
                    <div class="absolute bottom-0 right-0 flex items-center justify-between gap-2 w-fll z-50 p-3 w-full pointer-events-none">
                        <x-section-navigation-button direction="right" id="info" icon="fa-info" />
                        <x-section-navigation-button link="{{ route('users.create') }}" title="Add New User" icon="fa-plus" />
                    </div>

                    <div class="details h-full z-40">
                        <div class="container-parent h-full overflow-y-auto my-scrollbar-2">
                            <div class="card_container px-3 h-full flex flex-col">
                                <div id="table-head" class="grid grid-cols-4 bg-[var(--h-bg-color)] rounded-lg font-medium py-2 hidden mt-4 mx-2">
                                    <div class="text-left pl-5 col-span-2">Name</div>
                                    <div class="text-left pl-5">Username</div>
                                    <div class="text-center">Role</div>
                                    <div class="text-right pr-5">Status</div>
                                </div>
                                <p id="noItemsError" style="display: none" class="text-sm text-[var(--border-error)] mt-3">No items found</p>
                                <div>
                                    <div class="search_container grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 overflow-y-auto grow my-scrollbar-2">
                                        {{-- class="search_container overflow-y-auto grow my-scrollbar-2"> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="no-article-message w-full h-full flex flex-col items-center justify-center gap-2">
                        <h1 class="text-md text-[var(--secondary-text)] capitalize">No User yet</h1>
                        <a href="{{ route('users.create') }}"
                            class="text-md bg-[var(--primary-color)] text-[var(--text-color)] px-4 py-2 rounded-md hover:bg-blue-600 transition-all duration-300 ease-in-out uppercase font-semibold">Add
                            New</a>
                    </div>
                @endif
            </div>
        </section>
    </div>

    <script>
        let currentUserRole = '{{ Auth::user()->role }}';
        let authLayout = '{{ $authLayout }}';

        function createRow(data) {
            return `
            <div id="${data.id}" oncontextmenu='${data.oncontextmenu || ""}' onclick='${data.onclick || ""}'
                class="item row relative group grid text- grid-cols-8 border-b border-[var(--h-bg-color)] items-center py-2 cursor-pointer hover:bg-[var(--h-secondary-bg-color)] transition-all fade-in ease-in-out"
                data-json='${JSON.stringify(data)}'>

                <span class="text-left pl-5 col-span-2">${data.name}</span>
                <span class="text-left pl-5">${data.details["Username"]}</span>
                <span class="text-center capitalize">${data.details["Role"]}</span>
                <span class="text-right pr-5 capitalize ${data.user.status === 'active' ? 'text-[var(--border-success)]' : 'text-[var(--border-error)]'}">${data.user.status}</span>
            </div>`;
        }

        const fetchedData = @json($users);
        let allDataArray = fetchedData.map(item => {
            return {
                id: item.id,
                uId: item.id,
                image: item.profile_picture == 'default_avatar.png' ? '/images/default_avatar.png' : `/storage/uploads/images/${item.profile_picture}`,
                name: item.name,
                status: item.status,
                details: {
                    'Username': item.username,
                    'Role': item.role,
                },
                oncontextmenu: "generateContextMenu(event)",
                onclick: "generateModal(this)",
                profile: true,
                visible: true,
            };
        });

        const activeUser = allDataArray.filter(user => user.status === 'active');

        let infoDom = document.getElementById('info').querySelector('span');
        infoDom.textContent = `Total Users: ${allDataArray.length} | Active: ${activeUser.length}`;

        function generateContextMenu(e) {
            e.preventDefault();
            let item = e.target.closest('.item');
            let data = JSON.parse(item.dataset.json);

            let contextMenuData = {
                data: data,
                x: e.pageX,
                y: e.pageY,
                action: "{{ route('update-user-status') }}",
            };

            if (currentUserRole != data.details['Role']) {
                contextMenuData.forceStatusBtn = true;
            }

            if ((currentUserRole == 'admin' || currentUserRole == 'developer' || currentUserRole == 'owner') && currentUserRole != data.details['Role']) {
                contextMenuData.actions = [
                    {id: 'reset-password', text: 'Reset Password', onclick: `generateResetPasswordModel(${JSON.stringify(data)})`},
                ];
            }

            createContextMenu(contextMenuData);
        }

        function generateModal(item) {
            let data = JSON.parse(item.dataset.json);

            let modalData = {
                id: 'modalForm',
                uId: data.id,
                status: data.status,
                method: "POST",
                action: "{{ route('update-user-status') }}",
                image: data.image,
                name: data.name,
                details: {
                    'Username': data.details['Username'],
                    'Role': data.details['Role'],
                },
                profile: true,
            }

            if (currentUserRole != data.details['Role']) {
                modalData.forceStatusBtn = true;
            }

            if ((currentUserRole == 'admin' || currentUserRole == 'developer' || currentUserRole == 'owner') && currentUserRole != data.details['Role']) {
                modalData.bottomActions = [
                    {id: 'reset-password', text: 'Reset Password', onclick: `generateResetPasswordModel(${JSON.stringify(data)})`},
                ];
            }

            createModal(modalData);
        }

        function generateResetPasswordModel(data) {
            let modalData = {
                id: 'resetPasswordModalForm',
                class: 'h-auto',
                method: 'POST',
                action: '{{ route("users.reset-password") }}',
                name: 'Reset Password',
                fields: [
                    {
                        category: 'input',
                        label: 'Username',
                        value: data.details['Username'],
                        disabled: true,
                    },
                    {
                        category: 'input',
                        type: 'hidden',
                        name: 'user_id',
                        value: data.id,
                    },
                    {
                        category: 'input',
                        label: 'Password',
                        name: 'password',
                        id: 'password',
                        type: 'password',
                        placeholder: 'Enter new password',
                        data_validate: 'required|min:4|alphanumeric|lowercase',
                        oninput: 'check(this)',
                        required: true,
                    },
                ],
                fieldsGridCount: '2',
                bottomActions: [
                    {id: 'reset-password-btn', text: 'Reset Password', type: 'submit'}
                ]
            }

            createModal(modalData);
        }

        function check(elem) {
            console.log(elem);
        }
    </script>
@endsection
