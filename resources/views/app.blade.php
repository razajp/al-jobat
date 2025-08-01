<!DOCTYPE html>
<html lang="en" data-theme="{{ Auth::check() ? Auth::user()->theme : (isset($_COOKIE['theme']) ? $_COOKIE['theme'] : (request()->cookie('theme') ?? (strpos($_SERVER['HTTP_USER_AGENT'], 'Dark') !== false ? 'dark' : 'light'))) }}">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#2563eb">
    <meta name="description" content="Al Jobat`s Garments Busniess Management Solution!">
    <link rel="manifest" href="/manifest.json">
    <title>@yield('title', app('company')->name)</title>
    <style>
        @font-face {
            font-family: 'Calibri';
            src: url('/calibri.ttf') format('truetype'); /* For TTF */
            font-weight: normal;
            font-style: normal;
        }

        /* color theme */
        :root {
            --bg-color: #111827;
            /* Default dark theme background */
            --h-bg-color: #374151;
            --secondary-bg-color: #1f2937;
            --h-secondary-bg-color: hsl(215, 28%, 13%);
            /* Default dark theme secondary background */
            --text-color: #ffffff;
            /* Default dark theme text color */
            --secondary-text: #d1d5db;
            /* Default dark theme secondary text */
            --primary-color: #2563eb;
            --h-primary-color: #1f56cd;
            /* Default dark theme primary color */
            --bg-warning: hsl(45, 50%, 30%);
            --bg-success: hsl(130, 50%, 30%);
            --bg-error: hsl(360, 50%, 30%);
            --border-warning: hsl(45, 100%, 45%);
            --border-success: hsl(130, 100%, 45%);
            --border-error: hsl(360, 100%, 45%);
            --text-warning: hsl(45, 30%, 95%);
            --text-success: hsl(130, 30%, 95%);
            --text-error: hsl(360, 30%, 95%);

            --h-bg-warning: hsl(45, 50%, 20%);
            --h-bg-success: hsl(130, 50%, 20%);
            --h-bg-error: hsl(360, 50%, 20%);

            --danger-color: hsl(0, 65%, 51%);
            --h-danger-color: hsl(0, 65%, 41%);
            --success-color: hsl(142, 65%, 36%);
            --h-success-color: hsl(142, 65%, 26%);

            --overlay-color: rgba(0, 0, 0, 0.438);
            --glass-border-color: #ffffff;
        }

        [data-theme='light'] {
            --bg-color: #ffffff;
            --h-bg-color: #d1d3d7;
            --secondary-bg-color: #eef0f2;
            --h-secondary-bg-color: hsl(0, 0%, 96%);
            --text-color: #1f2937;
            --secondary-text: #4b5563;
            --bg-warning: hsl(45, 100%, 80%);
            --bg-success: hsl(130, 100%, 80%);
            --bg-error: hsl(360, 100%, 80%);
            --h-bg-warning: hsl(45, 100%, 75%);
            --h-bg-success: hsl(130, 100%, 75%);
            --h-bg-error: hsl(360, 100%, 75%);
            --border-warning: hsl(45, 100%, 40%);
            --border-success: hsl(130, 100%, 40%);
            --border-error: hsl(360, 100%, 40%);
            --text-warning: hsl(45, 75%, 35%);
            --text-success: hsl(130, 75%, 35%);
            --text-error: hsl(360, 75%, 35%);
            --glass-border-color: #000000;
        }

        [data-theme="dark"] input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
        }

        .bg-\[var\(--primary-color\)\]{
            color: #e2e8f0 !important;
        }

        .my-scrollbar-2::-webkit-scrollbar {
            display: none;
        }

        .fade-in {
            animation: fadeIn 0.35s ease-in-out;
        }

        .scale-in {
            animation: scaleIn 0.4s ease-in-out;
        }

        .scale-out {
            animation: scaleOut 0.4s ease-in-out;
        }

        /* Example animation */
        @keyframes fadeIn {
            0% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            0% {
                opacity: 1;
            }

            100% {
                opacity: 0;
            }
        }

        @keyframes scaleIn {
            0% {
                transform: scale(0.9);
            }
            60% {
                transform: scale(1.05);
            }
            80% {
                transform: scale(0.97);
            }
            100% {
                transform: scale(1);
            }
        }

        @keyframes scaleOut {
            0% {
                transform: scale(1);
            }
            30% {
                transform: scale(1.05);
            }
            60% {
                transform: scale(0.95);
            }
            100% {
                transform: scale(0);
            }
        }

        .fade-out {
            animation: fadeOut 0.35s forwards !important;
        }

        .opacity-zero {
            opacity: 0;
        }

        .opacity-transition {
            transition: opacity .2s linear;
        }

        .card {
            transition: all 0.3s ease-in-out;
            position: relative;
        }

        .card:hover {
            transform: translateY(-0.3rem);
            background-color: var(--h-secondary-bg-color);
            box-shadow: 0 5px 0.8rem var(--bg-color);
        }

        .card button {
            transition: all 0.2s ease-in-out;
        }

        .card:hover button {
            scale: 1.1;
        }

        .active_inactive_dot {
            opacity: 100;
            transition: all 0.2s ease-in-out;
        }

        .active_inactive {
            opacity: 0;
            transition: all 0.2s ease-in-out;
        }

        .card:hover .active_inactive {
            opacity: 100;
        }

        .card:hover .active_inactive_dot {
            opacity: 0;
        }

        .nav-link.active {
            background-color: var(--h-bg-color) !important;
        }

        .nav-link.active i {
            color: var(--primary-color) !important;
        }

        .nav-link.active svg {
            fill: var(--primary-color) !important;
        }

        .nav-link.active:hover i {
            color: var(--h-primary-color) !important;
        }

        .nav-link.active:hover svg {
            fill: var(--h-primary-color) !important;
        }

        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type="number"] {
            -moz-appearance: textfield;
            /* For Firefox */
        }

        input[disabled] {
            cursor: not-allowed;
        }

        select[disabled] {
            cursor: not-allowed;
        }

        input::-webkit-calendar-picker-indicator {
            display: none !important;
            -webkit-appearance: none;
        }

        strong {
            font-weight: 600 !important;
        }

        span {
            color: var(--secondary-text) !important;
        }

        .open-dropdown:hover .open-dropdown-hover\:block {
            display: block;
        }

        input.row-checkbox:checked + input {
            opacity: 1 !important;
            pointer-events: all !important;
        }
    </style>

    @vite('resources/css/app.css')

    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/service-worker.js')
            .then(reg => console.log('Service Worker registered ✔️', reg))
            .catch(err => console.warn('Service Worker registration failed ❌', err));
        }
    </script>
    <script src="{{ asset('jquery.js') }}"></script>
    <script src="{{ asset('js/validate-inputs.js') }}"></script>
    <script>
        let closeOnClickOutside;
        let escToClose;
        let enterToSubmit;

        function formatDate(date) {
            const inputDate = new Date(date);

            const day = inputDate.getDate().toString().padStart(2, '0');
            const month = inputDate.toLocaleString('en-US', { month: 'short' });
            const year = inputDate.getFullYear();
            const weekday = inputDate.toLocaleString('en-US', { weekday: 'short' });

            const formatted = `${day}-${month}-${year} ${weekday}`;
            return formatted;
        }

        function formatNumbersDigitLess(number) {
            number = Number(number);
            let formatted = new Intl.NumberFormat('en-US').format(number);
            return formatted;
        }

        function formatNumbersWithDigits(number, maxFraction, minFraction) {
            number = Number(number);
            let formatted = new Intl.NumberFormat('en-US', {
                maximumFractionDigits: maxFraction,
                minimumFractionDigits: minFraction
            }).format(number);
            return formatted;
        }
    </script>

    <script src="{{ asset('js/components/card.js') }}"></script>
    <script src="{{ asset('js/components/modal.js') }}"></script>
    <script src="{{ asset('js/components/context-menu.js') }}"></script>
</head>

<body class="bg-[var(--secondary-bg-color)] text-[var(--text-color)] text-sm min-h-screen flex flex-col md:flex-row items-center justify-center fade-in" cz-shortcut-listen="true">
    {{-- side bar --}}
    @if (Auth::check())
        <script>
            const url = window.location.href; // Get the current URL
        </script>

        @component('components.sidebar')
        @endcomponent
    @endif

    <!-- Loader -->
    <div id="page-loader" class="fixed inset-0 z-[999] bg-[var(--overlay-color)] bg-opacity-80 flex items-center justify-center hidden">
        <div class="w-12 h-12 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
    </div>
    <div class="wrapper flex-1 flex flex-col md:h-screen relative w-full overflow-y-hidden">
        {{-- main content --}}
        <main class="flex-1 px-8 py-0 md:p-8 overflow-y-auto my-scrollbar-2 flex items-center justify-center bg-[var(--bg-color)] rounded-3xl mx-2.5 md:mr-2.5 {{ request()->is('login') ? 'mt-2.5 md:ml-2.5' : 'mt-0 md:ml-0' }} md:mt-2.5 relative">
            {{-- alert --}}
            <div id="messageBox" class="absolute top-5 mx-auto flex items-center flex-col space-y-3 z-[100] text-sm w-full select-none pointer-events-none">
                @if (session('info'))
                    <x-alert type="info" :messages="session('info')" />
                @endif

                @if (session('success'))
                    <x-alert type="success" :messages="session('success')" />
                @endif

                @if (session('warning'))
                    <x-alert type="warning" :messages="session('warning')" />
                @endif

                @if (session('error'))
                    <x-alert type="error" :messages="session('error')" />
                @endif
            </div>
            <!-- Notification Box -->
            <div id="notificationBox" class="absolute top-5 right-5 flex flex-col space-y-3 z-[100] text-sm mx-auto items-end w-full select-none">
                {{-- <x-notification
                    title="Payment Method Expiring"
                    message="Your card ending in 1122 is expiring soon. Please update your billing info."
                    actionLabel="Update Card"
                    actionUrl="/billing"
                />
                <x-notification
                    title="Payment Method Expiring"
                    message="Your card ending in 1122 is expiring soon. Please update your billing info."
                /> --}}
            </div>
            <div class="left_actions absolute top-5 left-5 flex gap-2">
                <div id="go_back_button" class="border border-gray-600 group bg-[var(--bg-color)] h-full rounded-xl cursor-pointer flex items-center justify-end p-1 overflow-hidden hover:pr-3 transition-all duration-300 ease-in-out">
                    <div class="flex items-center justify-center bg-[var(--h-bg-color)] rounded-lg p-2">
                        <svg class="size-3 transition-all duration-300 ease-in-out group-hover:size-2.5 fill-[var(--secondary-text)]"
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                        <path d="M19 12H5m6-6l-6 6 6 6" stroke="currentColor" stroke-width="2.5" fill="none"/>
                        </svg>
                    </div>
                    <span class="inline-block max-w-0 opacity-0 overflow-hidden whitespace-nowrap transition-all duration-300 ease-in-out group-hover:opacity-100 group-hover:max-w-[200px] group-hover:ml-2">
                        Go Back
                    </span>
                </div>
                <div id="refresh_button" class="border border-gray-600 group bg-[var(--bg-color)] h-full rounded-xl cursor-pointer flex items-center justify-end p-1 overflow-hidden hover:pr-3 transition-all duration-300 ease-in-out">
                    <div class="flex items-center justify-center bg-[var(--h-bg-color)] rounded-lg p-2">
                        <svg class="size-3 transition-all duration-300 ease-in-out group-hover:size-2.5 fill-[var(--secondary-text)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                            <g>
                              <path d="M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6s-2.69 6-6 6-6-2.69-6-6H4c0 4.42 3.58 8 8 8s8-3.58 8-8-3.58-8-8-8z"/>
                              <path d="M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6s-2.69 6-6 6-6-2.69-6-6H4c0 4.42 3.58 8 8 8s8-3.58 8-8-3.58-8-8-8z" transform="translate(0.3, 0.3)" />
                            </g>
                        </svg>
                    </div>
                    <span class="inline-block max-w-0 opacity-0 overflow-hidden whitespace-nowrap transition-all duration-300 ease-in-out group-hover:opacity-100 group-hover:max-w-[200px] group-hover:ml-2">
                        Refersh
                    </span>
                </div>
            </div>
            <div class="main-child grow">
                @yield('content')
            </div>
        </main>

        {{-- footer --}}
        @component('components.footer')
        @endcomponent
    </div>

    <script>
        // go back and refresh script
        if (window.history.length > 1) {
            document.getElementById('go_back_button').classList.remove('hidden');
            document.getElementById('go_back_button').addEventListener('click', () => {
            window.history.back();
        })
        } else {
            document.getElementById('go_back_button').classList.add('hidden')
        }
        document.getElementById('refresh_button').addEventListener('click', () => {
            location.reload();
        })
        function checkMax(input) {
            input.value = input.value.replace(/\D/g, '');

            let errorElem = document.getElementById(input.id+"-error");

            const max = parseInt(input.max, 10);
            if (parseInt(input.value, 10) > max) {
                errorElem.textContent = `Value cannot exceed ${max}.`;
                if (errorElem.classList.contains("hidden")) {
                    errorElem.classList.remove("hidden");
                }

                input.value = max;
            } else {
                errorElem.textContent = ``;
                if (!errorElem.classList.contains("hidden")) {
                    errorElem.classList.add("hidden");
                }
            }
        }

        // Message box animation
        function messageBoxAnimation() {
            setTimeout(function() {
                // Select all alert messages by their common class
                const messages = document.querySelectorAll('.alert-message');

                messages.forEach((message) => {
                    if (message) {
                        message.classList.add('fade-out');
                        message.addEventListener('animationend', () => {
                            message.style.display = 'none'; // Hide the element after animation
                        });
                    }
                });
            }, 5000); // Trigger fade-out after 5 seconds
        }
        messageBoxAnimation();

        // notification box animation
        function hideNotification(notificationElem) {
            notificationElem.classList.add('fade-out');

            notificationElem.addEventListener('animationend', () => {
                notificationElem.style.display = 'none';
                notificationElem.remove();
            });
        }

        function openDropDown(e, trigger) {
            e.stopPropagation();

            const relatedDropDownMenu = trigger.nextElementSibling;

            if (relatedDropDownMenu.classList.contains('hidden')) {
                closeAllDropdowns(relatedDropDownMenu); // Pass the one to skip

                relatedDropDownMenu.classList.remove('hidden');
                setTimeout(() => {
                    relatedDropDownMenu.classList.add('opacity-100', "scale-in");
                    relatedDropDownMenu.classList.remove('opacity-0', "scale-out");
                }, 10);
            } else {
                relatedDropDownMenu.classList.remove('opacity-100', "scale-in");
                relatedDropDownMenu.classList.add('opacity-0', "scale-out");
                setTimeout(() => {
                    relatedDropDownMenu.classList.add('hidden');
                }, 300);
            }
        }

        function closeAllDropdowns(skipElement = null) {
            const dropdownMenus = document.querySelectorAll('.dropdownMenu');

            dropdownMenus.forEach(menu => {
                if (menu === skipElement) return; // Skip the one we want to keep open

                menu.classList.remove('opacity-100', 'scale-in');
                menu.classList.add('opacity-0', 'scale-out');
                setTimeout(() => {
                    menu.classList.add('hidden');
                }, 300);
            });
        }

        document.addEventListener('focus', function(event) {
            if (event.target.matches('input[type="date"]')) {
                event.target.showPicker(); // Trigger the date picker
            } else if (event.target.matches('input[type="month"]')) {
                event.target.showPicker(); // Trigger the date picker
            }
        }, true); // Use capturing phase

        const previewImage = (event) => {
            const file = event.target.files[0];
            const placeholderIcon = document.querySelector(".placeholder_icon");
            const uploadText = document.querySelector(".upload_text");

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    placeholderIcon.src = e.target.result;
                    placeholderIcon.classList.add("rounded-md", "w-full", "h-auto");
                    uploadText.textContent = "Preview";
                }
                reader.readAsDataURL(file);
            }
        }

        document.addEventListener("contextmenu", e => e.preventDefault());

        // Function to send AJAX request to update last_activity
        function updateLastActivity() {
            $.ajax({
                url: '/update-last-activity',
                type: 'POST',
                data: {}, // Optional if you want to send any data, can be left empty
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status === 'updated') {
                        console.log("Last activity updated successfully.");
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Failed to update last activity", error);
                }
            });
        }

        // Call the function immediately once
        updateLastActivity();

        // Then every 60 minutes (3600000 milliseconds)
        setInterval(updateLastActivity, 60 * 60 * 1000);
    </script>

    <script>
        var pusher = new Pusher('c99f4e2f9df04cc306f4', {
            cluster: 'ap2',
            forceTLS: true
        });

        var channel = pusher.subscribe('notifications');

        // Utility function to create and show notification
        function showNotification(title = '', message = '') {
            const notificationBox = document.getElementById("notificationBox");
            if (!notificationBox) return;

            const wrapper = document.createElement("div");
            wrapper.innerHTML = `
                <x-notification
                    title="${title}"
                    message="${message}"
                />
            `;
            const notificationElement = wrapper.firstElementChild;
            notificationBox.prepend(notificationElement);

            setTimeout(() => hideNotification(notificationElement), 5000);
        }

        // Listen to the event
        channel.bind('App\\Events\\NewNotificationEvent', function (data) {
            console.log('📢 Notification received:', data);

            const dataObject = data.data;

            @if(request()->is('orders/create'))
                if (dataObject.title === "New Article Added.") {
                    const dateInput = document.querySelector("#date");

                    if (dateInput?.value) {
                        getDataByDate(dateInput);
                        showNotification(dataObject.title, dataObject.message);
                    }
                }
            @endif

            @if(!request()->is('login'))
                if (dataObject.title == "User Inactivated" && dataObject.id == {{Auth::user()->id}}) {
                    document.getElementById("logoutForm").submit();
                }
            @endif
        });

        pusher.connection.bind('connected', function() {
            console.log('✅ Pusher connected');
        });
    </script>
</body>
<script>
    let doHide = false;
    window.addEventListener('beforeunload', function () {
        showLoader();
    });

    function showLoader() {
        document.getElementById('page-loader').classList.remove('hidden');
        document.getElementById('page-loader').classList.remove('fade-out');
        document.getElementById('page-loader').classList.add('fade-in');
    }

    function hideLoader() {
        document.getElementById('page-loader').classList.add('hidden');
        document.getElementById('page-loader').classList.add('fade-out');
        document.getElementById('page-loader').classList.remove('fade-in');
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Loader for normal <a> clicks
        document.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                const target = this.getAttribute('target');

                if (
                    href &&
                    !href.startsWith('#') &&
                    !href.startsWith('javascript:') &&
                    !target
                ) {
                    showLoader();
                }
            });
        });

        // Loader for all form submissions
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function () {
                showLoader();
            });
        });
    });

    // Hide loader on full page load (in case of back/refresh)
    window.addEventListener('load', function () {
        hideLoader();
    });

    // ======================
    // For AJAX Requests
    // ======================

    // If using Axios
    if (typeof axios !== 'undefined') {
        axios.interceptors.request.use(config => {
            showLoader();
            return config;
        }, error => {
            hideLoader();
            return Promise.reject(error);

        });

        axios.interceptors.response.use(response => {
            hideLoader();
            return response;
        }, error => {
            hideLoader();
            return Promise.reject(error);
        });
    }

    // If using jQuery
    if (typeof $ !== 'undefined') {
        $(document).ajaxStart(function () {
            showLoader();
        }).ajaxStop(function () {
            if (!doHide) {
                hideLoader();
            }
            doHide = false;
        });
    }

    // its for cache clear
    // Unregister service worker and clear caches
    // if ('serviceWorker' in navigator) {
    // navigator.serviceWorker.getRegistrations().then(registrations => {
    //     for (let registration of registrations) {
    //     registration.unregister();
    //     }
    // });

    // caches.keys().then(function(names) {
    //     for (let name of names) {
    //     caches.delete(name);
    //     }
    // }).then(() => {
    //     console.log('Service Worker and Cache cleared!');
    //     window.location.reload(); // Optional: reload the page
    // });
    // }

    // const items = document.querySelectorAll(".search_container > div");
    // function filterByText(inputValue, jsonPath) {
    //     inputValue = inputValue.toLowerCase().trim();

    //     items.forEach(item => {
    //         const jsonData = item.getAttribute("data-json");
    //         if (!jsonData) return;

    //         const parsed = JSON.parse(jsonData);
    //         const fieldValue = getNestedValue(parsed, jsonPath)?.toString().toLowerCase() || "";

    //         const match = fieldValue.includes(inputValue);
    //         item.style.display = match ? "" : "none";
    //     });

    //     const noItemsError = document.getElementById("noItemsError");
    //     noItemsError.style.display = Array.from(items).every(item => item.style.display === "none") ? "block" : "none";
    // }

    // function filterBySelect(inputValue, jsonPath) {
    //     inputValue = inputValue.toLowerCase().trim();

    //     items.forEach(item => {
    //         const jsonData = item.getAttribute("data-json");
    //         if (!jsonData) return;

    //         const parsed = JSON.parse(jsonData);
    //         const fieldValue = getNestedValue(parsed, jsonPath)?.toString().toLowerCase() || "";

    //         const match = fieldValue == inputValue;
    //         item.style.display = match ? "" : "none";
    //     });

    //     const noItemsError = document.getElementById("noItemsError");
    //     noItemsError.style.display = Array.from(items).every(item => item.style.display === "none") ? "block" : "none";
    // }

    // function getNestedValue(obj, path) {
    //     return path.split('.').reduce((acc, part) => acc?.[part], obj);
    // }

    @if(request()->route()->getActionMethod() === 'index' || request()->is('invoices/create'))
        let search_container = document.querySelector('.search_container');
        let tableHead = document.getElementById('table-head');

        function renderFilteredData() {
            // Check if authLayout is defined
            const isGrid = typeof authLayout !== 'undefined' && authLayout === "grid";

            if (isGrid) {
                tableHead.classList.add("hidden");
                search_container.classList = "search_container grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 pt-4 px-2 overflow-y-auto grow my-scrollbar-2 pb-4";
            } else {
                tableHead.classList.remove("hidden");
                search_container.classList = "search_container overflow-y-auto grow my-scrollbar-2 mx-2 mb-3 pb-4";
            }

            search_container.innerHTML = "";

            @if(request()->route()->getActionMethod() === 'index')
                const html = newlyFilteredData
                    .filter(item => item.visible === true)
                    .map(item => isGrid ? createCard(item) : createRow(item))
                    .join('');

                search_container.insertAdjacentHTML('beforeend', html);
            @elseif(request()->is('invoices/create'))

            @endif
        }

        function getNestedValue(obj, path) {
            const parts = path.split('.');

            function resolve(current, parts) {
                for (let i = 0; i < parts.length; i++) {
                    const part = parts[i];

                    if (part.endsWith('[]')) {
                        const key = part.slice(0, -2);
                        const arr = current?.[key];
                        if (!Array.isArray(arr)) return [];

                        const remainingPath = parts.slice(i + 1).join('.');
                        return arr.map(item => resolve(item, remainingPath.split('.')));
                    }

                    current = current?.[part];
                    if (current === undefined) return undefined;
                }
                return current;
            }

            return resolve(obj, parts);
        }

        let newlyFilteredData = [];
        function runDynamicFilter() {
            newlyFilteredData = [];
            const filters = document.querySelectorAll('[data-filter-path]');
            const noItemsError = document.getElementById("noItemsError");

            // Group filters by path
            const filterGroups = {};

            filters.forEach(filter => {
                const path = filter.dataset.filterPath;

                if (!filterGroups[path]) filterGroups[path] = [];
                filterGroups[path].push(filter);
            });

            allDataArray.forEach(item => {
                let tempItem = item;
                let visible = true;

                for (const path in filterGroups) {
                    const group = filterGroups[path];

                    const rawVal = getNestedValue(tempItem, path);
                    const jsonVal = Array.isArray(rawVal) ? rawVal : (rawVal || "").toString().toLowerCase();

                    // Handle date range
                    if (group.length === 2 && group[0].type === "date" && group[1].type === "date") {
                        const startInput = group.find(i => i.id.includes("start"));
                        const endInput = group.find(i => i.id.includes("end"));

                        const startVal = startInput?.value;
                        const endVal = endInput?.value;

                        const jsonDate = new Date(jsonVal);

                        if (startVal) {
                            const startDate = new Date(startVal);
                            if (jsonDate < startDate) visible = false;
                        }

                        if (endVal) {
                            const endDate = new Date(endVal);
                            if (jsonDate > endDate) visible = false;
                        }
                    } else {
                        for (const input of group) {
                            const value = (input.value || "").trim().toLowerCase();

                            if (!value) continue;

                            if (input.type === "text" || input.type === "hidden") {
                                if (input.classList.contains("dbInput") && !Array.isArray(jsonVal)) {
                                    jsonVal != value ? visible = false : '';
                                } else if (!jsonVal.includes(value)) visible = false;
                            } else if (input.type === "select-one") {
                                if (jsonVal !== value) visible = false;
                            } else if (input.type === "number") {
                                const inputNumber = Number(value);
                                const jsonNumber = Number(jsonVal);

                                if (isNaN(jsonNumber) || jsonNumber !== inputNumber) visible = false;
                            } else if (input.type === "date") {
                                const inputDate = new Date(value);
                                const jsonDate = new Date(jsonVal);

                                if (input.id.includes("start") && jsonDate < inputDate) visible = false;
                                if (input.id.includes("end") && jsonDate > inputDate) visible = false;
                            }
                        }
                    }

                    if (!visible) break;
                }

                tempItem.visible = visible;

                newlyFilteredData.push(tempItem);
            });
            renderFilteredData();

            noItemsError.style.display = allDataArray.every(i => i.visible == false) ? "block" : "none";
        }

        function debounce(func, delay = 300) {
            let timeout;
            return function (...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), delay);
            };
        }

        const debouncedFilter = debounce(runDynamicFilter, 300);

        function setSearchDebounce() {
            document.querySelectorAll('[data-filter-path]').forEach(input => {
                const eventType = (input.classList.contains("dbInput") || input.type === 'date') ? 'change' : 'input';
                input.addEventListener(eventType, debouncedFilter);
            });
        }
        setSearchDebounce();

        function clearAllSearchFields() {
            document.querySelectorAll('[data-clearable]').forEach(searchField => {
                searchField.value = "";
                debouncedFilter();
            })
        }
    @endif

    @if(request()->route()->getActionMethod() === 'index')
        // change layout
        function changeLayout() {
            $.ajax({
                url: "{{ route('change-data-layout') }}",
                type: 'POST',
                data: {
                    layout: authLayout,
                }, // Optional if you want to send any data, can be left empty
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status === 'updated') {
                        console.log("Layout Updated Successfully.");
                        authLayout = response.updatedLayout;
                        console.log(authLayout);

                        clearAllSearchFields();
                        renderData();

                        const changeLayoutBtn = document.getElementById('changeLayoutBtn');
                        if (response.updatedLayout == "grid") {
                            changeLayoutBtn.innerHTML = `
                                <i class='fas fa-list-ul text-white'></i>
                                <span class="absolute shadow-xl -right-2 top-7.5 z-10 bg-[var(--h-secondary-bg-color)] border border-gray-600 text-[var(--text-color)] text-xs rounded-lg px-2.5 py-1 opacity-0 group-hover:opacity-100 transition-all duration-300 pointer-events-none text-nowrap">List</span>
                            `;
                        } else {
                            changeLayoutBtn.innerHTML = `
                                <i class='fas fa-grip text-white'></i>
                                <span class="absolute shadow-xl -right-2 top-7.5 z-10 bg-[var(--h-secondary-bg-color)] border border-gray-600 text-[var(--text-color)] text-xs rounded-lg px-2.5 py-1 opacity-0 group-hover:opacity-100 transition-all duration-300 pointer-events-none text-nowrap">Grid</span>
                            `;
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Failed to update Layout", error);
                }
            });
        }

        const scroller = search_container;
        const batchSize = 50;
        let startIndex = 0;
        let isFetching = false;

        function renderNextBatch() {
            if (startIndex >= allDataArray.length) return;

            const nextChunk = allDataArray.slice(startIndex, startIndex + batchSize);
            const html = nextChunk
                .filter(item => item.visible === true)
                .map(item => authLayout === 'grid' ? createCard(item) : createRow(item))
                .join('');
            search_container.insertAdjacentHTML('beforeend', html);
            startIndex += batchSize;
        }

        scroller?.addEventListener('scroll', () => {
            const scrollTop = scroller.scrollTop;
            const scrollHeight = scroller.scrollHeight;
            const clientHeight = scroller.clientHeight;

            if (scrollTop + clientHeight >= scrollHeight - 100 && !isFetching) {
                isFetching = true;
                setTimeout(() => {
                    console.log("Render Next Batch");

                    renderNextBatch();
                    isFetching = false;
                }, 100);
            }
        });

        function renderData() {
            if (authLayout == "grid") {
                tableHead.classList.add("hidden");
                search_container.classList = "search_container grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 pt-4 px-2 overflow-y-auto grow my-scrollbar-2 pb-4";
            } else {
                tableHead.classList.remove("hidden");
                search_container.classList = "search_container overflow-y-auto grow my-scrollbar-2 mx-2 mb-3 pb-4";
            }
            search_container.innerHTML = "";
            startIndex = 0;
            renderNextBatch();
        }

        renderData(); // initial load
    @endif

    function closeModal(modalId, animate = 'animate') {
        const modal = document.getElementById(`${modalId}-wrapper`);
        const modalForm = modal.querySelector('form');

        if (animate == 'animate') {
            modalForm.classList.add('scale-out');

            modalForm.addEventListener('animationend', () => {
                modal.classList.add('fade-out');

                modal.addEventListener('animationend', () => {
                    modal.remove();
                }, { once: true });
            }, { once: true });
        } else {
            modal.remove();
        }
        document.removeEventListener('mousedown', closeOnClickOutside);
        document.removeEventListener('keydown', escToClose);
        document.removeEventListener('keydown', enterToSubmit);
    }

    function selectThisOption(optionLiElem) {
        const forId = optionLiElem.dataset.for;

        const selectSearch = document.getElementById(forId);
        const dbInput = document.querySelector(`.dbInput[data-for="${forId}"]`);

        selectSearch.value = optionLiElem.textContent.trim();
        dbInput.value = optionLiElem.dataset.value;

        // Remove 'selected' from all
        const allOptions = document.querySelectorAll(`.optionsDropdown li[data-for="${forId}"]`);
        allOptions.forEach(li => li.classList.remove('selected'));

        // Add 'selected' to current
        optionLiElem.classList.add('selected');

        // Trigger change event manually on the hidden input
        const changeEvent = new Event('change', { bubbles: true });
        dbInput.dispatchEvent(changeEvent);

        // Optional: hide dropdown if needed
        searchSelect(selectSearch);
    }

    function searchSelect(selectSearchInput) {
        const inputValue = selectSearchInput.value.toLowerCase().trim();
        const forId = selectSearchInput.id;

        const allOptions = document.querySelectorAll(`.optionsDropdown li[data-for="${forId}"]`);

        const isDefaultSelection = inputValue.startsWith('-- select');

        allOptions.forEach((li) => {
            const optionText = li.textContent.toLowerCase().trim();

            // Always show "-- Select Customer --"
            if (optionText.startsWith('-- select')) {
                li.classList.remove('hidden');
                li.innerHTML = li.textContent; // Remove highlight
                return;
            }

            // If default option is selected, show everything
            if (isDefaultSelection) {
                li.classList.remove('hidden');
                li.innerHTML = li.textContent; // Remove highlight
                return;
            }

            // Otherwise filter based on input value
            if (optionText.includes(inputValue) && inputValue.length > 0) {
                li.classList.remove('hidden');
                // Highlight the matching part
                const originalText = li.textContent;
                const regex = new RegExp(`(${inputValue})`, 'ig');
                li.innerHTML = originalText.replace(regex, '<mark class="bg-yellow-200 text-black rounded">$1</mark>');
            } else if (optionText.includes(inputValue)) {
                li.classList.remove('hidden');
                li.innerHTML = li.textContent; // Remove highlight if input is empty
            } else {
                li.classList.add('hidden');
                li.innerHTML = li.textContent; // Remove highlight
            }
        });
    }

    function validateSelectInput(selectSearchInput) {
        const inputValue = selectSearchInput.value.toLowerCase().trim();
        const forId = selectSearchInput.id;
        const dbInput = document.querySelector(`.dbInput[data-for="${forId}"]`);

        const allOptions = document.querySelectorAll(`.optionsDropdown li[data-for="${forId}"]`);

        let isValid = false;

        allOptions.forEach((li) => {
            const optionText = li.textContent.toLowerCase().trim();
            if (optionText === inputValue) {
                isValid = true;
            }
        });

        if (!isValid) {
            // Clear both fields if no exact match
            selectFirstOption(forId);
            searchSelect(selectSearchInput);
        }
    }

    function selectFirstOption(forId) {
        const firstOption = document.querySelector(`.optionsDropdown li[data-for="${forId}"]:not(.hidden)`);
        if (firstOption) {
            selectThisOption(firstOption);
        }
    }

    document.querySelectorAll(".selectParent .dbInput")
        .forEach(dbInput => selectFirstOption(dbInput.dataset.for));

    function selectClicked(input) {
        input.select();

        const inputRect = input.getBoundingClientRect();
        const dropdown = input.closest(".selectParent").querySelector(".optionsDropdown");

        dropdown.style.width = inputRect.width + "px";
        dropdown.style.top = (inputRect.top + inputRect.height) + "px";
        dropdown.style.left = inputRect.left + "px";
    }

    function selectKeyDown(event, input) {
        const dropdown = input.closest(".selectParent").querySelector(".optionsDropdown");
        const allOptions = dropdown.querySelectorAll("li");
        const options = Array.from(allOptions).filter(li => !li.classList.contains("hidden"));

        function scrollIntoViewIfNeeded(element) {
            if (element && typeof element.scrollIntoView === "function") {
                element.scrollIntoView({ block: "nearest", inline: "nearest" });
            }
        }

        if (event.key === "ArrowDown") {
            event.preventDefault();
            const selected = dropdown.querySelector("li.selected:not(.hidden)");
            let next = selected
                ? options[options.indexOf(selected) + 1]
                : options[0];
            if (next) {
                options.forEach(li => li.classList.remove("selected"));
                next.classList.add("selected");
                input.value = next.textContent.trim();
                scrollIntoViewIfNeeded(next);
            }
        } else if (event.key === "ArrowUp") {
            event.preventDefault();
            const selected = dropdown.querySelector("li.selected:not(.hidden)");
            let prev = selected
                ? options[options.indexOf(selected) - 1]
                : options[options.length - 1];
            if (prev) {
                options.forEach(li => li.classList.remove("selected"));
                prev.classList.add("selected");
                input.value = prev.textContent.trim();
                scrollIntoViewIfNeeded(prev);
            }
        } else if (event.key === "Enter") {
            event.preventDefault();
            const selected = dropdown.querySelector("li.selected:not(.hidden)");
            if (selected) {
                selectThisOption(selected);
                input.blur();
            }
        } else if (event.key === "Escape") {
            input.blur();
        }
    }
</script>

</html>
