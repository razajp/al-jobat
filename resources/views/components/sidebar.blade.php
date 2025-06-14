<!-- Logout Modal -->
<div id="logoutModal" class="hidden fixed inset-0 z-[99] flex items-center justify-center bg-[var(--overlay-color)] text-xs md:text-sm fade-in">
    <!-- Modal Content -->
    <div class="bg-[var(--secondary-bg-color)] rounded-xl shadow-lg w-80 md:w-full md:max-w-lg p-6 relative">
        <!-- Close Button -->
        <button onclick="closeLogoutModal()"
            class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-all duration-300 ease-in-out cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Modal Body -->
        <div class="modal_body flex items-start">
            <div class="w-1/3 h-1/3 md:w-1/5 md:h-1/5">
                <img src="{{ asset('images/error_icon.png') }}" alt=""
                    class="w-full h-full object-cover">
            </div>
            <div class="content ml-5">
                <h2 class="text-lg md:text-xl font-semibold text-[var(--text-color)]">Logout Account</h2>
                <p class="text-[var(--secondary-text)] mt-1 mb-4 md:mt-2 md:mb-6">Are you sure you want to logout? All of your data
                    will be permanently removed. This action cannot be undone.</p>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex justify-end space-x-3">
            <!-- Cancel Button -->
            <button onclick="closeLogoutModal()"
                class="px-4 py-2 bg-[var(--secondary-bg-color)] border text-[var(--secondary-text)] rounded-md hover:bg-[var(--bg-color)] transition-all duration-300 ease-in-out cursor-pointer">Cancel</button>

            <!-- Logout Form -->
            <form id="logoutForm" method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="px-4 py-2 bg-[var(--danger-color)] text-white rounded-md hover:bg-[var(--h-danger-color)] transition-all duration-300 ease-in-out cursor-pointer">Logout</button>
            </form>
        </div>
    </div>
</div>
<div class="relative w-full md:w-auto md:z-40">
    <aside class="bg-[var(--secondary-bg-color)] w-full md:w-16 flex justify-between md:flex-col items-center px-5 py-3 md:px-0 md:py-3 h-full md:h-screen transition-all duration-300 ease-in-out fade-in relative z-40">
        <!-- Logo -->
        <a href="/"
            class="md:mb-6 text-[var(--text-color)] p-3 w-10 h-10 flex items-center justify-center group cursor-normal relative">
            <h1 class="font-bold text-2xl text-[var(--primary-color)] m-0">AJ</h1>
        </a>
    
        <!-- Mobile Menu Toggle Button -->
        <button id="menuToggle" type="button" class="md:hidden flex items-center p-2 text-[var(--text-color)] cursor-pointer">
            <i class="fas fa-bars text-xl transition-all 0.5s ease-in-out"></i>
        </button>

        <!-- Navigation Menu -->
        <nav class="space-y-4 hidden md:flex flex-col ">
            <div class="relative group">
                <x-nav-link-item 
                    label="Home" 
                    icon="fas fa-home"
                    href="/"
                />
            </div>
            
            @if (in_array(Auth::user()->role, ['developer', 'owner', 'admin', 'accountant']))
                <div class="relative group">
                    <x-nav-link-item 
                        label="Users" 
                        icon="fas fa-user"
                        includesDropdown
                        :items="[
                            ['type' => 'link', 'href' => route('users.index'), 'label' => 'Show Users'],
                            ['type' => 'link', 'href' => route('users.create'), 'label' => 'Add User']
                        ]"
                    />
                </div>
            @endif
            
            @if (in_array(Auth::user()->role, ['developer', 'owner', 'admin', 'accountant']))
                <div class="relative group">
                    <x-nav-link-item 
                        label="Suppliers" 
                        icon="fas fa-truck"
                        :activatorTags="['vouchers']"
                        includesDropdown
                        :items="[
                            [
                                'label' => 'Supplier',
                                'type' => 'group',
                                'children' => [
                                    ['type' => 'link', 'href' => route('suppliers.index'), 'label' => 'Show Suppliers'],
                                    ['type' => 'link', 'href' => route('suppliers.create'), 'label' => 'Add Supplier'],
                                ]
                            ],
                            [
                                'label' => 'Voucher',
                                'type' => 'group',
                                'children' => [
                                    ['type' => 'link', 'href' => route('vouchers.index'), 'label' => 'Show Vouchers'],
                                    ['type' => 'link', 'href' => route('vouchers.create'), 'label' => 'Generater Voucher'],
                                ]
                            ]
                        ]"
                    />
                </div>
            @endif
            
            @if (in_array(Auth::user()->role, ['developer', 'owner', 'admin', 'accountant']))
                <div class="relative group">
                    <x-nav-link-item 
                        label="Customers" 
                        :activatorTags="['customer-payments']"
                        icon="fas fa-user-tag"
                        includesDropdown
                        :items="[
                            [
                                'label' => 'Customer',
                                'type' => 'group',
                                'children' => [
                                    ['type' => 'link', 'href' => route('customers.index'), 'label' => 'Show Customers'],
                                    ['type' => 'link', 'href' => route('customers.create'), 'label' => 'Add Customer'],
                                ]
                            ],
                            [
                                'label' => 'Payment',
                                'type' => 'group',
                                'children' => [
                                    ['type' => 'link', 'href' => route('customer-payments.index'), 'label' => 'Show Payments'],
                                    ['type' => 'link', 'href' => route('customer-payments.create'), 'label' => 'Add Payment'],
                                ]
                            ]
                        ]"
                    />
                </div>
            @endif
            
            @if (in_array(Auth::user()->role, ['developer', 'owner', 'admin', 'accountant', 'store_keeper']))
                <div class="relative group">
                    <x-nav-link-item 
                        label="Articles" 
                        :activatorTags="['physical-quantities']"
                        icon="fas fa-tshirt"
                        includesDropdown
                        :items="[
                            [
                                'label' => 'Article',
                                'type' => 'group',
                                'children' => [
                                    ['type' => 'link', 'href' => route('articles.index'), 'label' => 'Show Articles'],
                                    ['type' => 'link', 'href' => route('articles.create'), 'label' => 'Add Article'],
                                ]
                            ],
                            [
                                'label' => 'Physical Quantity',
                                'type' => 'group',
                                'children' => [
                                    ['type' => 'link', 'href' => route('physical-quantities.index'), 'label' => 'Show'],
                                    ['type' => 'link', 'href' => route('physical-quantities.create'), 'label' => 'Add'],
                                ]
                            ]
                        ]"
                    />
                </div>
            @endif
            
            @if (in_array(Auth::user()->role, ['developer', 'owner', 'admin', 'accountant']))
                <div class="relative group">
                    <x-nav-link-item 
                        label="Orders"
                        :activatorTags="['payment-programs']"
                        icon="fas fa-cart-shopping"
                        includesDropdown
                        :items="[
                            [
                                'label' => 'Order',
                                'type' => 'group',
                                'children' => [
                                    ['type' => 'link', 'href' => route('orders.index'), 'label' => 'Show Orders'],
                                    ['type' => 'link', 'href' => route('orders.create'), 'label' => 'Generate Order'],
                                ]
                            ],
                            [
                                'label' => 'Payment Program',
                                'type' => 'group',
                                'children' => [
                                    ['type' => 'link', 'href' => route('payment-programs.index'), 'label' => 'Show Programs'],
                                    ['type' => 'link', 'href' => route('payment-programs.create'), 'label' => 'Add Program'],
                                ]
                            ]
                        ]"
                    />
                </div>
            @endif
            
            @if (in_array(Auth::user()->role, ['developer', 'owner', 'admin', 'accountant']))
                <div class="relative group">
                    <x-nav-link-item 
                        label="Shipments"
                        icon="fas fa-box-open"
                        includesDropdown
                        :items="[
                            ['type' => 'link', 'href' => route('shipments.index'), 'label' => 'Show Shipments'],
                            ['type' => 'link', 'href' => route('shipments.create'), 'label' => 'Generate Shipment'],
                        ]"
                    />
                </div>
            @endif
            
            @if (in_array(Auth::user()->role, ['developer', 'owner', 'admin', 'accountant']))
                <div class="relative group">
                    <x-nav-link-item 
                        label="Expenses"
                        svgIcon='
                            <svg class="fill-[var(--text-color)]" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                x="0px" y="0px" viewBox="0 0 578 578" style="enable-background:new 0 0 578 578;" xml:space="preserve">
                                <g>
                                    <path d="M523.7,308.6c-30.7,0-59.6,0-88.6,0c-36.9,0-51.8,11.3-56.3,47.9c-2.2,18.2-0.6,37.5,3.2,55.5
                                    c4.9,23.3,20.7,33.5,44.5,33.7c31.8,0.2,63.6,0,95.1,0c0,24.6,2.4,49.1-0.7,72.8c-2.8,20.8-23.3,34.7-44.6,34.7
                                    c-141.1,0.2-282.3,0.2-423.4,0c-26.2,0-47.6-21.4-47.7-47.6c-0.3-85.7-0.3-171.3,0-257c0.1-26.2,21.5-47.5,47.7-47.6
                                    c141.1-0.2,282.3-0.2,423.4,0c24.2,0,45.5,18.9,47.1,42.9C524.9,265,523.7,286.2,523.7,308.6z" />
                                    <path
                                        d="M90,181.2c58.4-51.1,114.1-99.9,169.9-148.6c9-7.9,19.3-10.5,30.5-5.2c11.3,5.3,17.7,14.3,18,26.8
                                    c0.3,13.8,4.4,30.1-1.5,40.8c-5.4,9.8-21.7,13.9-33.4,19.9c-36.2,18.3-73.5,34.7-108.7,54.9C141.5,183.1,117.8,181,90,181.2z" />
                                    <path d="M484.3,426.1c-19.1,0-38.3,0.1-57.4,0c-15.8-0.1-25.6-6.5-27.2-22.1c-1.8-18-1.8-36.5,0.2-54.4
                                    c1.6-14.9,11.4-21.2,26.6-21.2c38.3-0.1,76.5-0.1,114.8,0c19.3,0.1,31.3,11.9,31.6,31c0.2,12.2,0.2,24.4,0,36.6
                                    c-0.3,17.7-12.4,29.9-30,30.1C523.3,426.3,503.8,426.1,484.3,426.1z M456,416.3c21.1-0.3,38.6-18,38.6-39.1
                                    c0-21.6-18.1-39.5-39.8-39.2c-21.6,0.3-39.2,18.8-38.6,40.3C416.9,399.4,434.9,416.7,456,416.3z" />
                                    <path d="M395.4,181c-68.9,0-137.4,0-208.3,0c4.1-2.7,6.3-4.4,8.8-5.7c51.2-25.7,102.5-51.3,153.9-76.8c26.1-12.9,46.8,0,46.9,29
                                    c0,16.7-0.1,33.4-0.2,50.1C396.5,178.4,396,179.2,395.4,181z" />
                                    <path d="M455,396.7c-10.6-0.2-19.2-9-19.1-19.6c0.1-11.1,9.4-20,20.5-19.4c10.5,0.5,18.9,9.7,18.5,20.2
                                    C474.6,388.4,465.6,396.9,455,396.7z" />
                                </g>
                            </svg>
                        '
                        includesDropdown
                        :items="[
                            ['type' => 'link', 'href' => route('expenses.index'), 'label' => 'Show Expenses'],
                            ['type' => 'link', 'href' => route('expenses.create'), 'label' => 'Add Expense'],
                        ]"
                    />
                </div>
            @endif

            {{-- <svg class="fill-white size-6" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                viewBox="0 0 192 192" style="enable-background:new 0 0 192 192;" xml:space="preserve">
            <g>
                <path d="M159.3,101.29c-8.28,0-16.08,0-23.89,0c-9.94,0-13.97,3.04-15.19,12.92c-0.6,4.9-0.17,10.12,0.86,14.97
                    c1.33,6.28,5.57,9.04,12.01,9.08c8.58,0.05,17.15,0.01,25.65,0.01c0,6.64,0.65,13.24-0.2,19.64c-0.75,5.62-6.29,9.35-12.04,9.36
                    c-38.06,0.05-76.12,0.06-114.17,0c-7.07-0.01-12.84-5.78-12.86-12.84c-0.08-23.1-0.08-46.2,0-69.3
                    c0.02-7.07,5.79-12.82,12.86-12.83c38.06-0.06,76.12-0.06,114.17,0c6.53,0.01,12.26,5.11,12.71,11.57
                    C159.61,89.53,159.3,95.25,159.3,101.29z"/>
                <path d="M42.34,66.92c15.75-13.78,30.78-26.94,45.81-40.08c2.44-2.13,5.2-2.82,8.22-1.41c3.04,1.42,4.78,3.86,4.86,7.23
                    c0.09,3.73,1.19,8.13-0.4,11c-1.46,2.64-5.84,3.75-9.02,5.36c-9.77,4.93-19.83,9.36-29.3,14.81
                    C56.21,67.45,49.83,66.89,42.34,66.92z"/>
                <path d="M148.65,132.97c-5.16,0-10.32,0.03-15.48-0.01c-4.27-0.03-6.9-1.76-7.33-5.96c-0.49-4.85-0.48-9.83,0.05-14.67
                    c0.43-4.01,3.08-5.72,7.18-5.73c10.32-0.03,20.64-0.04,30.95,0c5.21,0.02,8.44,3.22,8.52,8.35c0.05,3.29,0.06,6.59,0,9.88
                    c-0.09,4.76-3.34,8.05-8.08,8.12C159.19,133.03,153.92,132.97,148.65,132.97z M141.03,130.34c5.69-0.08,10.4-4.86,10.41-10.55
                    c0-5.83-4.89-10.65-10.72-10.57c-5.82,0.08-10.57,5.06-10.4,10.88C130.49,125.78,135.34,130.43,141.03,130.34z"/>
                <path d="M124.68,66.89c-18.59,0-37.05,0-56.16,0c1.1-0.72,1.7-1.2,2.37-1.54c13.82-6.92,27.64-13.84,41.49-20.71
                    c7.05-3.49,12.63,0,12.64,7.81c0.01,4.51-0.02,9.01-0.05,13.52C124.98,66.18,124.85,66.38,124.68,66.89z"/>
                <path d="M140.77,125.04c-2.85-0.06-5.17-2.44-5.16-5.29c0.02-3,2.54-5.39,5.53-5.24c2.84,0.14,5.1,2.61,5,5.45
                    C146.04,122.81,143.62,125.1,140.77,125.04z"/>
            </g>
            </svg> --}}
            
            @if (in_array(Auth::user()->role, ['developer', 'owner', 'admin', 'accountant']))
                <div class="relative group">
                    <x-nav-link-item 
                        label="Invoices"
                        icon="fas fa-receipt"
                        includesDropdown
                        :activatorTags="['invoices', 'cargos', 'bilties']"
                        :items="[
                            [
                                'label' => 'Invoices',
                                'type' => 'group',
                                'children' => [
                                    ['type' => 'link', 'href' => route('invoices.index'), 'label' => 'Show Invoices'],
                                    ['type' => 'link', 'href' => route('invoices.create'), 'label' => 'Generate Invoice'],
                                ]
                            ],
                            [
                                'label' => 'Cargos',
                                'type' => 'group',
                                'children' => [
                                    ['type' => 'link', 'href' => route('cargos.index'), 'label' => 'Show Lists'],
                                    ['type' => 'link', 'href' => route('cargos.create'), 'label' => 'Generate List'],
                                ]
                            ],
                            [
                                'label' => 'Bilties',
                                'type' => 'group',
                                'children' => [
                                    ['type' => 'link', 'href' => route('bilties.index'), 'label' => 'Show Bilties'],
                                    ['type' => 'link', 'href' => route('bilties.create'), 'label' => 'Add Bilty'],
                                ]
                            ]
                        ]"
                    />
                </div>
            @endif
            
            @if (in_array(Auth::user()->role, ['developer', 'owner', 'admin', 'accountant']))
                <div class="relative group">
                    <x-nav-link-item 
                        label="Bank-Accounts" 
                        icon="fas fa-university"
                        includesDropdown
                        :items="[
                            ['type' => 'link', 'href' => route('bank-accounts.index'), 'label' => 'Show Accounts'],
                            ['type' => 'link', 'href' => route('bank-accounts.create'), 'label' => 'Add Account'],
                        ]"
                    />
                </div>
            @endif
            
            @if (in_array(Auth::user()->role, ['developer', 'owner', 'admin', 'accountant']))
                <div class="relative group">
                    <x-nav-link-item 
                        label="Fabrics" 
                        icon="fas fa-university"
                        includesDropdown
                        :items="[
                            ['type' => 'link', 'href' => route('fabrics.index'), 'label' => 'Show Fabrics'],
                            ['type' => 'link', 'href' => route('fabrics.create'), 'label' => 'Add Fabric'],
                        ]"
                    />
                </div>
            @endif
            
            @if (in_array(Auth::user()->role, ['developer', 'owner', 'admin', 'accountant']))
                <div class="relative group">
                    <x-nav-link-item 
                        label="Employees" 
                        icon="fas fa-university"
                        includesDropdown
                        :items="[
                            ['type' => 'link', 'href' => route('employees.index'), 'label' => 'Show Employees'],
                            ['type' => 'link', 'href' => route('employees.create'), 'label' => 'Add Employee'],
                        ]"
                    />
                </div>
            @endif
        </nav>
    
        <div class="relative hidden md:flex group md:pt-3 md:ml-0 md:mt-auto dropdown-trigger">
            <!-- User Avatar -->
            <button type="button" class="w-10 h-10 ml-1.5 mb-1 flex items-center justify-center rounded-[41.5%] cursor-pointer transition-all duration-300 ease-in-out text-[var(--text-color)] font-semibold text-lg overflow-hidden">
                @if (Auth::user()->profile_picture == 'default_avatar.png')
                    <img src="{{ asset('images/default_avatar.png') }}" class="w-full h-full object-cover" alt="Avatar">
                @else
                    <img src="{{ asset('storage/uploads/images/' . auth()->user()->profile_picture) }}" class="w-full h-full object-cover" alt="Avatar">
                @endif
                <span
                    class="absolute shadow-xl capitalize text-nowrap left-18 bottom-1.5 bg-[var(--h-secondary-bg-color)] text-[var(--text-color)] border border-gray-600 text-sm rounded-lg px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
                    {{ Auth::user()->name }}
                </span>
            </button>
    
            <!-- Dropdown Menu -->
            <div class="dropdownMenu text-sm absolute bottom-0 left-16 hidden border border-gray-600 w-48 bg-[var(--h-secondary-bg-color)] text-[var(--text-color)] shadow-lg rounded-2xl opacity-0 transform scale-95 transition-all duration-300 ease-in-out z-50">
                <ul class="p-2">
                    <!-- Add Setups -->
                    <li>
                        <a href="{{route('addSetup')}}"
                            class="block px-4 py-2 hover:bg-[var(--h-bg-color)] rounded-lg transition-all duration-200 ease-in-out cursor-pointer">
                            <i class="fas fa-cog text-[var(--secondary-color)] mr-3"></i>
                            Setups
                        </a>
                    </li>
                    <!-- Theme Toggle -->
                    <li>
                        <button id="themeToggle"
                            class="flex items-center w-full px-4 py-2 text-left hover:bg-[var(--h-bg-color)] rounded-lg transition-all duration-200 ease-in-out cursor-pointer">
                            <i class="fas fa-moon text-[var(--secondary-color)] mr-3"></i>
                            Theme
                        </button>
                    </li>
                    <!-- Logout Button -->
                    <li>
                        <button onclick="openLogoutModal()"
                            class="block w-full text-left px-4 py-2 text-[var(--border-error)] hover:bg-[var(--bg-error)] hover:text-[var(--text-error)] rounded-lg transition-all duration-200 ease-in-out cursor-pointer">
                            <i class="fas fa-sign-out-alt mr-3"></i>
                            Logout
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </aside>
    {{-- mobile menu --}}
    <div id="mobileMenuOverlay" class="mobileMenuOverlay w-screen h-screen bg-[var(--overlay-color)] opacity-zero opacity-transition pointer-events-none fixed z-30">
        <div id="mobileMenu" class="fixed md:hidden w-full bg-[var(--secondary-bg-color)] z-30 flex flex-col items-start justify-start p-4 space-y-4 transform -translate-y-full transition-all 0.5s ease-in-out">
            <!-- Main Menu Items -->
            <div class="flex flex-col space-y-2 w-full">
                <x-mobile-menu-item href="/" title="Home" active="{{ request()->is('home') }}" />

                <x-mobile-menu-item 
                    title="Users" 
                    includesDropdown
                    :dropdown="[
                        ['href' => route('users.index'), 'title' => 'Show Users'],
                        ['href' => route('users.create'), 'title' => 'Add User']
                    ]" 
                />

                <x-mobile-menu-item 
                    title="Suppliers" 
                    includesDropdown
                    :dropdown="[
                        ['href' => route('suppliers.index'), 'title' => 'Show Suppliers'],
                        ['href' => route('suppliers.create'), 'title' => 'Add Supplier']
                    ]" 
                />

                <x-mobile-menu-item 
                    title="Customer" 
                    includesDropdown
                    :dropdown="[
                        ['href' => route('customers.index'), 'title' => 'Show Customers'],
                        ['href' => route('customers.create'), 'title' => 'Add Customer']
                    ]" 
                />

                <x-mobile-menu-item 
                    title="Articles" 
                    includesDropdown
                    :dropdown="[
                        ['href' => route('articles.index'), 'title' => 'Show Articles'],
                        ['href' => route('articles.create'), 'title' => 'Add Article']
                    ]" 
                />

                <x-mobile-menu-item
                    title="Orders" 
                    includesDropdown
                    :dropdown="[
                        ['href' => route('orders.index'), 'title' => 'Show Orders'],
                        ['href' => route('payment-programs.index'), 'title' => 'Show Payment Prg.'],
                        ['href' => route('orders.create'), 'title' => 'Generate Order'],
                        ['href' => route('payment-programs.create'), 'title' => 'Add Payment Prg.'],
                    ]"
                />

                <x-mobile-menu-item
                    title="Shipments" 
                    includesDropdown
                    :dropdown="[
                        ['href' => route('shipments.index'), 'title' => 'Show Shipments'],
                        ['href' => route('shipments.create'), 'title' => 'Generate Shipment'],
                    ]"
                />

                <x-mobile-menu-item 
                    title="Physical Quantities" 
                    includesDropdown
                    :dropdown="[
                        ['href' => route('physical-quantities.index'), 'title' => 'Show Phys. Quantity'],
                        ['href' => route('physical-quantities.create'), 'title' => 'Add Phys. Quantity'],
                    ]"
                />

                <x-mobile-menu-item 
                    title="Invoices" 
                    includesDropdown
                    :dropdown="[
                        ['href' => route('invoices.index'), 'title' => 'Show Invoices'],
                        ['href' => route('invoices.create'), 'title' => 'Generate Invoice'],
                        ['href' => route('cargos.index'), 'title' => 'Show Cargo Lists'],
                        ['href' => route('cargos.create'), 'title' => 'Generate Cargo List'],
                        ['href' => route('bilties.index'), 'title' => 'Show Bilties'],
                        ['href' => route('bilties.create'), 'title' => 'Add Bilty'],
                    ]"
                />

                <x-mobile-menu-item 
                    title="Payments" 
                    includesDropdown
                    :dropdown="[
                        ['href' => route('customer-payments.index'), 'title' => 'Show Payments'],
                        ['href' => route('customer-payments.create'), 'title' => 'Add Payment'],
                    ]"
                />

                <x-mobile-menu-item 
                    title="Banks" 
                    includesDropdown
                    :dropdown="[
                        ['href' => route('bank-accounts.index'), 'title' => 'Show Banks'],
                        ['href' => route('bank-accounts.create'), 'title' => 'Add Bank'],
                    ]"
                />
                </div>

                <!-- Divider -->
                <div class="border-t border-gray-600 w-full my-4"></div>
            
                <!-- Profile Section -->
                <div class="flex items-center space-x-4 px-4">
                    @if (Auth::user()->profile_picture == 'default_avatar.png')
                            <img src="{{ asset('images/default_avatar.png') }}" alt="Avatar" class="w-10 h-10 rounded-[41.5%]">
                        @else
                            <img src="{{ asset('storage/uploads/images/' . auth()->user()->profile_picture) }}" alt="Avatar" class="w-10 h-10 rounded-[41.5%]">
                        @endif
                    <div>
                        <div class="text-[var(--text-color)] font-semibold capitalize">{{ Auth::user()->name }}</div>
                        <div class="text-gray-400 text-sm">username: {{ Auth::user()->username }}</div>
                    </div>
                </div>
            
                <!-- Additional Links -->
                <div class="flex flex-col space-y-2 w-full mt-2">
                    <x-mobile-menu-item href="{{ route('addSetup') }}" title="Setups" active="{{ request()->is('add-setup') }}" />
                    
                    <x-mobile-menu-item title="Theme" asButton="true" id="themeToggleMobile" />

                    <x-mobile-menu-item title="Logout" asButton="true" onclick="openLogoutModal()" />
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.querySelectorAll('.dropdown-toggle').forEach(button => {
        button.addEventListener('click', () => {
            // Close other open dropdowns
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                if (menu !== button.nextElementSibling) {
                    menu.classList.add('hidden');
                    menu.previousElementSibling.querySelector('i').classList.remove('rotate-180');
                }
            });

            // Toggle clicked dropdown
            const dropdownMenu = button.nextElementSibling;
            dropdownMenu.classList.toggle('hidden');
            button.querySelector('i').classList.toggle('rotate-180');
        });
    });

    function closeAllMobileMenuDropdowns() {
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.classList.add('hidden');
            menu.previousElementSibling.querySelector('i').classList.remove('rotate-180');
        });
    }

    const menuToggle = document.getElementById('menuToggle');
    const menuToggleIcon = document.querySelector('#menuToggle i');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
    const mobileMenu = document.getElementById('mobileMenu');

    menuToggle.addEventListener('click', () => {
        toggleMobileMenu();
    });

    function toggleMobileMenu(){
        closeAllMobileMenuDropdowns();
        
        // Toggle between bars and xmark icons
        menuToggleIcon.classList.toggle('fa-bars');
        menuToggleIcon.classList.toggle('fa-xmark');

        // Toggle menu visibility
        mobileMenu.classList.toggle('-translate-y-full');  // Moves out of view
        mobileMenu.classList.toggle('translate-y-0');      // Brings into view

        mobileMenuOverlay.classList.toggle('opacity-zero');
        mobileMenuOverlay.classList.toggle('pointer-events-none');
    }

    mobileMenuOverlay.addEventListener('mousedown', (e)=>{
        if (e.target.classList.contains("mobileMenuOverlay")) {
            toggleMobileMenu();
        }
    })

    const html = document.documentElement;
    const themeIcon = document.querySelector('#themeToggle i');
    const themeToggle = document.getElementById('themeToggle');
    const themeToggleMobile = document.getElementById('themeToggleMobile');
    let isLogoutModalOpened = false;
    
    themeToggle?.addEventListener('click', () => {
        themefunction();
    });

    themeToggleMobile?.addEventListener('click', () => {
        themefunction();
    });

    function themefunction() {
        changeTheme();

        // Get the current theme from the HTML element
        const currentTheme = $('html').attr('data-theme');

        // Send an AJAX request to update the theme in the database
        $.ajax({
            url: '/update-theme',  // Route to your controller
            type: 'POST',
            data: {
                theme: currentTheme,
                _token: $('meta[name="csrf-token"]').attr('content')  // CSRF token
            },
            success: function(response) {
                console.log('AJAX Response:', response);  // Console pe response dekhein
                
                // Check if messageBox exists
                if (messageBox) {
                    if (response.success) {
                        messageBox.innerHTML = `
                            <x-alert type="success" :messages="'${response.message}'" />
                        `;
                        messageBoxAnimation()
                    } else {
                        messageBox.innerHTML = `
                            <x-alert type="error" :messages="'Failed to update theme. Please try again later.'" />
                        `;
                        messageBoxAnimation()
                    }
                } else {
                    console.error('Element with ID "ajax-message" not found.');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                if (messageBox) {
                    messageBox.innerHTML = `
                        <x-alert type="error" :messages="'An error occurred while updating the theme. Please try again later.'" />
                    `;
                    messageBoxAnimation()
                } else {
                    console.error('Element with ID "ajax-message" not found.');
                }
            }
        });
    }

    function changeTheme() {
        const currentTheme = html.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        html.setAttribute('data-theme', newTheme);

        themeIcon?.classList.toggle('fa-sun');
        themeIcon?.classList.toggle('fa-moon');
    }

    document.getElementById('logoutModal').addEventListener('click', (e) => {
        if (e.target.id === 'logoutModal') {
            closeLogoutModal();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeLogoutModal();
        }
    });

    // Close any open dropdown when clicking anywhere else on the document
    document.addEventListener('mousedown', function(e) {
        // Check if the click is outside of any dropdown trigger or menu
        if (!e.target.closest('.dropdown-trigger') && !e.target.closest('.dropdownMenu')) {
            closeAllDropdowns();
        }
    });

    function openLogoutModal() {
        isLogoutModalOpened = true;
        document.getElementById('logoutModal').classList.remove('hidden');
        closeAllDropdowns();
    }

    function closeLogoutModal() {
        let logoutModal = document.getElementById('logoutModal')
        logoutModal.classList.add('fade-out');

        // Wait for the animation to complete
        logoutModal.addEventListener('animationend', () => {
            logoutModal.classList.add('hidden');  // Add hidden class after animation ends
            logoutModal.classList.remove('fade-out'); // Optional: Remove fade-out class to reset
        }, { once: true });
    }
</script>