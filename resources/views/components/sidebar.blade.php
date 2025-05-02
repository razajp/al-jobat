<!-- Logout Modal -->
<div id="logoutModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-[var(--overlay-color)] text-xs md:text-sm fade-in">
    <!-- Modal Content -->
    <div class="bg-[var(--secondary-bg-color)] rounded-xl shadow-lg w-80 md:w-full md:max-w-lg p-6 relative">
        <!-- Close Button -->
        <button onclick="closeLogoutModal()"
            class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-all 0.3s ease-in-out cursor-pointer">
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
                class="px-4 py-2 bg-[var(--secondary-bg-color)] border text-[var(--secondary-text)] rounded-md hover:bg-[var(--bg-color)] transition-all 0.3s ease-in-out cursor-pointer">Cancel</button>

            <!-- Logout Form -->
            <form id="logoutForm" method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="px-4 py-2 bg-[var(--danger-color)] text-white rounded-md hover:bg-[var(--h-danger-color)] transition-all 0.3s ease-in-out cursor-pointer">Logout</button>
            </form>
        </div>
    </div>
</div>
<div class="relative w-full md:w-auto md:z-40">
    <aside class="bg-[var(--secondary-bg-color)] w-full md:w-16 flex justify-between md:flex-col items-center px-5 py-3 md:px-0 md:py-3 h-full md:h-screen transition-all 0.3s ease-in-out fade-in relative z-40">
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
        <nav class="space-y-4 hidden md:flex flex-col">
            <div class="relative group">
                <x-nav-link-item 
                    label="Home" 
                    icon="fas fa-home"
                    href="/"
                />
            </div>
            
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
            
            <div class="relative group">
                <x-nav-link-item 
                    label="Suppliers" 
                    icon="fas fa-truck"
                    includesDropdown
                    :items="[
                        ['type' => 'link', 'href' => route('suppliers.index'), 'label' => 'Show Suppliers'],
                        ['type' => 'link', 'href' => route('suppliers.create'), 'label' => 'Add Supplier'],
                    ]"
                />
            </div>
            
            <div class="relative group">
                <x-nav-link-item 
                    label="Customers" 
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
                                ['type' => 'link', 'href' => route('payments.index'), 'label' => 'Show Payments'],
                                ['type' => 'link', 'href' => route('payments.create'), 'label' => 'Add Payment'],
                            ]
                        ]
                    ]"
                />
            </div>
            
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
        </nav>
    
        <div class="relative hidden md:flex group md:pt-3 md:ml-0 md:mt-auto dropdown-trigger">
            <!-- User Avatar -->
            <button type="button" class="w-10 h-10 ml-1.5 mb-1 flex items-center justify-center rounded-full cursor-pointer transition-all 0.3s ease-in-out text-[var(--text-color)] font-semibold text-lg overflow-hidden">
                @if (Auth::user()->profile_picture == 'default_avatar.png')
                    <img src="{{ asset('images/default_avatar.png') }}" class="w-full h-full object-cover" alt="Avatar">
                @else
                    <img src="{{ asset('storage/uploads/images/' . auth()->user()->profile_picture) }}" class="w-full h-full object-cover" alt="Avatar">
                @endif
                <span
                    class="absolute shadow-xl capitalize left-18 bottom-1.5 bg-[var(--h-secondary-bg-color)] text-[var(--text-color)] border border-gray-600 text-sm rounded-lg px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity 0.3s pointer-events-none">
                    {{ Auth::user()->name }}
                </span>
            </button>
    
            <!-- Dropdown Menu -->
            <div class="dropdownMenu text-sm absolute bottom-0 left-16 hidden border border-gray-600 w-48 bg-[var(--h-secondary-bg-color)] text-[var(--text-color)] shadow-lg rounded-2xl opacity-0 transform scale-95 transition-all 0.3s ease-in-out z-50">
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
                        ['href' => route('payments.index'), 'title' => 'Show Payments'],
                        ['href' => route('payments.create'), 'title' => 'Add Payment'],
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
                            <img src="{{ asset('images/default_avatar.png') }}" alt="Avatar" class="w-10 h-10 rounded-full">
                        @else
                            <img src="{{ asset('storage/uploads/images/' . auth()->user()->profile_picture) }}" alt="Avatar" class="w-10 h-10 rounded-full">
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