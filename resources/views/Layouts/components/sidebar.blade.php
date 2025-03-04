<aside class="bg-[--secondary-bg-color] w-16 flex flex-col items-center py-5 h-screen shadow-lg z-40 transition-all 0.3s ease-in-out">
    <!-- Logo -->
    <a href="#"
        class="mb-6 text-[--text-color] p-3 w-10 h-10 flex items-center justify-center group cursor-normal relative">
        <h1 class="font-bold text-2xl md:text-3xl text-[--primary-color] m-0">AJ</h1>
        <span
            class="absolute text-nowrap shadow-xl left-20 top-1/2 transform -translate-y-1/2 bg-[--secondary-bg-color] text-[--text-color] text-xs md:text-sm rounded-md px-2 py-1 opacity-0 group-hover:opacity-100 transition-all 0.3s ease-in-out pointer-events-none">
            Al Jobat
        </span>
    </a>

    <!-- Navigation Links -->
    <nav class="space-y-6">
        <!-- Home Link -->
        <a href="#"
            class="nav-link home text-[--text-color] p-3 rounded-full hover:bg-[--h-bg-color] transition-all 0.3s ease-in-out w-10 h-10 flex items-center justify-center group relative">
            <i class="fas fa-home group-hover:text-[--primary-color] transition-all 0.3s ease-in-out"></i>
            <span
                class="absolute shadow-xl left-20 top-1/2 transform -translate-y-1/2 bg-[--secondary-bg-color] text-[--text-color] text-xs md:text-sm rounded-md px-2 py-1 opacity-0 group-hover:opacity-100 transition-all 0.3s ease-in-out pointer-events-none">
                Home
            </span>
        </a>
        
        <div class="relative group">
            <!-- Main Button -->
            <a id="trigger1"
                class="nav-link user dropdown-trigger text-[--text-color] p-3 rounded-full group-hover:bg-[--h-bg-color] transition-all 0.3s ease-in-out w-10 h-10 flex items-center justify-center cursor-pointer">
                <i class="fas fa-user group-hover:text-[--primary-color]"></i>
                <span
                    class="absolute shadow-xl left-16 top-1/2 transform -translate-y-1/2 bg-[--secondary-bg-color] text-[--text-color] text-xs rounded-md px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity 0.3s pointer-events-none">Users</span>
            </a>
            <!-- Dropdown Menu -->
            <div id="menu1"
                class="dropdownMenu text-sm absolute top-0 left-16 hidden border border-gray-600 w-48 bg-[--secondary-bg-color] text-[--text-color] shadow-lg rounded-xl opacity-0 transform scale-95 transition-all 0.3s ease-in-out z-50">
                <ul class="p-2">
                    <li>
                        <a href="{{ route('users.index') }}"
                            class="block px-4 py-2 hover:bg-[--h-bg-color] rounded-md transition-all duration-200 ease-in-out">Show
                            Users</a>
                    </li>
                    <li>
                        <a href="{{ route('users.create') }}"
                            class="block px-4 py-2 hover:bg-[--h-bg-color] rounded-md transition-all duration-200 ease-in-out">Add
                            User</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="relative group pt-3 mt-auto">
        <!-- User Avatar -->
        <button id="trigger999"
            class="dropdown-trigger w-10 h-10 flex items-center justify-center rounded-full cursor-pointer border-transparent hover:border-[--primary-color] transition-all 0.3s ease-in-out bg-[--primary-color] text-white font-semibold text-lg overflow-hidden">
            <img src=""
                class="w-full h-full object-cover" alt="Avatar">
            <span
                class="absolute shadow-xl left-16 top-1/2 transform -translate-y-1/2 bg-[--secondary-bg-color] text-[--text-color] text-xs rounded-md px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity 0.3s pointer-events-none">
                Al Jobat
            </span>
        </button>

        <!-- Dropdown Menu -->
        <div id="menu999"
            class="dropdownMenu text-sm absolute bottom-0 left-16 hidden border border-gray-600 w-48 bg-[--secondary-bg-color] text-[--text-color] shadow-lg rounded-xl opacity-0 transform scale-95 transition-all 0.3s ease-in-out z-50">
            <ul class="p-2">
                <!-- Add Setups -->
                <li>
                    <a href="#"
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
                    <button onclick="openModal()"
                        class="block w-full text-left px-4 py-2 text-[--border-error] hover:bg-[--bg-error] hover:text-[--text-error] rounded-md transition-all duration-200 ease-in-out">
                        <i class="fas fa-sign-out-alt mr-3"></i>
                        Logout
                    </button>
                </li>
            </ul>
        </div>
    </div>
</aside>
