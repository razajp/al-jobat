<!-- Logout Modal -->
<div id="logoutModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 text-sm fade-in">
    <!-- Modal Content -->
    <div class="bg-[--secondary-bg-color] rounded-xl shadow-lg w-full max-w-lg p-6 relative">
        <!-- Close Button -->
        <button onclick="closeLogoutModal()"
            class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-all 0.3s ease-in-out">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Modal Body -->
        <div class="modal_body flex items-start">
            <div class="w-1/5 h-1/5">
                <img src="{{ asset('images/error_icon.png') }}" alt=""
                    class="w-full h-full object-cover">
            </div>
            <div class="content ml-5">
                <h2 class="text-xl font-semibold text-[--text-color]">Logout Account</h2>
                <p class="text-sm text-[--secondary-text] mt-2 mb-6">Are you sure you want to logout? All of your data
                    will be permanently removed. This action cannot be undone.</p>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex justify-end space-x-3">
            <!-- Cancel Button -->
            <button onclick="closeLogoutModal()"
                class="px-4 py-2 bg-[--secondary-bg-color] border text-[--secondary-text] rounded-md hover:bg-[--bg-color] transition-all 0.3s ease-in-out">Cancel</button>

            <!-- Logout Form -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="px-4 py-2 bg-[--danger-color] text-white rounded-md hover:bg-[--h-danger-color] transition-all 0.3s ease-in-out">Logout</button>
            </form>
        </div>
    </div>
</div>
<aside class="bg-[--secondary-bg-color] w-16 flex flex-col items-center py-5 h-screen shadow-lg z-40 transition-all 0.3s ease-in-out fade-in">
    <!-- Logo -->
    <a href="/"
        class="mb-6 text-[--text-color] p-3 w-10 h-10 flex items-center justify-center group cursor-normal relative">
        <h1 class="font-bold text-2xl text-[--primary-color] m-0">AJ</h1>
        <span
            class="absolute text-nowrap shadow-xl left-20 top-1/2 transform -translate-y-1/2 bg-[--secondary-bg-color] text-[--text-color] text-xs md:text-sm rounded-md px-2 py-1 opacity-0 group-hover:opacity-100 transition-all 0.3s ease-in-out pointer-events-none">
            Al Jobat
        </span>
    </a>

    <!-- Navigation Links -->
    <nav class="space-y-4">
        <div class="relative group">
            <x-nav-link-item 
                label="Home" 
                icon="fas fa-home"
                href="/"
                includesDropdown="false"
            />
        </div>
        
        <div class="relative group">
            <x-nav-link-item 
                label="Users" 
                icon="fas fa-user"
                includesDropdown="true"
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
                includesDropdown="true"
                :items="[
                    ['type' => 'link', 'href' => route('suppliers.index'), 'label' => 'Show Suppliers'],
                    ['type' => 'link', 'href' => route('suppliers.create'), 'label' => 'Add Supplier'],
                ]"
            />
        </div>
    </nav>

    <div class="relative group pt-3 mt-auto dropdown-trigger">
        <!-- User Avatar -->
        <button class="w-10 h-10 flex items-center justify-center rounded-full cursor-pointer border-transparent hover:border-[--primary-color] transition-all 0.3s ease-in-out bg-[--primary-color] text-white font-semibold text-lg overflow-hidden">
            @if (Auth::user()->profile_picture == 'default_avatar.png')
                <img src="{{ asset('images/default_avatar.png') }}" class="w-full h-full object-cover" alt="Avatar">
            @else
                <img src="{{ asset('storage/uploads/images/' . auth()->user()->profile_picture) }}" class="w-full h-full object-cover" alt="Avatar">
            @endif
            <span
                class="absolute shadow-xl capitalize left-16 bottom-1 bg-[--h-secondary-bg-color] text-[--text-color] border border-gray-600 text-sm rounded-lg px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity 0.3s pointer-events-none">
                {{ Auth::user()->name }}
            </span>
        </button>

        <!-- Dropdown Menu -->
        <div class="dropdownMenu text-sm absolute bottom-0 left-16 hidden border border-gray-600 w-48 bg-[--h-secondary-bg-color] text-[--text-color] shadow-lg rounded-xl opacity-0 transform scale-95 transition-all 0.3s ease-in-out z-50">
            <ul class="p-2">
                <!-- Add Setups -->
                <li>
                    <a href="{{route('addSetup')}}"
                        class="block px-4 py-2 hover:bg-[--h-bg-color] rounded-md transition-all duration-200 ease-in-out">
                        <i class="fas fa-cog text-[--secondary-color] mr-3"></i>
                        Setups
                    </a>
                </li>
                <!-- Theme Toggle -->
                <li>
                    <button id="themeToggle"
                        class="flex items-center w-full px-4 py-2 text-left hover:bg-[--h-bg-color] rounded-md transition-all duration-200 ease-in-out">
                        <i class="fas fa-moon text-[--secondary-color] mr-3"></i>
                        Theme
                    </button>
                </li>
                <!-- Logout Button -->
                <li>
                    <button onclick="openLogoutModal()"
                        class="block w-full text-left px-4 py-2 text-[--border-error] hover:bg-[--bg-error] hover:text-[--text-error] rounded-md transition-all duration-200 ease-in-out">
                        <i class="fas fa-sign-out-alt mr-3"></i>
                        Logout
                    </button>
                </li>
            </ul>
        </div>
    </div>
</aside>
<script>
    const html = document.documentElement;
    const themeIcon = document.querySelector('#themeToggle i');
    const themeToggle = document.getElementById('themeToggle');
    let isLogoutModalOpened = false;
    
    themeToggle?.addEventListener('click', () => {
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
    });

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
        };
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeLogoutModal();
        };
    });

    // Close any open dropdown when clicking anywhere else on the document
    document.addEventListener('click', function(e) {
        // Check if the click is outside of any dropdown trigger or menu
        if (!e.target.closest('.dropdown-trigger') && !e.target.closest('.dropdownMenu')) {
            closeAllDropdowns();
        }
    });

    function openLogoutModal() {
        isLogoutModalOpened = true;
        document.getElementById('logoutModal').classList.remove('hidden');
        closeAllDropdowns();
        closeContextMenu();
    }

    function closeLogoutModal() {
        let logoutModal = document.getElementById('logoutModal')
        logoutModal.classList.add('fade-out');

        // Wait for the animation to complete
        logoutModal.addEventListener('animationend', () => {
            logoutModal.classList.add('hidden');  // Add hidden class after animation ends
            logoutModal.classList.remove('fade-out'); // Optional: Remove fade-out class to reset
        }, { once: true });
        closeContextMenu();
    }
</script>