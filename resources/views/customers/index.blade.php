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
            'Status' => [
                'id' => 'status',
                'type' => 'select',
                'options' => [
                    'active' => ['text' => 'Active'],
                    'in_active' => ['text' => 'In Active'],
                ],
                'dataFilterPath' => 'user.status',
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
    <div>
        <div class="w-[80%] mx-auto">
            <x-search-header heading="Customers" :search_fields=$searchFields />
        </div>

        <!-- Main Content -->
        <section class="text-center mx-auto">
            <div
                class="show-box mx-auto w-[80%] h-[70vh] bg-[var(--secondary-bg-color)] rounded-xl shadow overflow-y-auto pt-8.5 relative">
                <x-form-title-bar title="Show Customers" changeLayoutBtn layout="{{ $authLayout }}" />

                @if (count($customers) > 0)
                    <div class="absolute bottom-0 right-0 flex items-center justify-between gap-2 w-fll z-50 p-3 w-full pointer-events-none">
                        <x-section-navigation-button direction="right" id="info" icon="fa-info" />
                        <x-section-navigation-button link="{{ route('customers.create') }}" title="Add New Customer"
                            icon="fa-plus" />
                    </div>

                    <div class="details h-full z-40">
                        <div class="container-parent h-full overflow-y-auto my-scrollbar-2">
                            <div class="card_container py-0 p-3 h-full flex flex-col">
                                <div id="table-head" class="grid grid-cols-8 bg-[var(--h-bg-color)] rounded-lg font-medium py-2 hidden mt-4 mx-2">
                                    <div class="text-left pl-5 col-span-2">Customer</div>
                                    <div class="text-left pl-5">Urdu Title</div>
                                    <div class="text-center">Category</div>
                                    <div class="text-center">City</div>
                                    <div class="text-center">Phone</div>
                                    <div class="text-right">Balance</div>
                                    <div class="text-right pr-5">Status</div>
                                </div>
                                <p id="noItemsError" style="display: none" class="text-sm text-[var(--border-error)] mt-3">No items found</p>
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
                profile: true,
                visible: true,
            };
        });

        const activeCustomers = allDataArray.filter(customer => customer.user.status === 'active');

        let infoDom = document.getElementById('info').querySelector('span');
        infoDom.textContent = `Total Customers: ${allDataArray.length} | Active: ${activeCustomers.length}`;

        function generateContextMenu(e) {
            e.preventDefault();
            let item = e.target.closest('.item');
            let data = JSON.parse(item.dataset.json);

            let contextMenuData = {
                item: item,
                data: data,
                x: e.pageX,
                y: e.pageY,
                action: "{{ route('update-user-status') }}",
                actions: [
                    {id: 'edit', text: 'Edit Customer'}
                ],
            };

            createContextMenu(contextMenuData);
        }

        function generateModal(item) {
            let data = JSON.parse(item.dataset.json);

            let modalData = {
                id: 'modalForm',
                method: "POST",
                action: "{{ route('update-user-status') }}",
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
                profile: true,
                bottomActions: [
                    {id: 'edit', text: 'Edit Customer', dataId: data.id}
                ],
            }

            createModal(modalData);
        }
    </script>
@endsection